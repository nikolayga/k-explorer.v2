<?#php $this->load->view('templates/ms'); ?>
<?#=$title?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
	<?php $this->view('templates/menu-mobile'); ?>
	<div class="row">	
	  <div class="col-12">	
		<div class=" card1 bg-light mt-2 " >
			<div class="card-body">
				<div class="row">
					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="card1 card-stats mb-0 mt-1">
							<div class="card-body ">
								<div class="row">
									<div class="col-5 col-md-4 d-sm-none d-md-block">
										<div class="icon-big text-center pt-2"><img src="<?php echo base_url() ?>assets/images/tbtc_32.png"></div>
									</div>
									<div class="col-7 col-md-8 col-sm-12" >
										<div class="numbers">
											<p class="card-category">Max Supply</p>
											<p class="card-title">250 <small>tBTC</small></p>
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
									<div class="col-5 col-md-4 d-sm-none d-md-block">
										<div class="icon-big text-center pt-2"><img src="<?php echo base_url() ?>assets/images/tbtc_32.png"></div>
									</div>
									<div class="col-7 col-md-8">
										<div class="numbers">
											<p class="card-category">Current Supply</p>
											<p class="card-title"><?=$currentSuply?> <small>tBTC</small></p>
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
									<div class="col-5 col-md-4 d-sm-none d-md-block">
										<div class="icon-big text-center pt-2"><img src="<?php echo base_url() ?>assets/images/tbtc_32.png"></div>
									</div>
									<div class="col-7 col-md-8">
										<div class="numbers">
											<p class="card-category">Total Minted</p>
											<p class="card-title"><?=$totalMinted?> <small>tBTC</small></p>
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
									<div class="col-5 col-md-4">
										<div class="icon-big text-center icon-warning"><i class="nc-icon nc-favourite-28 text-primary"></i></div>
									</div>
									<div class="col-7 col-md-8">
										<div class="numbers">
											<p class="card-category">Transactions 24H</p>
											<p class="card-title"><?=$transfers24h?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	  </div>
	</div>
	

	
    <div class="mt-3" role="tabpanel" >
		<ul class="nav nav-tabs _bar_tabs" role="tablist" data-counter="1">
			<li role="presentation" class="active">
				<a href="#tab_content-1" role="tab" data-toggle="tab" aria-expanded="true" >Deposits</a>
			</li>
			<li role="presentation">
				<a href="#tab_content-3" role="tab" data-toggle="tab"  >Redeeming</a>
			</li>
			<li role="presentation">
				<a href="#tab_content-2" role="tab" data-toggle="tab"  >Transactions</a>
			</li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active in" id="tab_content-1" aria-labelledby="t-tab-1">
				 <div id="chart_deposits" style="width:100%; height:400px;"></div>
			</div>
			
			<div role="tabpanel" class="tab-pane " id="tab_content-2" aria-labelledby="t-tab-2">
				<div id="chart_transfers" style="width:100%; height:400px;"></div>
			</div> 
			
			<div role="tabpanel" class="tab-pane" id="tab_content-3" aria-labelledby="t-tab-3">
				 <div id="chart_redeemed" style="width:100%; height:400px;"></div>
			</div>
			
		</div>
	</div>	
</main>

<script src="<?php echo base_url() ?>assets/vendors/amcharts/amcharts.js"></script>
<script src="<?php echo base_url() ?>assets/vendors/amcharts/serial.js"></script>
<script src="<?php echo base_url() ?>assets/js/chart_deposit.js"></script>
<script src="<?php echo base_url() ?>assets/js/chart_transfers.js"></script>
<script src="<?php echo base_url() ?>assets/js/chart_redeemed.js"></script>
