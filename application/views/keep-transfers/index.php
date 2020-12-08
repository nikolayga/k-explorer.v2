<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 mt-3">
	<h1>KEEP transfers</h1>
	<?php $this->view('templates/menu-mobile'); ?>
	<?
	$uni = array(strtolower('0xE6f19dAb7d43317344282F803f8E8d240708174a')=>true);
	?>
	<div class="my-3">
		<form class="form-horizontal js--form" role="form" id="keeptransfers-filter" action="/keeptransfers/">
			<div class="row mx-0"> 
				  <div class="form-group col-md-12 col-sm-12 col-lg-2 mb-2">
					<label for="filter">Date from</label>
					<input type="date" name="date_from" max="<?=date("Y-m-d")?>"  min="2020-04-28" class="form-control" value="<?=@htmlspecialchars($_REQUEST['date_from'])?>">
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-2 mb-2">
					<label for="filter">Date to</label>
					<input type="date" name="date_to" max="<?=date("Y-m-d")?>"  min="2020-04-28" class="form-control"  value="<?=@htmlspecialchars($_REQUEST['date_to'])?>">
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-2 mb-2">
					<label for="contain">Amount from</label>
					<input class="form-control" name="value_from" type="number" value="<?=@htmlspecialchars($_REQUEST['value_from'])?>"/>
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-2 mb-2">
					<label for="contain">Amount to</label>
					<input class="form-control" name="value_to" type="number" value="<?=@htmlspecialchars($_REQUEST['value_to'])?>"/>
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-1 mb-2">
					<label for="contain">From</label>
					<input class="form-control" name="fromAddr" type="text" value="<?=@htmlspecialchars($_REQUEST['fromAddr'])?>" placeholder="Address to"/>
				  </div>
				  <div class="form-group col-md-12 col-sm-12 col-lg-1 mb-2">
					<label for="contain">To</label>
					<input class="form-control" name="toAddr" type="text" value="<?=@htmlspecialchars($_REQUEST['toAddr'])?>"  placeholder="Address from"/>
				  </div>
				  
				  <div class="form-group  col-md-6 col-sm-6 col-lg-2 mb-2" style="padding-top:28px;">
					<button type="submit" class="btn btn-primary d-inline-block" title="Apply filter"><span data-feather="search"></span> Apply</button>
					<?if(!empty($_GET)):?>
					<a class="btn btn-secondary d-inline-block" href="/keeptransfers/" title="Reset filter"><span data-feather="slash"></span></a>
					<?endif?>
				  </div>
			</div>
			
		</form>
	</div>
	
	<div class="table-responsive" style="background:#fff;">
		<table id="deposites1" class="table table-striped table-bordered" style="width:100%">
		   <thead>
				<tr>
					<th>Date/Txhash</th>
					<th class="no-sort d-none d-lg-table-cell d-xl-table-cell">Info</th>
					<th class="d-none d-lg-table-cell d-xl-table-cell">Amount</th>
				</tr>
		   </thead>
		   <tbody>
				<?php foreach ($items as  $row) :?>
				   <?
				   $m = new \Moment\Moment($row['date']);
				   ?>
				<tr>
					<td data-order="<?=strtotime($row['date'])?>">
						<div class="max-90p"><a href="https://etherscan.io/tx/<?= $row['txhash'] ?>" target="_blank" rel="nofollow"><?= $row['txhash'] ?></a></div>
						<?= $m->fromNow()->getRelative()?> <small>( <?=date("M d, Y g:i a",strtotime($row['date'])) ?> )</small>
						<div class="d-xl-none">
							<div class="clearfix">
								<div class="float-left">From &nbsp;</div>
								<div class="max-70p float-left">
									<a href="https://etherscan.io/address/<?= $row['from'] ?>" target="_blank" rel="nofollow"><?=!empty($uni[strtolower($row['from'])])?'<img src="/assets/images/uniswap.svg" title="uniswap">':''?><?= $row['from'] ?></a>
								</div>
							</div>
							<div class="clearfix">
								<div class="float-left">To &nbsp;</div>
								<div class="max-70p float-left">
									<a href="https://etherscan.io/address/<?= $row['to'] ?>" target="_blank" rel="nofollow"><?=!empty($uni[strtolower($row['to'])])?'<img src="/assets/images/uniswap.svg" title="uniswap">':''?><?= $row['to'] ?></a>
								</div>
							</div>
							<b><?= number_format($row['value']) ?> KEEP</b>
						</div>
					</td>
					<td class="d-none d-lg-table-cell d-xl-table-cell">
						<div class="clearfix">
							<div class="float-left">From &nbsp;</div>
							<div class="max-70p float-left">
								<a href="https://etherscan.io/address/<?= $row['from'] ?>" target="_blank" rel="nofollow"><?=!empty($uni[strtolower($row['from'])])?'<img src="/assets/images/uniswap.svg" title="uniswap">':''?><?= $row['from'] ?></a>
							</div>
						</div>
						<div class="clearfix">
							<div class="float-left">To &nbsp;</div>
							<div class="max-70p float-left">
								<a href="https://etherscan.io/address/<?= $row['to'] ?>" target="_blank" rel="nofollow"><?=!empty($uni[strtolower($row['to'])])?'<img src="/assets/images/uniswap.svg" title="uniswap">':''?><?= $row['to'] ?></a>
							</div>
						</div>
					</td>
					<td align="right" class="d-none d-lg-table-cell d-xl-table-cell"><div class="max-90p"><?= number_format($row['value']) ?> KEEP</div></td>
				</tr>
				<?endforeach?>
		   </tbody>
		</table>
		<nav aria-label="Page navigation example">
		  <ul class="pagination">
				<?php echo $this->pagination->create_links(); ?>
		  </ul>
		</nav>
    </div>
</main>

