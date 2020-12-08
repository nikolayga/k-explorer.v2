

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 mt-3 ">
	<?php $this->view('templates/menu-mobile'); ?>
	<div class="mx-90p"><h3 class="mb-3"><?=$operator['operator']?></h3></div>
	
	<div class="row row-eq-height">
		<div class="col-md-12 col-lg-12 col-xl-6">
			<div class="card h-100">
			  <div class="card-body">
				<h4 class="card-title">Operator info</h4>
				<div class="table-responsive">
				<table class="table table-sm table-hover table-striped table-compact">
				  <tbody>
					<tr>
					  <th scope="row">Staked</th>
					  <td id="js--staked" data-staked="<?=$operator['staked']?>"><?=number_format($operator['staked'])?> KEEP</td>
					</tr>
					<tr>
					  <th scope="row">Bonded</th>
					  <td><?=number_format($operator['bonded'])?> ETH</td>
					</tr>
					<tr>
					  <th scope="row">Available for bonding</th>
					  <td id="js--bonding_avail" data-bonding_avail="<?=$operator['availBond']?>"><?=number_format($operator['availBond'],2)?> ETH</td>
					</tr>
					<tr>
					  <th scope="row">Securing deposits</th>
					  <td><?=$operator['deposits']?></td>
					</tr>
					<tr>
					  <th scope="row">ETH Rewards</th>
					  <td id="js--eth_rewards"><?=!empty($operator['eth_rewards'])?number_format($operator['eth_rewards'],2).' ETH':''?> <img src="/assets/images/line-loader.gif" title="Refreshing data"></td>
					</tr>
					<tr>
					  <th scope="row">tBTC Rewards</th>
					  <td id="js--eth_rewards"><?=number_format($operator['tbtc_rewards'],6).' tBTC'?></td>
					</tr>
					<?/*<tr>
					  <th scope="row">Setup faults</th>
					  <td><?=$operator['faults']?></td>
					</tr>*/?>
					<tr class="js--need_web3 d-none">
						<th scope="row">Beacon authorization:</th>
						<td class="js--beacon_auth"><img src="/assets/images/line-loader.gif" title="Refreshing data"></td>
					</tr>
					<tr class="js--need_web3 d-none">
						<th scope="row">Bonded ECDSA Keep factory authorization:</th>
						<td class="js--ecdsa_auth"><img src="/assets/images/line-loader.gif" title="Refreshing data"></td>
					</tr>
					<tr class="js--need_web3 d-none">
						<th scope="row">Is eligible for tbtc system contract:</th>
						<td class="js--is_eligible"><img src="/assets/images/line-loader.gif" title="Refreshing data"></td>
					</tr>
					<tr class="js--need_web3 d-none">
						<th scope="row">tBTC system authorization:</th>
						<td class="js--tbtc_auth"><img src="/assets/images/line-loader.gif" title="Refreshing data"></td>
					</tr>
				  </tbody>
				</table>
				 </div>
			  </div>
			</div>
		</div>
		<div class="col-md-12 col-lg-12 col-xl-6">
			<div class="card h-100">
			  <div class="card-body">
				<h4 class="card-title">Subscribe to events</h4>
				<?if(empty($_SESSION['AUTH'])):?>
				<div class=" text-center"><a href="" class="btn btn-primary btn-lg js-auth">Connect With MetaMask</a></div>
				
				<p class="mt-3">To be able to subscribe, please connect with your crypto wallet</p>
				<?else:?>
					<table class="table table-sm table-compact js-subscribe_table mb-0">
						<tr>
							  <td>
								<form action="" method="POST" class="email-confirmation" data-url="/ajax/subscribe-confirmation/">
									<input type="hidden" name="operatorAddress" value="<?=htmlspecialchars($operator['operator'])?>">
									
									<?
								
									if(empty($subscribe)){
										if(!isset($_POST['email']) && isset($_SESSION['EMAIL'])) $_POST['email']= $_SESSION['EMAIL'];
									}else{
										if(!empty($subscribe['email'])) $_POST['email'] = $subscribe['email'];
									}
									?>
									<div class="row">
											<div class="col">
											  <input type="email" class="form-control" value="<?=@htmlspecialchars($_POST['email'])?>" placeholder="Email address" name="email" required <?=((!empty($_SESSION['EMAIL_CONFIRMATION_CODE']) && empty($subscribe) ) || !empty($subscribe))?'disabled':''?>>
											</div>
											
											<div class="col text-right">
												<?if(empty($subscribe)):?>
													<?if(!empty($_SESSION['EMAIL_CONFIRMATION_CODE'])):?>
														<input type="text" class="form-control d-inline-block" placeholder="Enter code" name="code" required style="width:115px;" maxlength="5">&nbsp;
														<button class="btn btn-info" type="submit" style="margin-top: -6px;">Confirm</button>
													<?else:?>
														<button class="btn btn-info" type="submit">Send confirmation code</button>
													<?endif?>
												<?else:?>
													<button class="btn btn-warning" type="submit">Unsubscribe</button>
													<input type="hidden" name="unsubscribe" value="Y">
												<?endif?>
											</div>
											
									</div>
								 </form>
							  </td>
						 </tr>
					</table>
					<form action="" method="POST" class="email-configuration" data-url="/ajax/subscribe-confirmation/">
						<input type="hidden" name="save" value="configuration">
						<input type="hidden" name="operatorAddress" value="<?=htmlspecialchars($operator['operator'])?>">
						<?
						$events = array();
						if(!empty($subscribe) && !empty($subscribe['events'])) $events = json_decode($subscribe['events'],true);
						?>
						<table class="table table-sm table-compact js-subscribe_table">
							<tr>
							  <th scope="row" align="center">Events</th>
							</tr>
							<tr>
							  <td><label><input type="checkbox" name="event[]" value="Funded" <?=empty($subscribe)?'disabled':''?> <?=in_array("Funded",$events)?'checked':''?>> Funded</label></td>
							</tr>
							<tr>
							  <td><label><input type="checkbox" name="event[]" value="RedemptionRequested" <?=empty($subscribe)?'disabled':''?> <?=in_array("RedemptionRequested",$events)?'checked':''?>> Redemption Requested</label></td>
							</tr>
							<tr>
							  <td><label><input type="checkbox" name="event[]" value="Redeemed" <?=empty($subscribe)?'disabled':''?> <?=in_array("Redeemed",$events)?'checked':''?>> Redeemed</label></td>
							</tr>
							<tr>
							  <td><label><input type="checkbox" name="event[]" value="CourtesyCalled" <?=empty($subscribe)?'disabled':''?> <?=in_array("CourtesyCalled",$events)?'checked':''?>> Courtesy Called</label></td>
							</tr>
							<tr>
							  <td><label><input type="checkbox" name="event[]" value="ExitedCourtesyCall" <?=empty($subscribe)?'disabled':''?> <?=in_array("ExitedCourtesyCall",$events)?'checked':''?>> Exited Courtesy Call</label></td>
							</tr>
							<tr>
							  <td><label><input type="checkbox" name="event[]" value="StartedLiquidation" <?=empty($subscribe)?'disabled':''?> <?=in_array("StartedLiquidation",$events)?'checked':''?>> Started Liquidation</label></td>
							</tr>
							<tr>
							  <td><label><input type="checkbox" name="event[]" value="Liquidated" <?=empty($subscribe)?'disabled':''?> <?=in_array("Liquidated",$events)?'checked':''?>> Liquidated</label></td>
							</tr>
							<tr>
							  <th scope="row" align="center">Other</th>
							</tr>
							<tr>
							  <td><label>Notify if collateralization less than (%)</label> <input type="number" name="collateralization" value="<?=@htmlspecialchars($subscribe['collateralization'])?>" class="form-control"  <?=empty($subscribe)?'disabled':''?>></td>
							</tr>
							<tr>
							  <td align="center"><button type="submit" class="btn btn-primary mt-3" <?=empty($subscribe)?'disabled':''?> >Save subscribe configuration</button></td>
							</tr>
							
						</table>
					</form>

				<?endif?>
			  </div>
			</div>
		</div>
	</div>
	
	<hr>
	<a href="/operators/" class="d-block mt-3"> Back to operators</a>
	<hr>
	<h4>Securing deposits</h4>
	<div class="row ">
		<div class="col-md-12">
			<h5>Total deposits: <?=count($operator['deposits_list'])?></h5>
			<div class="table-responsive pb-3" style="background:#fff;">
				<table  class="table table-striped table-bordered table-compact js--data-table" data-sort-col="1">
				   <thead>
						<tr>
							<th>Created</th>
							<th>Updated</th>
							<th class="">Deposit contract</th>
							<th>Lot Size</th>
							<th class="">Collateralization</th>
							<th >State</th>
							<th style="width:105px;" class="no-sort"></th>
						</tr>
				   </thead>
				   <tbody>
					   <?if(count($operator['deposits_list'])>0):?>
					   <?
						$client = new Codenixsv\CoinGeckoApi\CoinGeckoClient();
						$data = $client->simple()->getPrice('ethereum,bitcoin', 'usd,btc');
					   ?>
					   <?php foreach ($operator['deposits_list'] as  $row) :?>
						   <?
						   $m = new \Moment\Moment($row['datetime']);
						   $u = new \Moment\Moment($row['updated']);
						   $state = '<span class="badge badge-secondary">Processing</span>';
						   $redeem_action = '';
						   if($row['currentState']==0){
								$state = '<span class="badge badge-warning">Start</span>';
						   }elseif($row['currentState']==1){
							   $state = '<span class="badge badge-warning">Awaiting signer setup</span>';
						   }elseif($row['currentState']==2){
							   if(!$row['bitcoinTransaction']){
								   $state = '<span class="badge badge-warning">Awaiting Funding</span>';
							   }else{
								   $conf_detail = '('.intval($row['bitcoinConfirmations']).'/6)';
								   if(!empty($row['bitcoinTransaction'])){
									   $conf_detail  = '<a href="https://www.blockchain.com/btc/tx/'.$row['bitcoinTransaction'].'" target="_blank">'.$conf_detail .'</a>';
								   }
								   $state = '<span class="badge badge-warning">Awaiting BTC funding proof '.$conf_detail.'</span>';
							   }
						   }elseif($row['currentState']==3){
							   $state = '<span class="badge badge-secondary">Failed setup</span>';
							   if(!empty($row['_signingGroupPubkeyX'])) $state = '<span class="badge badge-secondary">Funding timeout</span>';
						   }elseif($row['currentState']==4){
							   $minted = '';
							   if($row['isMinted']==1)  {
								   $minted = '&nbsp;<img src="/assets/images/tbtc_32.png" width="16" title="tBTC minted" style="argin-top: -2px;">';
								   $redeem_action = '<a href="https://dapp.tbtc.network/deposit/'.$row['depositContractAddress'].'/redeem" target="_blank" class="btn btn-sm btn-info float-right">Redeem</a>';
							   }
							   $state = '<span class="badge badge-success">Active</span>'.$minted.$redeem_action;
						   }elseif($row['currentState']==5){
							   $state = '<span class="badge badge-warning">Awaiting withdrawal signature</span>';
						   }elseif($row['currentState']==6){
							   $state = '<span class="badge badge-warning">Awaiting withdrawal proof</span>';
						   }elseif($row['currentState']==7){
							   $state = '<span class="badge badge-dark">Redeemed</span>';
						   }elseif($row['currentState']==8){
							   $state = '<span class="badge badge-danger">Courtesy call</span>';
						   }elseif($row['currentState']==9){
							   $state = '<span class="badge badge-danger">Fraud liquidation in progress</span>';
						   }elseif($row['currentState']==10){
							   $state = '<span class="badge badge-danger">Liquidation in progress</span>';
						   }elseif($row['currentState']==11){
							   $state = '<span class="badge badge-danger">Liquudated</span>';
						   }
						   
						   $Securing = '&nbsp;';
						   if($row['keepBond']>0 && $data){
							  $pr = ($row['keepBond'] * $data['ethereum']['btc'] / $row['lotsize']) * 100;	
							  $Securing = round($pr,2)." % (".round($row['keepBond'],2)." ETH)";
						   }
						   ?>
						   <tr>
								<td <?=empty($_REQUEST['sorting']) || $_REQUEST['sorting']=='created'?'':'class="d-none d-lg-table-cell d-xl-table-cell"'?>>
								<div class="max-90p"><a href="/deposit/<?= $row['depositContractAddress'] ?>" ><?= $m->fromNow()->getRelative()?> <small class="d-none d-sm-inline-block">(<?=date("M d, Y g:i a",strtotime($row['datetime'])) ?>)</small></a></div>
								</td>
								<td <?=empty($_REQUEST['sorting']) || $_REQUEST['sorting']=='updated'?'':'class="d-none d-lg-table-cell d-xl-table-cell"'?>>
								<div class="max-90p"><a href="/deposit/<?= $row['depositContractAddress'] ?>" ><?= $u->fromNow()->getRelative()?><?/* <small class="d-none d-sm-inline-block">(<?=date("M d, Y g:i a",strtotime($row['updated'])) ?>)</small>*/?></a></div>
								</td>
								<td class="d-none  d-xl-table-cell">
									<a href="https://etherscan.io/address/<?= $row['depositContractAddress'] ?>" target="_blank" rel="nofollow" class="d-inline-block float-left" style="margin-top:-2px;margin-right: 5px;" title="Open on Etherscan.io"><img src="/assets/images/etherscan.png" width="16"></a>
									<div class="max-90p d-inline-block"><?= $row['depositContractAddress'] ?></div>
									
								</td>
								<td>
									<?= $row['lotsize'] ?>
								</td>
								<td class="d-none d-lg-table-cell d-xl-table-cell">
									<?= $Securing ?>
								</td>
								<td class="deposit-state">
									<?=$state?>
									
								</td>
								<td class="d-none d-lg-table-cell d-xl-table-cell">
									<a href="/deposit/<?= $row['depositContractAddress'] ?>" class="btn btn-sm btn-primary">View details</a>
								</td>
							</tr>
					   <?endforeach?>
					   <?else:?>
					   <tr><td align="center" colspan="7"><b>List is empty</b></td></tr>
					   <?endif?>
				   </tbody>
				</table>
			</div>
		</div>
	</div>
	
	
