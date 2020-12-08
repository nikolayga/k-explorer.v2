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
const log_stdout = process.stdout;


fs.writeFileSync(__dirname+'/fix_deposits.run', (new Date().getTime()).toString());

var startBlock = 0;
var currentProvider = 2;
var web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
var needToChangeProvider = false;

 
async function bond_upate_item(item){
	try {	
		var depositContract  =  new web3.eth.Contract(config.depositContractABI, item.depositContractAddress);
		var keepContract  =  new web3.eth.Contract(config.keepContractABI, item.keepAddress);
		

		if(!item.keepBond && item.currentState>=0){
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
		if(err.toString().includes("request rate limited")){
			needToChangeProvider = true;
		}
		
		if(err.toString().includes("connection not open on send")){
			process.exit();
		}
		
		if(!needToChangeProvider) console.log(err.toString());
	}
}

async function state_upate_item(item){
	try {	
		var depositContract  =  new web3.eth.Contract(config.depositContractABI, item.depositContractAddress);
		var keepContract  =  new web3.eth.Contract(config.keepContractABI, item.keepAddress);
		var currentState = await depositContract.methods.currentState().call();
		
		db.connection.query("UPDATE `depositHistory` set currentState="+currentState+" WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
		
		
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
 
async function pubKey_upate_item(item){
	try {	
		var keepContract  =  new web3.eth.Contract(config.keepContractABI, item.keepAddress);
		var publicKey = await keepContract.methods.publicKey().call();
		
		if(!publicKey) publicKey = 0;
		db.connection.query("UPDATE `depositHistory` set pubKey='"+publicKey+"' WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
		
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
		fs.writeFileSync(__dirname+'/fix_deposits.run', (new Date().getTime()).toString());
		
		//FIX funded flag	
		console.log("FIX funded flag - " + Utilities.toMysqlFormat(new Date()));
		db.connection.query("SELECT depositHistory.*  FROM  depositHistory LEFT JOIN systemContract ON depositHistory.depositContractAddress = systemContract._depositContractAddress WHERE depositHistory.isFunded=0 AND systemContract.event='Funded'", {}, function(err, result) { 
			if(err==null) {
				result.forEach( function(item, index, result) {
					db.connection.query("UPDATE `depositHistory` set isFunded=1,updating=1 WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
				})
			}
		});
		
		db.connection.query("SELECT depositHistory.id,systemContract._signingGroupPubkeyX,systemContract._signingGroupPubkeyY  FROM depositHistory LEFT JOIN systemContract ON systemContract._depositContractAddress = depositHistory.depositContractAddress WHERE systemContract.event='RegisteredPubkey' AND depositHistory._signingGroupPubkeyX IS NULL", {}, function(err, result) { 
			if(err==null) {
				result.forEach( function(item, index, result) {
					db.connection.query("UPDATE `depositHistory` set _signingGroupPubkeyX='"+item._signingGroupPubkeyX+"',_signingGroupPubkeyY='"+item._signingGroupPubkeyY+"' WHERE `id` ='"+item.id+"'", {}, function(err1, result1) {});
				})
			}
		});
		
	} catch (err) {
		console.log(err);
	}
});

var j = schedule.scheduleJob('* * * * *', function(){
	try {
		//FIX bonded
		console.log("FIX bonded - " + Utilities.toMysqlFormat(new Date()));
		db.connection.query("SELECT depositHistory.*  FROM  depositHistory WHERE (keepBond is null OR keepMembers is null) AND (currentState=0 OR currentState=1 OR currentState=2 OR currentState=4)", {}, function(err, result) { 
			if(err==null) {
				result.forEach( function(item, index, result) {
					bond_upate_item(item)
				})
			}
		});
	} catch (err) {
		console.log(err);
	}
});

var j = schedule.scheduleJob('* * * * *', function(){
	try {
		//FIX state
		console.log("FIX state - " + Utilities.toMysqlFormat(new Date()));
		db.connection.query("SELECT depositHistory.*  FROM  depositHistory WHERE (currentState=1 OR currentState=2) ORDER BY `datetime` DESC", {}, function(err, result) { 
			if(err==null) {
				result.forEach( function(item, index, result) {
					state_upate_item(item)
				})
			}
		});
	} catch (err) {
		console.log(err);
	}
});

var j = schedule.scheduleJob('* * * * *', function(){
	try {
		//FIX minted flag	
		db.connection.query("SELECT * FROM `depositHistory` WHERE `isFunded` = '1' AND `isMinted` IS NULL AND `currentState` = '4'", {}, function(err, result) { 
			if(err==null) {
				result.forEach( function(item, index, result) {
					db.connection.query("SELECT txhash  FROM  TokenContract WHERE `to`='"+item.depositContractAddress+"'", {}, function(err, data) {
						if(err==null && data[0]) {
							db.connection.query("SELECT *  FROM  TokenContract WHERE `to`!='"+item.depositContractAddress+"' and  txhash='"+data[0].txhash+"'", {}, function(err, r) {
								if(err==null) {
									db.connection.query("UPDATE `depositHistory` set isMinted = 1, mintedBy='"+r[0].to+"' WHERE `depositContractAddress` ='"+item.depositContractAddress+"'", {}, function(err1, result1) {});
								}
							});
						}
					});
				})
			}
		});
		
	} catch (err) {
		console.log(err);
	}
});

var j = schedule.scheduleJob('* * * * *', function(){
	try {
		//FIX state
		console.log("FIX pubkey - " + Utilities.toMysqlFormat(new Date()));
		db.connection.query("SELECT depositHistory.*  FROM  depositHistory WHERE currentState=3 AND pubKey IS NULL ORDER BY `datetime` DESC", {}, function(err, result) { 
			if(err==null) {
				result.forEach( function(item, index, result) {
					pubKey_upate_item(item)
				})
			}
		});
	} catch (err) {
		console.log(err);
	}
});
