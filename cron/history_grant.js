var Web3 = require("web3")

var fs = require('fs');
var config = require('./config/config');
var Utilities = require("./helpers/Utility");
var db = require('./helpers/Db');
var schedule = require('node-schedule');
var util = require('util');
var log_file = fs.createWriteStream(__dirname+'/debug_grant.log', {flags : 'w'});
var log_stdout = process.stdout;
var throttle = 1000;
var start = +new Date();

console.l = function(d) { log_file.write(util.format(d) + '\n');};

var startBlock = 0;
var currentProvider = 4;

var web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
var grantContract =  new web3.eth.Contract(config.grantContractABI, config.grantContractAddress);


var needToChangeProvider = false;
  
async function saveEvent(contract, name, event, contract_type){
	
	if(event.event=="StakingContractAuthorized"){
		var o = {
			'txhash':event.transactionHash,
			'blockNumber':event.blockNumber,
			'event':event.event,
			'address':event.address,
			'grantManager':event.returnValues.grantManager, 
			'stakingContract':event.returnValues.stakingContract, 
		};
	}else if(event.event=="TokenGrantCreated"){
		var o = {
			'txhash':event.transactionHash,
			'blockNumber':event.blockNumber,
			'event':event.event,
			'address':event.address,
			'grantId':event.returnValues.id, 
		};
			
	}else if(event.event=="TokenGrantRevoked"){
		var o = {
			'txhash':event.transactionHash,
			'blockNumber':event.blockNumber,
			'event':event.event,
			'address':event.address,
			'grantId':event.returnValues.id, 
		};
	}else if(event.event=="TokenGrantStaked"){
		var o = {
			'txhash':event.transactionHash,
			'blockNumber':event.blockNumber,
			'event':event.event,
			'address':event.address,
			'grantId':event.returnValues.grantId, 
			'amount': web3.utils.fromWei(event.returnValues.amount), 
			'operator':event.returnValues.operator
		};
	}else if(event.event=="TokenGrantWithdrawn"){
		var o = {
			'txhash':event.transactionHash,
			'blockNumber':event.blockNumber,
			'event':event.event,
			'address':event.address,
			'grantId':event.returnValues.grantId, 
			'amount': web3.utils.fromWei(event.returnValues.amount), 
		};
	}

	if(o.grantId && o.grantId>0){
		var grant = await grantContract.methods.grants(o.grantId).call();
		
		var availableToStake = await grantContract.methods.availableToStake(o.grantId).call();
		var unlockedAmount = await grantContract.methods.unlockedAmount(o.grantId).call();
		var withdrawable = await grantContract.methods.withdrawable(o.grantId).call();
		
		var oh = {
			'id':o.grantId,
			'amount': web3.utils.fromWei(grant.amount), 
			'withdrawn': web3.utils.fromWei(grant.withdrawn), 
			'staked': web3.utils.fromWei(grant.staked), 
			'revokedAmount': web3.utils.fromWei(grant.revokedAmount), 
			'revokedWithdrawn': web3.utils.fromWei(grant.revokedWithdrawn), 
			'revocable': grant.revocable ? 1 : 0,
			'grantee': grant.grantee, 
			'grantManager':grant.grantManager,
			'duration':(parseInt(grant.duration)/86400),
			'start':Utilities.toMysqlFormat(new Date(grant.start * 1000)),
			'end':Utilities.toMysqlFormat(new Date((parseInt(grant.start) + parseInt(grant.duration)) * 1000)),
			'cliff':Utilities.toMysqlFormat(new Date(grant.cliff * 1000)),
			'policy':grant.stakingPolicy,
			'availableToStake':web3.utils.fromWei(availableToStake), 
			'unlockedAmount':web3.utils.fromWei(unlockedAmount), 
			'withdrawable':web3.utils.fromWei(withdrawable), 
		}
		
		if(grant.revokedAt>0) oh.revokedAt = Utilities.toMysqlFormat(new Date(grant.revokedAt * 1000));
		
		db.connection.query('SELECT * FROM grants WHERE id='+o.grantId, {}, function(err, result) {
			if(err==null && result && result.length>0) {
				db.connection.query('UPDATE grants SET ? WHERE id='+o.grantId, oh, function(err, result) {});
			}else if(err==null && result.length==0){
				db.connection.query('INSERT INTO grants SET ?', oh, function(err, result) {console.l(err);});
			}
		});
	}
	
	db.connection.query('INSERT INTO grantContract SET ?', o, function(err, result) {
		if(err==null) {

		}else{
			if(!err.toString().includes("Duplicate entry")){
				console.l(event);
				console.l(err);
			}
		}
	});
	
}

