<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 mt-3">
	<?php $this->view('templates/menu-mobile'); ?>
	<div style="background:#fff;">
		<table id="deposites1" class="table table-striped table-bordered" style="width:100%">
		   <thead>
				<tr>
					<th>Date/Txhash</th>
					<th class="no-sort">Info</th>
					<th style="width:100px;">Ammount</th>
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
					</td>
					<td>
						<div class="clearfix">
							<div class="float-left">From &nbsp;</div>
							<div class="max-70p float-left">
								<a href="https://etherscan.io/address/<?= $row['from'] ?>" target="_blank" rel="nofollow"><?= $row['from'] ?></a>
							</div>
						</div>
						<div class="clearfix">
							<div class="float-left">To &nbsp;</div>
							<div class="max-70p float-left">
								<a href="https://etherscan.io/address/<?= $row['to'] ?>" target="_blank" rel="nofollow"><?= $row['to'] ?></a>
							</div>
						</div>
					</td>
					<td><div class="max-100"><img src="/assets/images/tbtc_32.png">&nbsp; <?= round($row['value'],6) ?></div></td>
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