</main>


<script type="text/javascript" src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
<script type="text/javascript">	
	$(document).on('submit','.email-confirmation',function(){
		var btn = $(this).find('.btn-info');
		$.post( $('.email-confirmation').data('url'), $('.email-confirmation').serialize(),function( data ) {
			$('.alert').remove();
			if(data.action=="get_code"){
				$('[name="email"]').attr('disabled','disabled');
				btn.before('<input type="text" class="form-control d-inline-block" placeholder="Enter code" name="code" required style="width:115px;" maxlength="5">&nbsp;');
				btn.html('Confirm');
				btn.attr('style',"margin-top: -6px;");
			}else if(data.action=="subscribe" && data.success==true){
				$('.js-subscribe_table .btn-primary').removeAttr('disabled');
				$('.js-subscribe_table input[type="checkbox"]').removeAttr('disabled');
				$('.js-subscribe_table input[type="number"]').removeAttr('disabled');
				$('.email-confirmation .btn').removeClass('btn-info').addClass('btn-warning');
				$('.email-confirmation .btn').html('Unsubscribe');
				$('.email-confirmation .btn').removeAttr('style');
				$('.email-confirmation .btn').after('<input type="hidden" name="unsubscribe" value="Y">');
				$('.email-confirmation input[name="code"]').remove();
			}else if(data.action=="wrong_code" && data.success==false){
				$('.js-subscribe_table').before('<div class="alert alert-danger">The verification code is wrong!</div>');
			}else if(data.action=="unsubscribe" && data.success==true){
				location.reload();
			}
		},'json');
		
		return false;
	});
	
	
	$(document).on('submit','.email-configuration',function(){
		$('.alert').remove();
		$.post( $('.email-configuration').data('url'), $('.email-configuration').serialize(),function( data ) {
			$('.js-subscribe_table').first().before('<div class="alert alert-success">Subscription configuration saved!</div>');
		},'json');
		return false;
	});
	
	
	async function getTotalETHRewards(address) {
		var url =  $('.js--keep').data('url');
		var KeepRandomBeaconOperatorStatisticsContract = new window.web3.eth.Contract(window.KeepRandomBeaconOperatorStatisticsABI,KeepRandomBeaconOperatorStatisticsAddress);
		var totalRewardsBalance = 0;
		for (let groupIndex = 0; groupIndex < <?=$operator['maxGroups']?>; groupIndex++) {
			var awaitingRewards = await KeepRandomBeaconOperatorStatisticsContract.methods.awaitingRewards(address, groupIndex).call();
			totalRewardsBalance +=parseInt(awaitingRewards);
		}

		totalRewardsBalance = web3.utils.fromWei(totalRewardsBalance.toString());
		$('#js--eth_rewards').html(number_format(totalRewardsBalance,2) + " ETH");
		$.post( url, { rewards_eth: totalRewardsBalance, update_rewards_eth: "Y" }).done(function( data ) {

		});
	}
	
	window.addEventListener('load', async function() {
		if (typeof web3 !== 'undefined') {
			console.warn("Using web3 detected from external source. If you find that your accounts don't appear or you have 0 MetaCoin, ensure you've configured that source properly. If using MetaMask, see the following link. Feel free to delete this warning. :) http://truffleframework.com/tutorials/truffle-and-metamask")
			window.web3 = new Web3(web3.currentProvider);
		} else {

		}

		if($('#js--staked').length>0 && window.web3 ){
			var BondedECDSAKeepFactoryContract = new window.web3.eth.Contract(window.BondedECDSAKeepFactoryABI,BondedECDSAKeepFactoryAddress);
			var url =  $('.js--keep').data('url');
			BondedECDSAKeepFactoryContract.methods.balanceOf('<?=$operator['operator']?>').call().then(function(result){
				result = web3.utils.fromWei(result);
				if(result>0 && parseInt($('#js--staked').data('staked'))!=parseInt(result)){
					$('#js--staked').data('staked',parseInt(result));
					$('#js--staked').html(number_format(parseInt(result)) + " KEEP");
					
					$.post( url, { staked: result, updateStaked: "Y" }).done(function( data ) {

					});
				}
			});
		}
		
		if($('#js--bonding_avail').length>0 && window.web3 ){
			var KeepBondingContract = new window.web3.eth.Contract(window.KeepBondingABI,KeepBondingAddress);
			var url =  $('.js--keep').data('url');
			KeepBondingContract.methods.unbondedValue('<?=$operator['operator']?>').call().then(function(result){
				result = web3.utils.fromWei(result);
				if(result>0 && parseInt($('#js--bonding_avail').data('bonding_avail'))!=parseInt(result)){
					$('#js--bonding_avail').data('bonding_avail',result);
					$('#js--bonding_avail').html(number_format(result,2) + " ETH");
					
					$.post( url, { bonding_avail: result, updateBA: "Y" }).done(function( data ) {

					});
				}
			});
		}
		
		if($('#js--eth_rewards').length>0 && window.web3 ){
			getTotalETHRewards('<?=$operator['operator']?>');
		}
		
		if(window.web3){
			$('.js--need_web3').removeClass('d-none');
			var stakingContract = new window.web3.eth.Contract(window.TokenStakingABI,window.TokenStakingAddress);
			var BondedECDSAKeepFactoryContract = new window.web3.eth.Contract(window.BondedECDSAKeepFactoryABI,window.BondedECDSAKeepFactoryAddress);
			var KeepBondingContract = new window.web3.eth.Contract(window.KeepBondingABI,window.KeepBondingAddress);
			
			//console.log('Checking random beacon authorization');
			const beaconAuth = await stakingContract.methods.isAuthorizedForOperator('<?=$operator['operator']?>', window.KeepRandomBeaconOperatorAddress).call();
			if(beaconAuth) $('.js--beacon_auth').html('<span class="badge badge-success">yes</span>'); else $('.js--beacon_auth').html('<span class="badge badge-danger">no</span>'); 
 			//console.log(`beacon authorization: ${beaconAuth}`)
			
			var stakingContract = new window.web3.eth.Contract(window.TokenStakingABI,window.TokenStakingAddress);
			//console.log('Checking ECDSA/tBTC authorizations');
			const ecdsaAuth = await stakingContract.methods.isAuthorizedForOperator('<?=$operator['operator']?>', window.BondedECDSAKeepFactoryAddress).call();
			//console.log(`bonded ECDSA Keep factory authorization: ${ecdsaAuth}`)
			if(ecdsaAuth) $('.js--ecdsa_auth').html('<span class="badge badge-success">yes</span>'); else $('.js--ecdsa_auth').html('<span class="badge badge-danger">no</span>'); 
			
			const opEg = await BondedECDSAKeepFactoryContract.methods.isOperatorEligible('<?=$operator['operator']?>', window.systemContractAddress).call();
			//console.log(`operator eligible for tbtc sys contract: ${opEg}`)
			if(opEg) $('.js--is_eligible').html('<span class="badge badge-success">yes</span>'); else $('.js--is_eligible').html('<span class="badge badge-danger">no</span>'); 

			const sortitionPoolAddress = await BondedECDSAKeepFactoryContract.methods.getSortitionPool(window.systemContractAddress).call();
			const tbtcAuth = await KeepBondingContract.methods.hasSecondaryAuthorization('<?=$operator['operator']?>', sortitionPoolAddress).call();
			//console.log(`tBTC system authorization: ${tbtcAuth}`)
			if(tbtcAuth) $('.js--tbtc_auth').html('<span class="badge badge-success">yes</span>'); else $('.js--tbtc_auth').html('<span class="badge badge-danger">no</span>'); 
		}
	});
</script>

