import TBTC from "@keep-network/tbtc.js"
import { BitcoinHelpers } from "@keep-network/tbtc.js"

import { createRequire } from 'module'
const require = createRequire(import.meta.url)

const Web3 = require("web3")
//const ProviderEngine = require("web3-provider-engine");
//const Subproviders = require("@0x/subproviders");
//const engine = new ProviderEngine({ pollingInterval: 1000 })

const path = require('path');
const __dirname = path.resolve(path.dirname(''));
const fs = require('fs');
const config = require('./config/config.js');
const Utilities = require("./helpers/Utility");
const db = require('./helpers/Db');
const schedule = require('node-schedule');
const util = require('util');
const log_file = fs.createWriteStream(__dirname+'/debug_update.log', {flags : 'w'});
const log_stdout = process.stdout;

console.l = function(d) { log_file.write(util.format(d) + '\n');};

var startBlock = 0;
var currentProvider = 0;
var web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));

var tbtc = await TBTC.withConfig({
	web3: web3,
	bitcoinNetwork: "main",
	electrum: {
	  server: config.electrumx_server,
	  port: config.electrumx_port,
	  protocol: config.electrumx_protocol
	}
});
		
async function upate_item(item){
	try {	
		var depositContract  =  new web3.eth.Contract(config.depositContractABI, item.depositContractAddress);
		var keepContract  =  new web3.eth.Contract(config.keepContractABI, item.keepAddress);
		
		var bitcoinAddress = null
		var transactions = null
		
		var currentState = await depositContract.methods.currentState().call();
		var updating = 1
		
		if(parseInt(currentState)>0){
			if(currentState == 3  || currentState==7 || currentState==11) updating = 0;
			db.connection.query("UPDATE `depositHistory` set updating = "+updating+", currentState="+currentState+" WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
		}	
			
		if(!item.isFunded || !item.bitcoinAddress || !item.bitcoinTransaction){
			if(!item.bitcoinAddress && item._signingGroupPubkeyX && item._signingGroupPubkeyX){
				var publicKeyPoint = {
					x: item._signingGroupPubkeyX,
					y: item._signingGroupPubkeyY
				}
				
				bitcoinAddress =  await publicKeyPointToBitcoinAddress(publicKeyPoint);
				if(bitcoinAddress){
					db.connection.query("UPDATE `depositHistory` set bitcoinAddress = '"+bitcoinAddress+"' WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
				}
			}else if(item.bitcoinAddress) bitcoinAddress=item.bitcoinAddress;
			
			
			if(bitcoinAddress && currentState<=4 && !item.bitcoinTransaction){
				var LotSizeSatoshis = item.lotsize * 100000000
				transactions = await findOrWaitForBitcoinTransaction(bitcoinAddress,LotSizeSatoshis);
				
				if(transactions && transactions.transactionID){
					db.connection.query("UPDATE `depositHistory` set bitcoinTransaction = '"+transactions.transactionID+"' WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
					item.bitcoinTransaction=transactions.transactionID
				}
			}
			
			if(item.bitcoinTransaction && ( !item.bitcoinConfirmations || item.bitcoinConfirmations<6)){
				var confirmations = await getConfirmations(item.bitcoinTransaction);
				if(confirmations && confirmations>0){
					db.connection.query("UPDATE `depositHistory` set bitcoinConfirmations = '"+confirmations+"' WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
				}else if(confirmations<0){
					db.connection.query("UPDATE `depositHistory` set bitcoinTransaction = NULL WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
				}
			}
		}
		
		
		if(!item.keepBond && item.currentState>=2){
			var keepBond = await keepContract.methods.checkBondAmount().call()
			if(keepBond && keepBond>0){
				keepBond= web3.utils.fromWei(keepBond, 'ether')
				db.connection.query("UPDATE `depositHistory` set keepBond = '"+keepBond+"' WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
			}
		}
		
		if(!item.keepMembers){
			var keepMembers = await keepContract.methods.getMembers().call()
			if(keepMembers){
				keepMembers= keepMembers.join(",")
				db.connection.query("UPDATE `depositHistory` set keepMembers = '"+keepMembers+"' WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
			}
		}
		
		if(!item.isMinted){
			db.connection.query("SELECT txhash  FROM  TokenContract WHERE `to`='"+item.depositContractAddress+"'", {}, function(err, data) {
				if(err==null && data[0]) {
					db.connection.query("SELECT *  FROM  TokenContract WHERE `to`!='"+item.depositContractAddress+"' and  txhash='"+data[0].txhash+"'", {}, function(err, r) {
						if(err==null) {
							db.connection.query("UPDATE `depositHistory` set isMinted = 1, isFunded=1, mintedBy='"+r[0].to+"', updating=0 WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
						}
					});
				}
			});
		}else{
			db.connection.query("UPDATE `depositHistory` set updating=0 WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
		}	
		
	} catch (err) {
		console.log(err);
	}
}
 

var j = schedule.scheduleJob('*/2 * * * *', function(){
	try {	
		console.log("start updating - " + Utilities.toMysqlFormat(new Date()));
		web3.eth.getBlockNumber().then(function(block){
			db.connection.query('SELECT * FROM depositHistory WHERE updating=1 LIMIT 20', {}, function(err, result) {
				if(err==null) {
					result.forEach(function(item, index, result) {
						upate_item(item)
					})
				}
			});
		}).catch(err=>{
			console.log(err);
			if(err.code==-32603){
				console.log("change provider");
				if(currentProvider + 1 <=2) currentProvider++; else currentProvider=0
				web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
			}
		});  	
	} catch (err) {
		console.log(err);
	}
});


async function publicKeyPointToBitcoinAddress(publicKeyPoint) {
    return BitcoinHelpers.Address.publicKeyPointToP2WPKHAddress(
      publicKeyPoint.x,
      publicKeyPoint.y,
      "main"
    )
}

async function findOrWaitForBitcoinTransaction(bitcoinAddress, expectedValue) {
  return await BitcoinHelpers.withElectrumClient(async electrumClient => {
	const script = BitcoinHelpers.Address.toScript(bitcoinAddress)

	// This function is used as a callback to electrum client. It is
	// invoked when an existing or a new transaction is found.
	const result = await BitcoinHelpers.Transaction.findWithClient(
		  electrumClient,
		  script,
		  expectedValue
		)
	return 	result;
  })
}

async function getConfirmations(transactionID){
	return BitcoinHelpers.withElectrumClient(async electrumClient => {
		try{
			const { confirmations } = await electrumClient.getTransaction(transactionID)
			return confirmations
		}catch (err) {
			return -1
		}
	})
}

