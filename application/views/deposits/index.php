<script src="/assets/vendors/arcticmodal/jquery.arcticmodal-0.3.min.js"></script>
<link rel="stylesheet" href="/assets/vendors/arcticmodal/jquery.arcticmodal-0.3.css">
<link rel="stylesheet" href="/assets/vendors/arcticmodal/themes/simple.css">

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 mt-3">
	
	<?php $this->view('templates/menu-mobile'); ?>
	<?
	$client = new Codenixsv\CoinGeckoApi\CoinGeckoClient();
	$data = $client->simple()->getPrice('ethereum,bitcoin', 'usd,btc');
	?>
	<div class="container1 my-3">
		<div class="row">
			  <div class="col-md-12">
					<form class="form-horizontal" role="form" id="deposit-filter" action="/deposits/">
						<div class="row"> 
							  <div class="form-group col-md-12 col-sm-12 col-lg-2">
								<label for="filter">Current state</label>
								<select class="form-control" name="state">
									<option value="" <?=empty($_REQUEST['state'])?'selected':''?>>any</option>
									<option value="1" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==1?'selected':''?>>Awaiting signer setup</option>
									<option value="2" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==2?'selected':''?>>Awaiting BTC funding proof</option>
									<option value="3" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==3?'selected':''?>>Failed</option>
									<option value="4" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==4?'selected':''?>>Active</option>
									<option value="5" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==5?'selected':''?>>Awaiting withdrawal signature</option>
									<option value="6" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==6?'selected':''?>>Awaiting withdrawal proof</option>
									<option value="7" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==7?'selected':''?>>Redeemed</option>
									<option value="8" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==8?'selected':''?>>Courtesy call</option>
									<option value="9" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==9?'selected':''?>>Fraud liquidation in progress</option>
									<option value="10" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==10?'selected':''?>>Liquidation in progress</option>
									<option value="11" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==11?'selected':''?>>Liquidated</option>
								</select>
							  </div>
							  <div class="form-group col-md-12 col-sm-12 col-lg-2">
								<label for="contain">Deposit contract</label>
								<input class="form-control" name="depositAddr" type="text" value="<?=@htmlspecialchars($_REQUEST['depositAddr'])?>"/>
							  </div>
							  <div class="form-group col-md-12 col-sm-12 col-lg-2">
								<label for="contain">Address search <a href="javascript:void(0)" title="Search by Keep Contract, Bitcoin address, Bitcoin Transaction Hash" data-toggle="tooltip"><span data-feather="help-circle" ></span></a></label>
								<input class="form-control" name="s" type="text" value="<?=@htmlspecialchars($_REQUEST['s'])?>"/>
							  </div>
							  <div class="form-group col-md-12 col-sm-12 col-lg-2">
								<label for="contain">Lotsize</label>
								<select class="form-control" name="lotsize">
									<option value="" <?=empty($_REQUEST['lotsize'])?'selected':''?>>any</option>
									<?if(!empty($lotsizes)) foreach($lotsizes as $lot):?>
										<option value="<?=$lot['lotsize']?>" <?=!empty($_REQUEST['lotsize']) && $_REQUEST['lotsize']==$lot['lotsize']?'selected':''?>><?=$lot['lotsize']?></option>
									<?endforeach?>
								</select>
							  </div>
							  <div class="form-group col-md-12 col-sm-12 col-lg-2">
								<label for="contain">Collateralization less than (%)</label>
								<input class="form-control" name="ctn" type="number" value="<?=@htmlspecialchars($_REQUEST['ctn'])?>"/>
							  </div>
							  <div class="form-group  col-md-6 col-sm-6 col-lg-2" style="padding-top:28px;">
								<label for="contain">&nbsp;</label>
								<button type="submit" class="btn btn-primary d-inline-block" title="Apply filter"><span data-feather="search"></span> Apply</button>
								<?if(!empty($_GET)):?>
								<a class="btn btn-secondary d-inline-block" href="/deposits/" title="Reset filter"><span data-feather="slash"></span></a>
								<?endif?>
							  </div>
						</div>
						<div class="row">
							  <div class="form-group  col-md-6 col-sm-6 col-lg-12">
								<a href="https://dapp.tbtc.network/deposit" target="_blank" class="btn btn-sm1 btn-info d-block" title="Make new deposit" style="max-width:150px;"><span data-feather="plus"></span> Deposit</a>
							  </div>
						</div>
					</form>
			  </div>
		</div>
	</div>
	
	
	<div class="table-responsive pb-3" style="background:#fff;">
		<table  class="table table-striped table-bordered table-compact" >
		   <thead>
				<tr>
					<th style="max-width:250px;">Created</th>
					<th class="d-none d-lg-table-cell d-xl-table-cell">Deposit contract</th>
					<th>Lot Size</th>
					<th>Securing</th>
					<th style="max-width:240px;">State</th>
					<th style="width:105px;"></th>
				</tr>
		   </thead>
		   <tbody>
			   <?if(count($items)>0):?>
			   <?php foreach ($items as  $row) :?>
				   <?
				   $m = new \Moment\Moment($row['datetime']);
				   $state = '<span class="badge badge-secondary">Processing</span>';
				  
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
					   $state = '<span class="badge badge-danger">Failed</span>';
				   }elseif($row['currentState']==4){
					   $state = '<span class="badge badge-success">Active</span>';
				   }elseif($row['currentState']==5){
					   $state = '<span class="badge badge-warning">Awaiting withdrawal signature</span>';
				   }elseif($row['currentState']==6){
					   $state = '<span class="badge badge-warning">Awaiting withdrawal proof</span>';
				   }elseif($row['currentState']==7){
					   $state = '<span class="badge badge-dark">Redeemed</span>';
				   }elseif($row['currentState']==8){
					   $state = '<span class="badge badge-warning">Courtesy call</span>';
				   }elseif($row['currentState']==9){
					   $state = '<span class="badge badge-danger">Fraud liquidation in progress</span>';
				   }elseif($row['currentState']==10){
					   $state = '<span class="badge badge-warning">Liquidation in progress</span>';
				   }elseif($row['currentState']==11){
					   $state = '<span class="badge badge-secondary">Liquudated</span>';
				   }
				   
				   $Securing = '&nbsp;';
				   if($row['keepBond']>0 && $data){
					  $pr = ($row['keepBond'] * $data['ethereum']['btc'] / $row['lotsize']) * 100;	
					  $Securing = round($pr,2)." % (".round($row['keepBond'],2)." ETH)";
				   }
				   ?>
				   <tr>
						<td>
						<div class="max-90p"><?= $m->fromNow()->getRelative()?> <small class="d-none d-sm-inline-block">(<?=date("M d, Y g:i a",strtotime($row['datetime'])) ?>)</small></div>
						</td>
						<td class="d-none d-lg-table-cell d-xl-table-cell">
							<div class="max-90p"><a href="https://etherscan.io/address/<?= $row['depositContractAddress'] ?>" target="_blank" rel="nofollow"><?= $row['depositContractAddress'] ?></a></div>
						</td>
						<td>
							<?= $row['lotsize'] ?>
						</td>
						<td>
							<?= $Securing ?>
						</td>
						<td class="deposit-state">
							<?=$state?>
						</td>
						<td>
							<a href="/deposit/<?= $row['depositContractAddress'] ?>" class="btn btn-sm btn-primary">View details</a>
						</td>
					</tr>
			   <?endforeach?>
			   <?else:?>
			   <tr><td align="center" colspan="6"><b>List is empty</b></td></tr>
			   <?endif?>
		   </tbody>
		</table>
		
		<nav aria-label="Page navigation example">
		  <ul class="pagination">
				<?php echo $this->pagination->create_links(); ?>
		  </ul>
		</nav>
    </div>