async function updateGrant(grantId){
	var grant = await grantContract.methods.grants(grantId).call();
	
	var availableToStake = await grantContract.methods.availableToStake(grantId).call();
	var unlockedAmount = await grantContract.methods.unlockedAmount(grantId).call();
	var withdrawable = await grantContract.methods.withdrawable(grantId).call();
	
	var oh = {
		'id':grantId,
		'amount': web3.utils.fromWei(grant.amount), 
		'withdrawn': web3.utils.fromWei(grant.withdrawn), 
		'staked': web3.utils.fromWei(grant.staked), 
		'revokedAmount': web3.utils.fromWei(grant.revokedAmount), 
		'revokedWithdrawn': web3.utils.fromWei(grant.revokedWithdrawn),
		'revocable': grant.revocable ? 1 : 0,
		'grantee': grant.grantee, 
		'grantManager':grant.grantManager,
		'duration':(parseInt(grant.duration)/86400),
		'start':Utilities.toMysqlFormat(new Date(grant.start * 1000)),
		'end':Utilities.toMysqlFormat(new Date((parseInt(grant.start) + parseInt(grant.duration)) * 1000)),
		'cliff':Utilities.toMysqlFormat(new Date(grant.cliff * 1000)),
		'policy':grant.stakingPolicy,
		'availableToStake':web3.utils.fromWei(availableToStake), 
		'unlockedAmount':web3.utils.fromWei(unlockedAmount), 
		'withdrawable':web3.utils.fromWei(withdrawable), 
	}
	
	if(grant.revokedAt>0) oh.revokedAt = Utilities.toMysqlFormat(new Date(grant.revokedAt * 1000));
	
	db.connection.query('SELECT * FROM grants WHERE id='+grantId, {}, function(err, result) {
		if(err==null && result && result.length>0) {
			db.connection.query('UPDATE grants SET ? WHERE id='+grantId, oh, function(err, result) {console.l(err);});
		}else if(err==null && result.length==0){
			db.connection.query('INSERT INTO grants SET ?', oh, function(err, result) {console.l(err);});
		}
	});
}

const remember = (i, contract, name , contract_type) => {
	try {	
		
		var end = +new Date();
		
		
		if((end-start) / 1000 > 2 || startBlock-i > 70){
			end=null;start=null;
			if (global.gc) global.gc();
			console.log("stop fix all - " + Utilities.toMysqlFormat(new Date()) + " - "+contract_type);
			return ("stop cron");
		}
		
		
		let eventPromise = contract.getPastEvents(name, { fromBlock: i-30, toBlock: i });
		
		eventPromise.then(pastEvents => {
			pastEvents.forEach(event=>{ 
				saveEvent(contract, name, event, contract_type) ;
			});

			setTimeout(()=>{
			  eventPromise = null;
			  pastEvents = null;
			  remember(i-29, contract, name, contract_type);
			}, throttle)
		}).catch(err=>{
			console.log(err);
			if(err.toString().includes("request rate limited")){
				needToChangeProvider = true;
			}
		});  
	} catch (err) {
		console.log(err);
		if(err.toString().includes("request rate limited")){
			needToChangeProvider = true;
		}
	}
}

var j = schedule.scheduleJob('*/5 * * * *', function(){
	try {	
		console.log("start garant all - " + Utilities.toMysqlFormat(new Date()));
		start = +new Date();
		
		web3.eth.getBlockNumber().then(function(block){
			startBlock = block;
			remember(block,grantContract,"allEvents","grantContract");

		}).catch(err=>{
			if(err.toString().includes("request rate limited")){
				needToChangeProvider = true;
			}
		});  		
	} catch (err) {
		console.log(err);
	}	
});

var j = schedule.scheduleJob('* 1 * * *', function(){
	try {
		console.log("start garant all hour - " + Utilities.toMysqlFormat(new Date()));
		db.connection.query("SELECT id FROM `grants` WHERE 1", {}, function(err, result) { 
			if(err==null) {
				result.forEach( function(item, index, result) {
					updateGrant(item.id);
				})
			}
		});
	} catch (err) {
		console.log(err);
	}
});

