import TBTC from "@keep-network/tbtc.js"
import { BitcoinHelpers } from "@keep-network/tbtc.js"

import { createRequire } from 'module'
const require = createRequire(import.meta.url)

const Web3 = require("web3")

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
fs.writeFileSync(__dirname+'/update_deposits_state.run', (new Date().getTime()).toString());

var startBlock = 0;
var currentProvider = 3;
var web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
var needToChangeProvider = false;

		
async function upate_item(item){
	try {	
	
		var depositContract  =  new web3.eth.Contract(config.depositContractABI, item.depositContractAddress);
		var keepContract  =  new web3.eth.Contract(config.keepContractABI, item.keepAddress);
		
		var bitcoinAddress = null
		var transactions = null
		
		var currentState = await depositContract.methods.currentState().call();
		var updating = 1
		
		db.connection.query("UPDATE `depositHistory` set updating = 1 WHERE currentState=2 AND `isFunded` = 1", {}, function(err1, result1) {});
		db.connection.query("UPDATE `depositHistory` set updating = 1 WHERE currentState=0 AND `updating` = 0", {}, function(err1, result1) {});
		
		if(parseInt(currentState)>0){
			if(currentState == 3  || currentState==7 || currentState==11) updating = 0;
			db.connection.query("UPDATE `depositHistory` set updating = "+updating+", currentState="+currentState+" WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
		}	
		
		if((!item.isFunded || !item.bitcoinAddress || !item.bitcoinTransaction) && (item.currentState==2 || item.currentState==4)){
			if(!item.bitcoinAddress && item._signingGroupPubkeyX && item._signingGroupPubkeyX){
				var publicKeyPoint = {
					x: item._signingGroupPubkeyX,
					y: item._signingGroupPubkeyY
				}
				
				bitcoinAddress =  await publicKeyPointToBitcoinAddress(publicKeyPoint);
				if(bitcoinAddress){
					db.connection.query("UPDATE `depositHistory` set bitcoinAddress = '"+bitcoinAddress+"' WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
				}
			}else if((!item._signingGroupPubkeyX || !item._signingGroupPubkeyY) && currentState>1){
				db.connection.query("SELECT *  FROM  systemContract WHERE event='RegisteredPubkey' AND _depositContractAddress='"+item.depositContractAddress+"'", {}, function(err, data) {
					if(err==null && data[0]) {
						db.connection.query("UPDATE `depositHistory` set _signingGroupPubkeyX='"+data[0]._signingGroupPubkeyX+"', _signingGroupPubkeyY='"+data[0]._signingGroupPubkeyY+"' WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
					}
				});
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
		
		
		if(!item.keepBond && (item.currentState==2 || item.currentState==4)){
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
		
		if(item.updating==1 && currentState==4 && item.isFunded){
			db.connection.query("UPDATE `depositHistory` set updating=0 WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
		}
	} catch (err) {
		if(err.toString().includes("request rate limited")){
			needToChangeProvider = true;
		}
		
		if(err.toString().includes("connection not open on send")){
			process.exit();
		}
		
		if(!needToChangeProvider) console.log(err.toString());
	}
}
 
 
var j = schedule.scheduleJob('* * * * *', function(){
	try {	
		console.log("start updating (1) - " + Utilities.toMysqlFormat(new Date()));
		fs.writeFileSync(__dirname+'/update_deposits_state.run', (new Date().getTime()).toString());
		
		if(needToChangeProvider==true){
			console.log("change provider");
			if(currentProvider + 1 <=3) currentProvider++; else currentProvider=0
			web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
			needToChangeProvider = false;
		}
		
		console.log(currentProvider);
		console.log(config.endpoint[currentProvider]);
		
		web3.eth.getBlockNumber().then( function(block){
			db.connection.query('SELECT * FROM depositHistory WHERE updating=2 AND currentState!=3 ORDER BY rand() LIMIT 50', {}, function(err, result) {
				if(err==null) {
					result.forEach( function(item, index, result) {
						if(!needToChangeProvider) upate_item(item)
					})
				}
			});
		}).catch(err=>{
			console.log(err);
			if(err.toString().includes("request rate limited")){
				needToChangeProvider = true;
			}
		});  	
	} catch (err) {
		console.log(err);
	}
});

var j = schedule.scheduleJob('*/2 * * * *', function(){
	try {	
		console.log("start updating (3) - " + Utilities.toMysqlFormat(new Date()));
		
		if(needToChangeProvider==true){
			console.log("change provider");
			if(currentProvider + 1 <=3) currentProvider++; else currentProvider=0
			web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
			needToChangeProvider = false;
		}
		
		console.log(currentProvider);
		console.log(config.endpoint[currentProvider]);
		
		web3.eth.getBlockNumber().then( function(block){
			db.connection.query('SELECT * FROM depositHistory WHERE updating=1 AND currentState!=3 AND datetime>=DATE_SUB(now(), INTERVAL 3 HOUR) ORDER BY rand() LIMIT 50', {}, function(err, result) { //BY currentState asc
				if(err==null) {
					result.forEach( function(item, index, result) {
						if(!needToChangeProvider) upate_item(item)
					})
				}
			});
		}).catch(err=>{
			console.log(err);
			if(err.toString().includes("request rate limited")){
				needToChangeProvider = true;
			}
		});  	
	} catch (err) {
		console.log(err);
	}
});

var j = schedule.scheduleJob('*/3 * * * *', function(){
	try {	
		console.log("start updating (10) - " + Utilities.toMysqlFormat(new Date()));
		
		if(needToChangeProvider==true){
			console.log("change provider");
			if(currentProvider + 1 <=3) currentProvider++; else currentProvider=0
			web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
			needToChangeProvider = false;
		}
		
		console.log(currentProvider);
		console.log(config.endpoint[currentProvider]);
		
		web3.eth.getBlockNumber().then( function(block){
			db.connection.query('SELECT * FROM depositHistory WHERE updating=1 AND currentState!=3 AND datetime<DATE_SUB(now(), INTERVAL 3 HOUR) ORDER BY currentState DESC, `datetime` asc LIMIT 50', {}, function(err, result) { //BY currentState asc
				if(err==null) {
					result.forEach( function(item, index, result) {
						if(!needToChangeProvider) upate_item(item)
					})
				}
			});
		}).catch(err=>{
			console.log(err);
			if(err.toString().includes("request rate limited")){
				needToChangeProvider = true;
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
  BitcoinHelpers.setElectrumConfig({
			  server: config.electrumx_server,
			  port: config.electrumx_port,
			  protocol: config.electrumx_protocol
			});
  return await BitcoinHelpers.withElectrumClient(async electrumClient => {
	const script = BitcoinHelpers.Address.toScript(bitcoinAddress)
	BitcoinHelpers.setElectrumConfig
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
	BitcoinHelpers.setElectrumConfig({
			  server: config.electrumx_server,
			  port: config.electrumx_port,
			  protocol: config.electrumx_protocol
			});
	return BitcoinHelpers.withElectrumClient(async electrumClient => {
		try{
			const { confirmations } = await electrumClient.getTransaction(transactionID)
			return confirmations
		}catch (err) {
			return -1
		}
	})
}

