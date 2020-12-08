import { createRequire } from 'module'
const require = createRequire(import.meta.url)

var Web3 = require("web3")

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
var currentProvider = 1;

var web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
var systemContract =  new web3.eth.Contract(config.systemContractABI, config.systemContractAddress);
var TokenContract  =  new web3.eth.Contract(config.tokenContractABI, config.tokenContractAddress);
var KeepRandomBeaconOperatorContract =  new web3.eth.Contract(config.KeepRandomBeaconOperatorABI, config.KeepRandomBeaconOperatorAddress);
var KeepBondingContract =  new web3.eth.Contract(config.KeepBondingABI, config.KeepBondingAddress);
var TokenStakingContract =  new web3.eth.Contract(config.TokenStakingABI, config.TokenStakingAddress);
var BondedECDSAKeepFactoryContract = new web3.eth.Contract(config.BondedECDSAKeepFactoryABI, config.BondedECDSAKeepFactoryAddress);
var KeepTokenContract = new web3.eth.Contract(config.KeepTokenABI, config.KeepTokenAddress);

var needToChangeProvider = false;
  
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
		}else if(event.event=="Liquidated"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
			
		}else if(event.event=="SetupFailed"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
		}else if(event.event=="OwnershipTransferred"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'previousOwner':event.returnValues.previousOwner, 
				'newOwner':event.returnValues.newOwner, 
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
		}else if(event.event=="ExitedCourtesyCall"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'_depositContractAddress':event.returnValues._depositContractAddress, 
				'date': Utilities.toMysqlFormat(new Date(event.returnValues._timestamp * 1000)), 
			};
		}else{
			console.l(event);
		}
		

		db.connection.query('INSERT INTO systemContract SET ?', o, async function(err, result) {
			if(err==null) {
				db.connection.query("UPDATE `systemContract` set format_date = DATE_FORMAT(`date`, '%Y-%m-%d') WHERE `date` is not null AND format_date is NULL", {}, function(err1, result1) {});
				if(event.event=="Created"){
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
						'updating': 2,
						'isFunded':0,
						'isRedeemed':0
					}

					db.connection.query('INSERT INTO depositHistory SET ?', oh, function(err, result) {
						if(err==null) {
							db.connection.query("UPDATE `depositHistory` set date = DATE_FORMAT(`datetime`, '%Y-%m-%d') WHERE `datetime` is not null AND date is NULL", {}, function(err1, result1) {});
						}else{
							if(!err.toString().includes("Duplicate entry")){}
						}
					});
				}else if(event.event=="RegisteredPubkey"){
					db.connection.query("UPDATE `depositHistory` set updating = 2, _signingGroupPubkeyX = '"+event.returnValues._signingGroupPubkeyX+"', _signingGroupPubkeyY= '"+event.returnValues._signingGroupPubkeyY+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
					db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
				}else if(event.event=="Funded"){
					db.connection.query("UPDATE `depositHistory` set updating = 2, isFunded = 1 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
					db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
					
					Utilities.addSubscribeQueue(db,event);
					Utilities.addSubscribeOperatorQueue(db,event);
				}else if(event.event=="RedemptionRequested"){
					db.connection.query("UPDATE `depositHistory` set updating = 2 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
					Utilities.addSubscribeQueue(db,event);
					Utilities.addSubscribeOperatorQueue(db,event);
					
					var path =  __dirname+'/blocks_cache/'+event.blockNumber;
					var block = null; 
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
					db.connection.query("UPDATE `depositHistory` set `date` = '"+datetime+"', updated='"+datetime+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+datetime+"')", {}, function(err1, result1) {});
				}else if(event.event=="GotRedemptionSignature"){
					db.connection.query("UPDATE `depositHistory` set updating = 2 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
			
					var path =  __dirname+'/blocks_cache/'+event.blockNumber;
					var block = null; 
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
					db.connection.query("UPDATE `depositHistory` set `date` = '"+datetime+"', updated='"+datetime+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+datetime+"')", {}, function(err1, result1) {});
					
				}else if(event.event=="Redeemed"){
					db.connection.query("UPDATE `depositHistory` set updating = 0, currentState=7,isRedeemed=1 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
					db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
					Utilities.addSubscribeQueue(db,event);
					Utilities.addSubscribeOperatorQueue(db,event);
				}else if(event.event=="StartedLiquidation"){
					db.connection.query("UPDATE `depositHistory` set updating = 2 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
					db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
					Utilities.addSubscribeQueue(db,event);
					Utilities.addSubscribeOperatorQueue(db,event);
				}else if(event.event=="Liquidated"){
					db.connection.query("UPDATE `depositHistory` set updating = 2, currentState=11 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
					db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
					Utilities.addSubscribeQueue(db,event);
					Utilities.addSubscribeOperatorQueue(db,event);
				}else if(event.event=="CourtesyCalled"){
					db.connection.query("UPDATE `depositHistory` set updating = 2, currentState=8 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
					db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
					Utilities.addSubscribeQueue(db,event);
					Utilities.addSubscribeOperatorQueue(db,event);
				}else if(event.event=="ExitedCourtesyCall"){
					db.connection.query("UPDATE `depositHistory` set updating = 2 WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"'", {}, function(err1, result1) {});
					db.connection.query("UPDATE `depositHistory` set updated='"+o.date+"' WHERE `depositContractAddress` ='"+event.returnValues._depositContractAddress+"' AND (updated is NULL or updated<'"+o.date+"')", {}, function(err1, result1) {});
					Utilities.addSubscribeQueue(db,event);
					Utilities.addSubscribeOperatorQueue(db,event);
				}
				
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
				db.connection.query("UPDATE `depositHistory` set updating = 0 WHERE `depositContractAddress` ='"+event.returnValues.to+"'", {}, function(err1, result1) {});
			}else{
				if(!err.toString().includes("Duplicate entry")){
					console.l(event);
					console.l(err);
				}
			}
		});
	}else if(contract_type=="KeepRandomBeaconOperator"){
		if(event.event=="DkgResultSubmittedEvent"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'memberIndex':event.returnValues.memberIndex, 
				'groupPubKey':event.returnValues.groupPubKey, 
				'misbehaved':event.returnValues.misbehaved, 
			};
			
		}else if(event.event=="GroupMemberRewardsWithdrawn"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'beneficiary':event.returnValues.beneficiary, 
				'operator':event.returnValues.operator, 
				'amount':web3.utils.fromWei(event.returnValues.amount), 
				'groupIndex':event.returnValues.groupIndex,
			};
		}else if(event.event=="GroupSelectionStarted"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'newEntry':event.returnValues.newEntry
			};
		}else if(event.event=="OnGroupRegistered"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'groupPubKey':event.returnValues.groupPubKey
			};
		}else if(event.event=="RelayEntryRequested"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'previousEntry':event.returnValues.previousEntry,
				'groupPublicKey':event.returnValues.groupPublicKey
			};
		}else if(event.event=="RelayEntrySubmitted"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address
			};
		}else if(event.event=="RelayEntryTimeoutReported"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'groupIndex':event.returnValues.groupIndex
			};
		}else if(event.event=="UnauthorizedSigningReported"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'groupIndex':event.returnValues.groupIndex
			};
		}

		db.connection.query('INSERT INTO KeepRandomBeaconOperator SET ?', o, function(err, result) {
			if(err==null) {
				//db.connection.query("UPDATE `TokenContract` set format_date = DATE_FORMAT(`date`, '%Y-%m-%d') WHERE `date` is not null AND format_date is NULL", {}, function(err1, result1) {});
			}
		});
	}else if(contract_type=="KeepBonding"){
		if(event.event=="BondCreated"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator, 
				'holder':event.returnValues.holder, 
				'sortitionPool':event.returnValues.sortitionPool, 
				'referenceID':event.returnValues.referenceID, 
				'amount':web3.utils.fromWei(event.returnValues.amount),
			};
			
		}else if(event.event=="BondReassigned"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator, 
				'referenceID':event.returnValues.referenceID, 
				'newHolder':event.returnValues.newHolder, 
				'newReferenceID':event.returnValues.newReferenceID
			};
			
		}else if(event.event=="BondReleased"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator
			};
		}else if(event.event=="BondSeized"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'referenceID':event.returnValues.referenceID,
				'destination':event.returnValues.destination,
				'amount':web3.utils.fromWei(event.returnValues.amount)
			};	
		}else if(event.event=="UnbondedValueDeposited"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'beneficiary':event.returnValues.referenceID,
				'amount':web3.utils.fromWei(event.returnValues.amount)
			};	
		}else if(event.event=="UnbondedValueWithdrawn"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'beneficiary':event.returnValues.referenceID,
				'amount':web3.utils.fromWei(event.returnValues.amount)
			};	
		}
		
		db.connection.query('INSERT INTO KeepBonding SET ?', o, async function(err, result) {
			if(err==null) {
				try{	
					var availBond = web3.utils.fromWei(await KeepBondingContract.methods.unbondedValue(o.operator).call());
					db.connection.query("UPDATE operators SET availBond='"+availBond+"' WHERE operator ='"+o.operator+"'", {}, function(err2, result2) {});
				}catch (error) {
					
				}		
			}
		});
		
	}else if(contract_type=="TokenStaking"){
		if(event.event=="ExpiredLockReleased"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator, 
				'lockCreator':event.returnValues.lockCreator
			};
		}else if(event.event=="LockReleased"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator, 
				'lockCreator':event.returnValues.lockCreator
			};
		}else if(event.event=="OperatorStaked"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator, 
				'beneficiary':event.returnValues.beneficiary,
				'authorizer':event.returnValues.authorizer,
				'value':web3.utils.fromWei(event.returnValues.value)
			};
			
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
		
			var oh = {
				'operator':event.returnValues.operator,
				'staked': web3.utils.fromWei(event.returnValues.value), 
				'saked_at': datetime, 
			}

			db.connection.query('INSERT INTO operators SET ?', oh, function(err, result) {});
			
		}else if(event.event=="RecoveredStake"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator 
			};
		}else if(event.event=="RecoveredStake"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'owner':event.returnValues.owner
			};
		}else if(event.event=="StakeLocked"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'lockCreator':event.returnValues.lockCreator,
				'until':Utilities.toMysqlFormat(new Date(event.returnValues.until * 1000))
			};
		}else if(event.event=="StakeOwnershipTransferred"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'newOwner':event.returnValues.newOwner
			};
		}else if(event.event=="TokensSeized"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'amount':web3.utils.fromWei(event.returnValues.amount)
			};
		}else if(event.event=="TokensSlashed"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'amount':web3.utils.fromWei(event.returnValues.amount)
			};
		}else if(event.event=="TopUpCompleted"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'newAmount':web3.utils.fromWei(event.returnValues.newAmount)
			};
		}else if(event.event=="TopUpInitiated"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'topUp':web3.utils.fromWei(event.returnValues.topUp)
			};
		}else if(event.event=="Undelegated"){
			var o = {
				'txhash':event.transactionHash,
				'blockNumber':event.blockNumber,
				'event':event.event,
				'address':event.address,
				'operator':event.returnValues.operator,
				'undelegatedAt':Utilities.toMysqlFormat(new Date(event.returnValues.undelegatedAt * 1000)) 
			};
		}
		
		db.connection.query('INSERT INTO TokenStaking SET ?', o, async function(err, result) {
			if(err==null) {
				try {	
					var staked = web3.utils.fromWei(await BondedECDSAKeepFactoryContract.methods.balanceOf(o.operator).call());
					db.connection.query("UPDATE operators SET staked='"+staked+"' WHERE operator ='"+o.operator+"'", {}, function(err2, result2) {});
				} catch (error) {
					
				}	
			}else{
				if(!err.toString().includes("Duplicate entry")){
					console.l(err);
				}
			}
		});
	}else if(contract_type=="KeepToken"){
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
			try {
				block = await web3.eth.getBlock(event.blockNumber);
				if(!!block && block.timestamp) fs.writeFile( path, JSON.stringify(block),function(){});
			} catch (err) {

			}
		}
		

		if(!!block) datetime = Utilities.toMysqlFormat(new Date(block.timestamp * 1000)); else datetime = null;
	   
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
		
		db.connection.query('INSERT INTO KeepToken SET ?', o, function(err, result) {
			if(err==null) {
				db.connection.query("UPDATE `KeepToken` set format_date = DATE_FORMAT(`date`, '%Y-%m-%d') WHERE `date` is not null AND format_date is NULL", {}, function(err1, result1) {});
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
		var end = +new Date();
		
		if((end-start) / 1000 > 60 || startBlock-i > 50){
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

	
var j = schedule.scheduleJob('* * * * *', function(){
	try {	
		console.log("start fix all - " + Utilities.toMysqlFormat(new Date()));
		start = +new Date();
		
		if(needToChangeProvider==true){
			console.log("change provider");
			if(currentProvider + 1 <=2) currentProvider++; else currentProvider=0
			web3 = new Web3(new Web3.providers.WebsocketProvider(config.endpoint[currentProvider]));
			systemContract =  new web3.eth.Contract(config.systemContractABI, config.systemContractAddress);
			TokenContract  =  new web3.eth.Contract(config.tokenContractABI, config.tokenContractAddress);
			needToChangeProvider=false;
		}
		
		web3.eth.getBlockNumber().then(function(block){
			startBlock = block;
			console.log(block)
			remember(block,systemContract,"allEvents","systemContract");
			remember(block,TokenContract,"Transfer","TokenContract");
			remember(block,KeepRandomBeaconOperatorContract,"allEvents","KeepRandomBeaconOperator");
			remember(block,TokenStakingContract,"allEvents","TokenStaking"); 
			remember(block,KeepBondingContract,"allEvents","KeepBonding"); 
			remember(block,KeepTokenContract,"Transfer","KeepToken"); 
		}).catch(err=>{
			if(err.toString().includes("request rate limited")){
				needToChangeProvider = true;
			}
		});  		
	} catch (err) {
		console.log(err);
	}		
});

