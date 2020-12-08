<script src="/assets/vendors/arcticmodal/jquery.arcticmodal-0.3.min.js"></script>
<link rel="stylesheet" href="/assets/vendors/arcticmodal/jquery.arcticmodal-0.3.css">
<link rel="stylesheet" href="/assets/vendors/arcticmodal/themes/simple.css">

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-2 mt-3">
	
	<h1>Deposits history <small class="text-muted" style="font-size:40%">Total <?=$total?></small> <a href="https://dapp.tbtc.network/deposit" target="_blank" title="Make new deposit" style="" class="btn btn-sm btn-info ml-3 d-none d-lg-inline-block d-xl-inline-block d-md-inline-block"><span data-feather="plus"></span></a></h1>

	<?php $this->view('templates/menu-mobile'); ?>
	<?
	$client = new Codenixsv\CoinGeckoApi\CoinGeckoClient();
	$data = $client->simple()->getPrice('ethereum,bitcoin', 'usd,btc');
	?>
	<div class="my-3">
		<form class="form-horizontal" role="form" id="deposit-filter" action="/deposits/">
			<div class="row mx-0"> 
				  <div class="form-group col-md-12 col-sm-12 col-lg-1 mb-2">
					<label for="filter">Current state</label>
					<select class="form-control" name="state">
						<option value="" <?=empty($_REQUEST['state'])?'selected':''?>>any</option>
						<option value="1" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==1?'selected':''?>>Awaiting signer setup</option>
						<option value="2" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==2?'selected':''?>>Awaiting BTC funding proof</option>
						<option value="3" <?=!empty($_REQUEST['state']) && $_REQUEST['state']==3?'selected':''?>>Failed setup</option>
						<option value="f" <?=!empty($_REQUEST['state']) && $_REQUEST['state']=='f'?'selected':''?>>Funding timeout</option>
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
				  <div class="form-group col-md-12 col-sm-12 col-lg-2 mb-2">
					<label for="contain">Deposit contract</label>
					<input class="form-control" name="depositAddr" type="text" value="<?=@htmlspecialchars($_REQUEST['depositAddr'])?>"/>
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-2 mb-2">
					<label for="contain">Operator address</label>
					<input class="form-control" name="operatorAddr" type="text" value="<?=@htmlspecialchars($_REQUEST['operatorAddr'])?>"/>
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-1 mb-2">
					<label for="contain">Search <a href="javascript:void(0)" title="Search by Keep Contract, Bitcoin address, Bitcoin Transaction Hash" data-toggle="tooltip"><span data-feather="help-circle" ></span></a></label>
					<input class="form-control" name="s" type="text" value="<?=@htmlspecialchars($_REQUEST['s'])?>"/>
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-1 mb-2">
					<label for="contain">Is minted</label>
					<select class="form-control" name="isminted">
						<option value="" <?=empty($_REQUEST['isminted'])?'selected':''?>>any</option>
						<option value="1" <?=!empty($_REQUEST['isminted']) && $_REQUEST['isminted']==1?'selected':''?>>yes</option>
						<option value="-1" <?=!empty($_REQUEST['isminted']) && $_REQUEST['isminted']==-1?'selected':''?>>no</option>
					</select>
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-1 mb-2">
					<label for="contain">Lotsize</label>
					<select class="form-control" name="lotsize">
						<option value="" <?=empty($_REQUEST['lotsize'])?'selected':''?>>any</option>
						<?if(!empty($lotsizes)) foreach($lotsizes as $lot):?>
							<option value="<?=$lot['lotsize']?>" <?=!empty($_REQUEST['lotsize']) && $_REQUEST['lotsize']==$lot['lotsize']?'selected':''?>><?=$lot['lotsize']?></option>
						<?endforeach?>
					</select>
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-2 mb-2">
					<label for="contain">Collateralization less than (%)</label>
					<input class="form-control" name="ctn" type="number" value="<?=@htmlspecialchars($_REQUEST['ctn'])?>"/>
				  </div>
				  <div class="form-group  col-md-6 col-sm-6 col-lg-2 mb-2" style="padding-top:28px;">
					<button type="submit" class="btn btn-primary d-inline-block" title="Apply filter"><span data-feather="search"></span> Apply</button>
					<?if(!empty($_GET)):?>
					<a class="btn btn-secondary d-inline-block" href="/deposits/" title="Reset filter"><span data-feather="slash"></span></a>
					<?endif?>
				  </div>
			</div>
			<div class="row mx-0 d-block d-sm-none">
				 <div class="form-group col-md-12 col-sm-12 col-lg-2 mb-2">
					<label for="contain">Sorting</label>
					<select class="form-control" name="sorting">
						<option value="created" <?=empty($_REQUEST['sorting']) || $_REQUEST['sorting']=='created'?'selected':''?>>Create date</option>
						<option value="updated" <?=!empty($_REQUEST['sorting']) && $_REQUEST['sorting']=='updated'?'selected':''?>>Update date</option>
					</select>
				  </div>
			</div>
		</form>
	</div>
	
	<?if($found!=$total):?>
	<h5>Total found: <?=$found?></h5>
	<?endif?>
	<div class="table-responsive pb-3" style="background:#fff;">
		<table  class="table table-striped table-bordered table-compact" >
		   <thead>
				<tr>
					<th <?=empty($_REQUEST['sorting']) || $_REQUEST['sorting']=='created'?'':'class="d-none d-lg-table-cell d-xl-table-cell"'?>><a href="" class="js--sort" data-sort="created" style="color:#000;" title="Sort by create date">Created <?=empty($_REQUEST['sorting']) || $_REQUEST['sorting']=='created'?'<span data-feather="arrow-down"></span>':''?></a></th>
					<th <?=empty($_REQUEST['sorting']) || $_REQUEST['sorting']=='updated'?'':'class="d-none d-lg-table-cell d-xl-table-cell"'?>><a href="" class="js--sort" data-sort="updated" style="color:#000;" title="Sort by update date">Updated <?=!empty($_REQUEST['sorting']) && $_REQUEST['sorting']=='updated'?'<span data-feather="arrow-down"></span>':''?></a></th>
					<th class="d-none d-xl-table-cell">Deposit contract</th>
					<th>Lot Size</th>
					<th class="d-none d-lg-table-cell d-xl-table-cell">Collateralization</th>
					<th >State</th>
					<th style="width:105px;" class="d-none d-lg-table-cell d-xl-table-cell"></th>
				</tr>
		   </thead>
		   <tbody>
			   <?if(count($items)>0):?>
			   <?php foreach ($items as  $row) :?>
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
	
	$(document).on('change','select[name="sorting"]',function(){
		$('#deposit-filter').trigger('submit');
	});
	
	$(document).on('click','a.js--sort',function(e){
		e.preventDefault();
		var sort = $(this).data('sort');
		$('select[name="sorting"]').val(sort);
		$('select[name="sorting"]').trigger('change');
	});
	
	
</script>