</main>

<script type="text/javascript">
	$(document).on('submit','#deposit-filter',function(){
		$(this).find('input,select').each(function(){
			if($(this).val()=="")$(this).attr("disabled","disabled");
		});
	});
	$(document).on('click','.js-submit',function(){
		$('input[name="depositAddr"]').val($('input[name="_depositAddr"]').val());
		$('#deposit-filter').submit();
	});
</script>
<?/*
<script type="text/javascript" src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
<script type="text/javascript">
	
	async function getState(contract){
		
	}
		
	window.addEventListener('load', function() {
		
		if (typeof web3 !== 'undefined') {
			console.warn("Using web3 detected from external source. If you find that your accounts don't appear or you have 0 MetaCoin, ensure you've configured that source properly. If using MetaMask, see the following link. Feel free to delete this warning. :) http://truffleframework.com/tutorials/truffle-and-metamask")
			// Use Mist/MetaMask's provider
			window.web3 = new Web3(web3.currentProvider);
		} else {
			console.warn("No web3 detected. Falling back to http://127.0.0.1:8545. You should remove this fallback when you deploy live, as it's inherently insecure. Consider switching to Metamask for development. More info here: http://truffleframework.com/tutorials/truffle-and-metamask");
			window.web3 = new Web3(new Web3.providers.HttpProvider(window.endpoint));
		}
		
		//var systemContract =  new web3.eth.Contract(window.systemContractABI, window.systemContractAddress);
		//var TokenContract  =  new web3.eth.Contract(window.tokenContractABI, window.tokenContractAddress);

		
		$(document).on("click",".js-view",function(e){
			e.preventDefault();
			var depositContractAddress = $(this).data('daddr');
			var keepAddress= $(this).data('keepaddr');
			
			var depositContract =  new window.web3.eth.Contract(window.depositContractABI,depositContractAddress);
			var keepContract = new window.web3.eth.Contract(window.keepContractABI,keepAddress);
			
			
			//state
			depositContract.methods.currentState().call().then(function(result){
				console.log(result);
			});
			
			
		});
	});
</script>
*/?>
