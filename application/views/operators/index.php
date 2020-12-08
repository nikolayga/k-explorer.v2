<script src="/assets/vendors/arcticmodal/jquery.arcticmodal-0.3.min.js"></script>
<link rel="stylesheet" href="/assets/vendors/arcticmodal/jquery.arcticmodal-0.3.css">
<link rel="stylesheet" href="/assets/vendors/arcticmodal/themes/simple.css">

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-2 mt-3">
	
	<h1>Operators</h1>

	<?php $this->view('templates/menu-mobile'); ?>
	<?
	$client = new Codenixsv\CoinGeckoApi\CoinGeckoClient();
	$data = $client->simple()->getPrice('ethereum,bitcoin', 'usd,btc');
	?>
	<div class="my-3">
		<form class="form-horizontal" role="form" id="deposit-filter" action="/operators/">
			<div class="row mx-0"> 
				  <div class="form-group col-md-12 col-sm-12 col-lg-2 mb-2">
					<label for="contain">Operator address</label>
					<input class="form-control" name="operatorAddr" type="text" value="<?=@htmlspecialchars($_REQUEST['operatorAddr'])?>"/>
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-1 mb-2">
					<label for="filter">Deposits state</label>
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
					<label for="contain">Has collateralization less than (%)</label>
					<input class="form-control" name="ctn" type="number" value="<?=@htmlspecialchars($_REQUEST['ctn'])?>"/>
				  </div>
				  <div class="form-group  col-md-6 col-sm-6 col-lg-2 mb-2" style="padding-top:28px;">
					<button type="submit" class="btn btn-primary d-inline-block" title="Apply filter"><span data-feather="search"></span> Apply</button>
					<?if(!empty($_GET)):?>
					<a class="btn btn-secondary d-inline-block" href="/operators/" title="Reset filter"><span data-feather="slash"></span></a>
					<?endif?>
				  </div>
			</div>
			
		</form>
	</div>
	

	<div class="row mx-0 mb-3">	
	  <div class="col-12">	
			<div class="row">
				<div class="col-lg-3 col-md-6 col-sm-6">
					<div class="card1 card-stats mb-0 mt-1">
						<div class="card-body ">
							<div class="row">
								<div class="col-12">
									<div class="numbers">
										<p class="card-category">Total operators</p>
										<p class="card-title"><?=$total?></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6">
					<div class="card1 card-stats mb-0 mt-1">
						<div class="card-body ">
							<div class="row">
								<div class="col-12">
									<div class="numbers">
										<p class="card-category">Total staked</p>
										<p class="card-title"><?=number_format($total_staked)?> <small>KEEP</small></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6">
					<div class="card1 card-stats mb-0 mt-1">
						<div class="card-body ">
							<div class="row">
								<div class="col-12">
									<div class="numbers">
										<p class="card-category">Total bonded</p>
										<p class="card-title"><?=number_format($total_bonded)?> <small>ETH</small></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6">
					<div class="card1 card-stats mb-0 mt-1">
						<div class="card-body ">
							<div class="row">
								<div class="col-12">
									<div class="numbers">
										<p class="card-category">Available for bonding</p>
										<p class="card-title"><?=number_format($bond_avail)?> <small>ETH</small></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	  </div>
	</div>

	
	<div class="table-responsive pb-3" style="background:#fff;">
		<table  class="table table-striped table-bordered table-compact js--data-table table-sm" data-sort-col="2">
		   <thead>
				<tr>
					<th>Staked At</th>
					<th class="d-none d-xl-table-cell">Operator</th>
					<th>Deposits</th>
					<th class="d-none d-lg-table-cell d-xl-table-cell">ETH Bonded</th>
					<th>ETH Available</th>
					<th style="width:105px;" class="d-none d-lg-table-cell d-xl-table-cell">KEEP Staked</th>
				</tr>
		   </thead>
		   <tbody>
			   <?if(count($items)>0):?>
			   <?php foreach ($items as  $row) :?>
				   <?
				   $m = new \Moment\Moment($row['saked_at']);
				   ?>
				   <tr>
						<td <?=empty($_REQUEST['sorting']) || $_REQUEST['sorting']=='created'?'':'class="d-none d-lg-table-cell d-xl-table-cell"'?>>
							<div class="max-90p"><?= $m->fromNow()->getRelative()?></div>
						</td>
						<td>
							<div class="max-90p"><a href="/operators/<?= $row['operator'] ?>" ><?= $row['operator']?></a></div>
						</td>
						<td data-order="<?= $row['deposits'] ?>">
							<?= $row['deposits'] ?>
						</td>
						<td data-order="<?= $row['bonded'] ?>">
							ETH <?= number_format($row['bonded']) ?>
						</td>
						<td data-order="<?= $row['availBond'] ?>">
							ETH <?= number_format($row['availBond']) ?>
						</td>
						<td data-order="<?= $row['staked'] ?>">
							KEEP <?= number_format($row['staked']) ?>
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

<script type="text/javascript" src="https://cdn.jsdelivr.net/gh/ethereum/web3.js@1.0.0-beta.34/dist/web3.min.js"></script>
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
