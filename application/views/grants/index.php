<link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/amcharts/plugins/export/export.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/arcticmodal/jquery.arcticmodal-0.3.css">
<link rel="stylesheet" href="<?php echo base_url() ?>assets/vendors/arcticmodal/themes/simple.css">

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 mt-3">
	<?php $this->view('templates/menu-mobile'); ?>
	
	<div class="mt-3" role="tabpanel" >
		<ul class="nav nav-tabs _bar_tabs" role="tablist" data-counter="1">
			<li role="presentation" class="active">
				<a href="#tab_content-1" role="tab" data-toggle="tab" aria-expanded="true" >Infographic</a>
			</li>
			<li role="presentation">
				<a href="#tab_content-2" role="tab" data-toggle="tab"  >Grants list</a>
			</li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active in" id="tab_content-1" aria-labelledby="t-tab-1">
				 <h3>Total grants amount: <?=number_format($total)?> KEEP</h3>
				 <div id="chart_grants" style=" height:500px;font-size:11px;"></div>
			</div>
			
			<div role="tabpanel" class="tab-pane " id="tab_content-2" aria-labelledby="t-tab-2">
				<div class="table-responsive" style="background:#fff;">
					<table  class="table table-striped table-bordered js--data-table table-sm" style="width:100%">
					   <thead>
							<tr>
								<th style="min-width:70px;">Grant ID</th>
								<th>Grantee</th>
								<th>Grant Ammount</th>
								<th>Start</th>
								<th style="min-width:110px;">Fully unlocked at</th>
								<th>Staked</th>
								<th>Revoked</th>
								<th>Withdrawn</th>
								<th>Withdrawable</th>
							</tr>
					   </thead>
					   <tbody>
						   <?php foreach ($items as  $row) :?>
						   <tr>
								<td data-order="<?= $row['id'] ?>"><?= $row['id'] ?><a href="/grants/<?= $row['id'] ?>" class="float-right btn btn-sm btn-info js-view" data-id="<?= $row['id'] ?>">Info</a></td>
								<td><a href="https://etherscan.io/address/<?=$row['grantee']?>" target="_blank"><?=substr($row['grantee'],0,8)?>...<?=substr($row['grantee'],strlen($row['grantee'])-6,6)?></a></td>
								<td data-order="<?= $row['amount'] ?>"><?= number_format($row['amount']) ?><div class="d-none"><?=$row['grantManager']?>|<?=$row['grantee']?></div></td>
								<td data-order="<?= $row['start'] ?>"><?=date("M d, Y",strtotime($row['start'])) ?></td>
								<td data-order="<?= $row['end'] ?>"><?=date("M d, Y",strtotime($row['end']))?></td>
								<td data-order="<?=$row['staked'] ?>"><?=number_format($row['staked']) ?></td>
								<td data-order="<?=$row['revokedAmount']?>"><?=number_format($row['revokedAmount'])?></td>
								<td data-order="<?=$row['withdrawn']?>"><?=number_format($row['withdrawn'])?></td>
								<td data-order="<?=$row['withdrawable']?>"><?=number_format($row['withdrawable'])?></td>
							</tr>
						   <?endforeach?>
					   </tbody>
					</table>
				</div>
			</div> 
		</div>
	</div>	
</main>

<script src="<?php echo base_url() ?>assets/vendors/amcharts/amcharts.js"></script>
<script src="<?php echo base_url() ?>assets/vendors/amcharts/pie.js"></script>
<script src="<?php echo base_url() ?>assets/vendors/amcharts/plugins/export/export.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/chart_grants.js"></script>
<script src="<?php echo base_url() ?>assets/vendors/arcticmodal/jquery.arcticmodal-0.3.min.js"></script>

<script type="text/javascript">
$(function(){
	$(document).on('click','.js-view',function(e){
		e.preventDefault();
		var url = $(this).attr('href');
		$.arcticmodal({
		    closeOnEsc:false,
			closeOnOverlayClick:true,
			type: 'ajax',
			url: url
		});		
	});
});
</script>
