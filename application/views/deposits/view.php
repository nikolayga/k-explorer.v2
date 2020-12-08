

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 mt-3 ">
	
	
	<?php $this->view('templates/menu-mobile'); ?>
	<h3 class="mb-3">
	  View deposit details
	</h3>
	<?
	   $state = '<span class="badge badge-secondary">Processing</span>';
	   $minted = '';
	   if($deposit['currentState']==0){
			$state = '<span class="badge badge-warning">Start</span>';
	   }elseif($deposit['currentState']==1){
		   $state = '<span class="badge badge-warning">Awaiting signer setup</span>';
	   }elseif($deposit['currentState']==2){
		   if(!$deposit['bitcoinTransaction']){
			   $state = '<span class="badge badge-warning">Awaiting Funding</span>';
		   }else{
			   $conf_detail = '('.intval($deposit['bitcoinConfirmations']).'/6)';
			   if(!empty($deposit['bitcoinTransaction'])){
				   $conf_detail  = '<a href="https://www.blockchain.com/btc/tx/'.$deposit['bitcoinTransaction'].'" target="_blank">'.$conf_detail .'</a>';
			   }
			   $state = '<span class="badge badge-warning">Awaiting BTC funding proof '.$conf_detail.'</span>';
		   }
	   }elseif($deposit['currentState']==3){
		   $state = '<span class="badge badge-secondary">Failed</span>';
	   }elseif($deposit['currentState']==4){
		   if(isset($deposit['isMinted']) && $deposit['isMinted']==1)  $minted = '&nbsp;<img src="/assets/images/tbtc_32.png" width="16" title="tBTC minted" style="argin-top: -2px;">';
		   $state = '<span class="badge badge-success">Active</span>';
	   }elseif($deposit['currentState']==5){
		   $state = '<span class="badge badge-warning">Awaiting withdrawal signature</span>';
	   }elseif($deposit['currentState']==6){
		   $state = '<span class="badge badge-warning">Awaiting withdrawal proof</span>';
	   }elseif($deposit['currentState']==7){
		   $state = '<span class="badge badge-dark">Redeemed</span>';
	   }elseif($deposit['currentState']==8){
		   $state = '<span class="badge badge-danger">Courtesy call</span>';
	   }elseif($deposit['currentState']==9){
		   $state = '<span class="badge badge-danger">Fraud liquidation in progress</span>';
	   }elseif($deposit['currentState']==10){
		   $state = '<span class="badge badge-danger">Liquidation in progress</span>';
	   }elseif($deposit['currentState']==11){
		   $state = '<span class="badge badge-danger">Liquudated</span>';
	   }
	?>
	<div class="row row-eq-height">
		<div class="col-md-12 col-lg-12 col-xl-6">
			<div class="card h-100">
			  <div class="card-body">
				<h4 class="card-title">Deposit details</h4>
				<div class="table-responsive">
				<table class="table table-sm table-hover table-striped table-compact">
				  <tbody>
					<tr>
					  <th scope="row">Current state</th>
					  <td id="js--currentState" data-state="<?=$deposit['currentState']?>"><?= $state?><?=$minted?> <?if($deposit['currentState']==4 && $deposit['isMinted']==1):?><a href="https://dapp.tbtc.network/deposit/<?=$deposit['depositContractAddress']?>/redeem" target="_blank" class="btn btn-sm btn-info float-right">Redeem</a><?endif?></td>
					</tr>
					<tr>
					  <th scope="row">Deposit contract</th>
					  <td><div class="max-90p"><a href="https://etherscan.io/address/<?=$deposit['depositContractAddress']?>" target="_blank"><?=$deposit['depositContractAddress']?></a></div></td>
					</tr>
					<tr>
					  <th scope="row">Lot size</th>
					  <td><?=$deposit['lotsize']?> BTC</td>
					</tr>
					<tr>
					  <th scope="row">Created</th>
					  <td>
						  <?=date("M d, Y g:i a",strtotime($deposit['datetime'])) ?>&nbsp;
						  <?if(!empty($systemCreated['txhash'])):?><a href="https://etherscan.io/tx/<?=$systemCreated['txhash']?>" target="_blank">txhash</a><?endif?>
					  </td>
					</tr>
					<?if(!empty($deposit['updated'])):?>
					<tr>
					  <th scope="row">Updated</th>
					  <td><?=date("M d, Y g:i a",strtotime($deposit['updated'])) ?></td>
					</tr>
					<?endif?>
					<?if(!empty($deposit['mintedBy'])):?>
					<tr>
					  <th scope="row">Created by</th>
					  <td><div class="max-90p"><a href="https://etherscan.io/address/<?=$deposit['mintedBy']?>" target="_blank"><?=$deposit['mintedBy']?></a></div></td>
					</tr>
					<?endif?>
					
					<?if(!empty($deposit['bitcoinAddress'])):?>
					<tr>
					  <th scope="row">BTC address</th>
					  <td><div class="max-90p"><a href="https://www.blockchain.com/btc/address/<?=$deposit['bitcoinAddress']?>" target="_blank"><?=$deposit['bitcoinAddress']?></a></div></td>
					</tr>
					<?endif?>
					<?if(!empty($deposit['bitcoinTransaction'])):?>
					<tr>
					  <th scope="row">BTC transaction</th>
					  <td><div class="max-90p"><a href="https://www.blockchain.com/btc/tx/<?=$deposit['bitcoinTransaction']?>" target="_blank">txhash</a></div></td>
					</tr>
					<?endif?>
				  </tbody>
				</table>
				 </div>
			  </div>
			</div>
		</div>
		<div class="col-md-12 col-lg-12 col-xl-6">
			<div class="card h-100">
			  <div class="card-body">
				<h4 class="card-title">Collateralization</h4>
				<div class="table-responsive">
				<table class="table table-sm table-hover table-striped js--keep" data-keepaddr="<?=$deposit['keepAddress']?>" data-url="/<?=uri_string()?>">
				  <tbody>
					<tr>
					  <th scope="row">Keep Contract address</th>
					  <td><div class="max-90p"><a href="https://etherscan.io/address/<?=$deposit['keepAddress']?>" target="_blank"><?=$deposit['keepAddress']?></a></div></td>
					</tr>
					<tr>
					  <th scope="row">Members</th>
					  <td class="">
						  <div class="max-90p  <?=empty($deposit['keepMembers'])?'js--keepMembers':''?>">
							  <?if(!empty($deposit['keepMembers'])):?>
								  <?$deposit['keepMembers'] = explode(",",$deposit['keepMembers']);?>
								  <?foreach($deposit['keepMembers'] as $member):?>
									<a href="/operators/<?=$member?>" target="_blank"><?=$member?></a><br>
								  <?endforeach?>
							  <?else:?>
							  ...
							  <?endif?>
						  </div>
					  </td>
					</tr>
					<?if(!empty($deposit['keepBond']) || $deposit['currentState']==4):?>
					<tr>
					  <th scope="row">Bond</th>
					  <td class="">
						  <div class="max-90p  <?=empty($deposit['keepBond']) && $deposit['currentState']==4?'js--keepBond':''?>">
							  <?if(!empty($deposit['keepBond'])):?>
								  <?=round($deposit['keepBond'],2)?> ETH
							  <?else:?>
							  ...
							  <?endif?>
						  </div>
					  </td>
					</tr>
					<?endif?>
					
					<?if(!empty($deposit['keepBond'])):?>
					<?
					$client = new Codenixsv\CoinGeckoApi\CoinGeckoClient();
					$data = $client->simple()->getPrice('ethereum,bitcoin', 'usd,btc');
					$pr = ($deposit['keepBond'] * $data['ethereum']['btc'] / $deposit['lotsize']) * 100;
					
					$courtesy_price = (125/100*$deposit['lotsize']) / $deposit['keepBond'];
					$liquidation_price = (110/100*$deposit['lotsize']) / $deposit['keepBond'];
					?>
	
					<tr>
					  <th scope="row">Current Collateralization</th>
					  <td class="">
						 <?=round($pr,2)?>
					  </td>
					</tr>
					<tr>
					  <th scope="row">Current ETH price</th>
					  <td class="">
						  
						  <?=round($data['ethereum']['btc'],3)?> BTC
					  </td>
					</tr>
					<tr>
					  <th scope="row">Courtesy call ETH price</th>
					  <td class="text-warning">
						 <?=round($courtesy_price,3)?> BTC / <?=round($courtesy_price *$data['bitcoin']['usd'] ,3)?> USD  (125% Collateralization)
					  </td>
					</tr>
					<tr>
					  <th scope="row">Liquidation ETH price</th>
					  <td class="text-danger">
						 <?=round($liquidation_price,3)?> BTC / <?=round($liquidation_price *$data['bitcoin']['usd'] ,3)?> USD (110% Collateralization)
					  </td>
					</tr>
					<?endif?>
				  </tbody>
				</table>
				</div>
			  </div>
			</div>
		</div>
	</div>
	
	<hr>
	
	<div class="row row-eq-height">
		<div class="col-md-12 col-lg-12 col-xl-6">
			<div class="card h-100">
			  <div class="card-body">
				<h4 class="card-title">Subscribe to deposit events</h4>
				<?if(empty($_SESSION['AUTH'])):?>
				<div class=" text-center"><a href="" class="btn btn-primary btn-lg js-auth">Connect With MetaMask</a></div>
				
				<p class="mt-3">To be able to subscribe, please connect with your crypto wallet</p>
				<?else:?>
					<table class="table table-sm table-compact js-subscribe_table mb-0">
						<tr>
							  <td>
								<form action="" method="POST" class="email-confirmation" data-url="/ajax/subscribe-confirmation/">
									<input type="hidden" name="contractAddress" value="<?=htmlspecialchars($deposit['depositContractAddress'])?>">
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
						<input type="hidden" name="contractAddress" value="<?=htmlspecialchars($deposit['depositContractAddress'])?>">
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
							  <td align="center"><button type="submit" class="btn btn-primary mt-3" <?=empty($subscribe)?'disabled':''?> >Save subscribe configuration</button></td>
							</tr>
						</table>
					</form>

				<?endif?>
			  </div>
			</div>
		</div>
	</div>
	<a href="/deposits/" class="d-block mt-3"> Back to deposits</a>

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
	
	window.addEventListener('load', function() {
		if (typeof web3 !== 'undefined') {
			console.warn("Using web3 detected from external source. If you find that your accounts don't appear or you have 0 MetaCoin, ensure you've configured that source properly. If using MetaMask, see the following link. Feel free to delete this warning. :) http://truffleframework.com/tutorials/truffle-and-metamask")
			window.web3 = new Web3(web3.currentProvider);
		} else {
			console.warn("No web3 detected. Falling back to http://127.0.0.1:8545. You should remove this fallback when you deploy live, as it's inherently insecure. Consider switching to Metamask for development. More info here: http://truffleframework.com/tutorials/truffle-and-metamask");
			window.web3 = new Web3(new Web3.providers.HttpProvider(window.endpoint));
		}
		
		if($('.js--keepMembers').length>0 && window.web3 ){
			var keepAddress  = $('.js--keep').data('keepaddr');
			var keepContract = new window.web3.eth.Contract(window.keepContractABI,keepAddress);
			var url =  $('.js--keep').data('url');
			keepContract.methods.getMembers().call().then(function(result){
				$.post( url, { members: result, addMembers: "Y" }).done(function( data ) {
					var arr = new Array;
					result.forEach(function(item, i, arr) {
					 arr.push('<a href="https://etherscan.io/address/'+item+'" target="_blank">'+item+'</a>');
					});
					$('.js--keepMembers').html(arr.join("<br>"));
					$('.js--keepMembers').removeClass('js--keepMembers');
				});
			});
		}
		
		if($('.js--keepBond').length>0 && window.web3 ){
			var keepAddress  = $('.js--keep').data('keepaddr');
			var keepContract = new window.web3.eth.Contract(window.keepContractABI,keepAddress);
			var url =  $('.js--keep').data('url');
			keepContract.methods.checkBondAmount().call().then(function(result){
				var result = web3.utils.fromWei(result, 'ether')
				if(result>0){
					$.post( url, { bond: result, addBond: "Y" }).done(function( data ) {
						$('.js--keepBond').html(result + " ETH");
						$('.js--keepBond').removeClass('js--keepMembers');
					});
				}
			});
		}
		
		if($('#js--currentState').length>0 && window.web3 ){
			var depositAddress  = '<?=$deposit['depositContractAddress']?>';
			var depositContract = new window.web3.eth.Contract(window.depositContractABI,depositAddress);
			var url =  $('.js--keep').data('url');
			depositContract.methods.currentState().call().then(function(result){
				if(result>0 && parseInt($('#js--currentState').data('state'))!=parseInt(result)){
					$.post( url, { state: result, updateState: "Y" }).done(function( data ) {
						location.reload();
					});
				}
			});
		}
	});
</script>

