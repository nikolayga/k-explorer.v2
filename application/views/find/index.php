<script src="/assets/vendors/arcticmodal/jquery.arcticmodal-0.3.min.js"></script>
<link rel="stylesheet" href="/assets/vendors/arcticmodal/jquery.arcticmodal-0.3.css">
<link rel="stylesheet" href="/assets/vendors/arcticmodal/themes/simple.css">

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 mt-3">
	
	<?php $this->view('templates/menu-mobile'); ?>
	<h2>Find deposit by etherium address</h2>
	
	<?if(!empty($items)):?>
	<div class="table-responsive pb-3" style="background:#fff;">
		<table id="deposites" class="table table-striped table-bordered" >
		   <thead>
				<tr>
					<th>Date/Txhash</th>
					<th class="no-sort">Info</th>
					<th style="width:170px;" class="no-sort">Lot Size / Fee (tBTC)</th>
					<th style="width:120px;">State</th>
				</tr>
		   </thead>
		   <tbody>
			   <?php foreach ($items as  $row) :?>
				   <?
				   $m = new \Moment\Moment($row['date']);
				   ?>
				   <tr>
					<td data-order="<?=strtotime($row['date']) ?>">
						<div class="max-90p"><a href="https://etherscan.io/tx/<?= $row['txhash'] ?>" target="_blank" rel="nofollow"><?= $row['txhash'] ?></a></div>
						<?= $m->fromNow()->getRelative()?> <small>( <?=date("M d, Y g:i a",strtotime($row['date'])) ?> ) </small>
					</td>
					<td>
						<div class="clearfix">
							<div class="float-left">Owner Addr &nbsp;</div>
							<div class="max-70p float-left">
								<a href="https://etherscan.io/address/<?= $row['to'] ?>" target="_blank"><?= $row['to'] ?></a>
							</div>
						</div>
						<div class="clearfix">
							<div class="float-left">Deposit Addr &nbsp;</div>
							<div class="max-70p float-left">
								<a href="https://etherscan.io/address/<?= $row['_depositContractAddress'] ?>" target="_blank" rel="nofollow"><?=$row['_depositContractAddress'] ?></a>
							</div>
						</div>
						<div class="clearfix">
							<div class="float-left">Keep Addr &nbsp;</div>
							<div class="max-70p float-left">
								<a href="https://etherscan.io/address/<?= $row['_keepAddress'] ?>" target="_blank" rel="nofollow"><?=$row['_keepAddress'] ?></a>
							</div>
						</div>
					</td>
					<td>
						<div class="legend">Value <?= $row['value'] ?> tBTC</div>	
						<div class="legend">Fee <?= $row['value'] ?> tBTC</div>	
					</td>
					<td data-order="<? if($row['isFunded']=='Funded'){ ?>1<?}else{?>0<?}?>">
						<div class="float-left">
							<? if($row['isFunded']=='Funded'){ ?>
							<div class="legend"><i class="fa fa-circle text-success"></i> Funded/Minted </div>	
							<?}?>
							<? if($row['isRedeemed']=='Redeemed'){ ?>
							<div class="legend"><i class="fa fa-circle text-secondary"></i> Redeemed </div>
							<?}?>
							<? if($row['isFunded']!='Funded' && $row['isRedeemed']!='Redeemed'){ ?>
							<div class="legend"><i class="fa fa-circle text-warning "></i> Processing </div>
							<?}?>
						</div>
					</td>
				</tr>
				<?/*
			   <tr>
					<td>
						<div>
							<? if($row['isFunded']=='Funded'){ ?>
							<div class="legend"><i class="fa fa-circle text-success"></i> Funded/Minted <?= $m->fromNow()->getRelative()?> <small>( <?=date("M d, Y g:i a",strtotime($row['date'])) ?> ) </small></div>	
							<?}?>
							<? if($row['isRedeemed']=='Redeemed'){ ?>
							<div class="legend"><i class="fa fa-circle text-secondary"></i> Redeemed <?= $m->fromNow()->getRelative()?> <small>( <?=date("M d, Y g:i a",strtotime($row['date'])) ?> ) </small></div>
							<?}?>
							<? if($row['isFunded']!='Funded' && $row['isRedeemed']!='Redeemed'){ ?>
							<div class="legend"><i class="fa fa-circle text-warning "></i> Processing <?= $m->fromNow()->getRelative()?> <small>( <?=date("M d, Y g:i a",strtotime($row['date'])) ?> ) </small></div>
							<?}?>
						</div>
						
						<div class="max-90p"><a href="https://etherscan.io/tx/<?= $row['txhash'] ?>" target="_blank" rel="nofollow"><?= $row['txhash'] ?></a></div>
						
						<a href="/deposits/<?= $row['_depositContractAddress'] ?>" class="js-view btn btn-info btn-sm" rel="nofollow">View details</a>
					</td>
					<td>
						<div class="clearfix">
							<div class="float-left">Deposit Addr &nbsp;</div>
							<div class="max-70p float-left">
								<a href="https://etherscan.io/address/<?= $row['_depositContractAddress'] ?>" target="_blank" rel="nofollow"><?= $row['_depositContractAddress'] ?></a>
							</div>
						</div>
						<div class="clearfix">
							<div class="float-left">Keep Addr &nbsp;</div>
							<div class="max-70p float-left">
								<a href="https://etherscan.io/address/<?= $row['_keepAddress'] ?>" target="_blank" rel="nofollow"><?= $row['_keepAddress'] ?></a>
							</div>
						</div>
					</td>
				</tr>
			   */?>
			   <?endforeach?>
		   </tbody>
		</table>
    </div>
    <?else:?>
    <div class="alert alert-info">We didn't find any deposit</div>
    <?endif?>
</main>
