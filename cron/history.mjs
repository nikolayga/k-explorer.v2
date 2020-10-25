import { createRequire } from 'module'
const require = createRequire(import.meta.url)

var Web3 = require("web3")
//var TBTC = require("@keep-network/tbtc.js");
//var ProviderEngine = require("web3-provider-engine");
//var Subproviders = require("@0x/subproviders");
//const engine = new ProviderEngine({ pollingInterval: 1000 })

const path = require('path');
const __dirname = path.resolve(path.dirname(''));

var fs = require('fs');
var config = require('./config/config');
var Utilities = require("./helpers/Utility");
var db = require('./helpers/Db');
var schedule = require('node-schedule');
var util = require('util');
var log_file = fs.createWriteStream(__dirname+'/debug.log', {flags : 'w'});
var log_stdout = process.stdout;
var throttle = 1000;
var start = +new Date();

console.l = function(d) { log_file.write(util.format(d) + '\n');};

var startBlock = 0;
var currentProvider = 0;

var web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
var systemContract =  new web3.eth.Contract(config.systemContractABI, config.systemContractAddress);
var TokenContract  =  new web3.eth.Contract(config.tokenContractABI, config.tokenContractAddress);

  
async function saveEvent(contract, name, event, contract_type){
	if(contract_type=="systemContract"){
		if(event.event=="Created"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'_keepAddress':event.returnValues._keepAddress, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
			
			var depositContract  =  new web3.eth.Contract(config.depositContractABI, event.returnValues._depositContractAddress);
			var LotSizeSatoshis = web3.utils.toBN(await depositContract.methods.lotSizeSatoshis().call());
			var lot_size = LotSizeSatoshis.toNumber()/100000000;
			
			var oh = {
				'depositContractAddress':event.returnValues._depositContractAddress,
				'keepAddress':event.returnValues._keepAddress, 
				'datetime': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
				'updated': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
				'currentState': 0,
				'lotsize': lot_size,
				'updating': 1,
				'isFunded':0,
				'isRedeemed':0
			}

			db.connection.query('INSERT INTO depositHistory SET ?', oh, function(err, result) {
			if(err==null) {
				db.connection.query("UPDATE `depositHistory` set date = DATE_FORMAT(`datetime`, '%Y-%m-%d') WHERE `datetime` is not null AND date is NULL", {}, function(err1, result1) {});
			}else{
				if(!err.toString().includes("Duplicate entry")){
	
				}
			}
		});
		
		}else if(event.event=="RegisteredPubkey"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'_signingGroupPubkeyX':event.returnValues._signingGroupPubkeyX, 
				'_signingGroupPubkeyY':event.returnValues._signingGroupPubkeyY,
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};

			db.connection.query("UPDATE `depositHistory` set _signingGroupPubkeyX = '"+event.returnValues._signingGroupPubkeyX+"', _signingGroupPubkeyY= '"+event.returnValues._signingGroupPubkeyY+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
		    db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
		}else if(event.event=="Funded"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'_txid':event.returnValues._txid, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
			
			db.connection.query("UPDATE `depositHistory` set isFunded = 1 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
			db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
			
			Utilities.addSubscribeQueue(db,event);
			
		}else if(event.event=="RedemptionRequested"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'_requester':event.returnValues._requester, 
				'_digest':event.returnValues._digest, 
				'_utxoValue':event.returnValues._utxoValue, 
				'_redeemerOutputScript':event.returnValues._redeemerOutputScript, 
				'_requestedFee':event.returnValues._requestedFee, 
				'_outpoint':event.returnValues._outpoint 
			};
			
			db.connection.query("UPDATE `depositHistory` set updating = 1 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
			Utilities.addSubscribeQueue(db,event);
		}else if(event.event=="GotRedemptionSignature"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'_digest':event.returnValues._digest, 
				'_r':event.returnValues._r, 
				'_s':event.returnValues._s 
			};
		}else if(event.event=="Redeemed"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'_txid':event.returnValues._digest, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
			
			db.connection.query("UPDATE `depositHistory` set updating = 0, currentState=7,isRedeemed=1 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
			db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
			Utilities.addSubscribeQueue(db,event);
		}else if(event.event=="StartedLiquidation"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'_wasFraud':event.returnValues.previousOwner, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
			
			db.connection.query("UPDATE `depositHistory` set updating = 1,updated="+o.date+" WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
		}else if(event.event=="Liquidated"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
			
			db.connection.query("UPDATE `depositHistory` set updating = 0, currentState=11,updated="+o.date+" WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
			Utilities.addSubscribeQueue(db,event);
		}else if(event.event=="SetupFailed"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
			
			db.connection.query("UPDATE `depositHistory` set updating = 0, currentState=3 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});	
			db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
		}else if(event.event=="OwnershipTransferred"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'previousOwner':event.returnValues.previousOwner, 
				'newOwner':event.returnValues.newOwner, 
				//'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
		}else if(event.event=="LotSizesUpdateStarted"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
		}else if(event.event=="LotSizesUpdated"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address, 
			};
		}else if(event.event=="CourtesyCalled"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
			
			db.connection.query("UPDATE `depositHistory` set updating = 0,updated="+o.date+" WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
			Utilities.addSubscribeQueue(db,event);
		}else if(event.event=="ExitedCourtesyCall"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
			db.connection.query("UPDATE `depositHistory` set updating = 0,updated="+o.date+" WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
			Utilities.addSubscribeQueue(db,event);
		}else{
			console.l(event);
		}
		

		db.connection.query('INSERT INTO systemContract SET ?', o, function(err, result) {
			if(err==null) {
				db.connection.query("UPDATE `systemContract` set format_date = DATE_FORMAT(`date`, '%Y-%m-%d') WHERE `date` is not null AND format_date is NULL", {}, function(err1, result1) {});
			}else{
				if(!err.toString().includes("Duplicate entry")){
					console.l(event);
					console.l(err);
				}
			}
		});
	}else if(contract_type=="TokenContract"){
		
		var path =  __dirname+'/blocks_cache/'+event.blockNumber;
		var block = null; 
		var transaction = null;
		var datetime = null;
		
		if (fs.existsSync(path)){
			try {
				block = JSON.parse(fs.readFileSync(path,{ encoding: 'utf8' }));
			} catch (err) {

			}	
		}else{
			block = await web3.eth.getBlock(event.blockNumber);
			if(!!block && block.timestamp) fs.writeFile( path, JSON.stringify(block),function(){});
		}
		

		if(!!block) datetime = Utilities.toMysqlFormat(new Date(block.timestamp * 1000)); else datetime = block;
	
	
		var o = {
			'txhash':event.transactionHash,
			'blockNumber':event.blockNumber,
			'event':event.event,
			'address':event.address,
			'from':event.returnValues.from,
			'to':event.returnValues.to,
			'date': datetime, 
			'value': web3.utils.fromWei(event.returnValues.value)
		};

		db.connection.query('INSERT INTO TokenContract SET ?', o, function(err, result) {
			if(err==null) {
				db.connection.query("UPDATE `TokenContract` set format_date = DATE_FORMAT(`date`, '%Y-%m-%d') WHERE `date` is not null AND format_date is NULL", {}, function(err1, result1) {});
			}else{
				if(!err.toString().includes("Duplicate entry")){
					console.l(event);
					console.l(err);
				}
			}
		});
	}
}

const remember = (i, contract, name , contract_type) => {
	try {	
		if (i < 10867000 ) return('done');
		
		var end = +new Date();

		if((end-start) / 1000 > 10 /*|| startBlock-i > 100*/){
			end=null;start=null;
			if (global.gc) global.gc();
			console.log("stop fix all - " + Utilities.toMysqlFormat(new Date()) + " - "+contract_type);
			return ("stop cron");
		}
		
		let eventPromise = contract.getPastEvents(name, { fromBlock: i-10, toBlock: i });
		
		eventPromise.then(pastEvents => {
			pastEvents.forEach(event=>{ 
				saveEvent(contract, name, event, contract_type) ;
			});

			setTimeout(()=>{
			  eventPromise = null;
			  pastEvents = null;
			  remember(i-9, contract, name, contract_type);
			}, throttle)
		}).catch(err=>{
			console.log(err);
		});  
	} catch (err) {
		console.log(err);
	}
}


const rememberBig = (i, contract, name , contract_type, max) => {
	if (i > max ) {	
		return('done');
	}
	var end = +new Date();

	let eventPromise = contract.getPastEvents(name, { fromBlock: i, toBlock: i+10000 });

	eventPromise.then(pastEvents => {
		console.l("eventPromise-"+contract_type);
		pastEvents.forEach(event=>{ 
			saveEvent(contract, name, event, contract_type) ;
		});

		setTimeout(()=>{
		  eventPromise = null;
		  pastEvents = null;
		  rememberBig(i+9999, contract, name, contract_type, max);
		}, throttle)
	}).catch(err=>{
		console.log(err);
	});  
}

/*
try {	
	console.log("start fix all - " + Utilities.toMysqlFormat(new Date()));
	start = +new Date();
	console.l("start");
	web3.eth.getBlockNumber().then(function(block){
		startBlock = block;
		console.log(block)
		rememberBig(11013227,systemContract,"allEvents","systemContract",block);
		rememberBig(11013227,TokenContract,"Transfer","TokenContract",block);
	}).catch(err=>{
		console.log(err);
	});  		
} catch (err) {
	console.log(err);
}	
	*/
	
var j = schedule.scheduleJob('* * * * *', function(){
	try {	
		console.log("start fix all - " + Utilities.toMysqlFormat(new Date()));
		start = +new Date();
		web3.eth.getBlockNumber().then(function(block){
			startBlock = block;
			console.log(block)
			remember(block,systemContract,"allEvents","systemContract");
			remember(block,TokenContract,"Transfer","TokenContract");

		}).catch(err=>{
			console.log(err);
			if(err.code==-32603){
				console.log("change provider");
				if(currentProvider + 1 <=2) currentProvider++; else currentProvider=0
				web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
				systemContract =  new web3.eth.Contract(config.systemContractABI, config.systemContractAddress);
				TokenContract  =  new web3.eth.Contract(config.tokenContractABI, config.tokenContractAddress);
			}
		});  		
	} catch (err) {
		console.log(err);
	}		
});

