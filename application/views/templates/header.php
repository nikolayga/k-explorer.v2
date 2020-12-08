<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="KEEP Explorer helps to view information about deposits, shows detailed information about deposit process. Also provides search deposits by address feature.">

	<!-- Open Graph / Facebook -->
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://keep-explorer.info/">
	<meta property="og:title" content="KEEP Explorer">
	<meta property="og:description" content="KEEP Explorer helps to view information about deposits, shows detailed information about deposit process. Also provides search deposits by address feature.">
	<meta property="og:image" content="<?php echo base_url() ?>assets/images/dashboard.png">

    <title>KEEP Explorer</title>

	<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/config.js"></script>
	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" >
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" >
	
	<link rel ="apple-touch-icon" sizes ="180x180" href="<?php echo base_url() ?>assets/images/apple-touch-icon.png">
	<link rel="icon" type ="image/png" sizes ="32x32" href="<?php echo base_url() ?>assets/images/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url() ?>assets/images/favicon-16x16.png">

	
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url() ?>assets/css/dashboard.css" rel="stylesheet">
    <link href="<?php echo base_url() ?>assets/css/app.css" rel="stylesheet">
    
    <!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-179160591-1"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'UA-179160591-1');
	</script>
	
  </head>
  <body style="overflow: hidden;">
  
  <div id="preloader"> 
      <div id="status">
      </div> 
  </div>
  
  <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
	  <a class="navbar-brand col-2 mr-0" href="/"><img src="<?php echo base_url() ?>assets/images/favicon-32x32.png"> <span class="logo-title">KEEP Explorer</span> <span class="badge badge-primary float-right mt-1 logo-net-type">Mainnet</span></a> 
	  <form action="/deposits/" method="GET" class="w-100 px-2 col-10">
	  <input class="form-control form-control-dark w-100" name="s" autocomplete="off" type="text" placeholder="Find deposit" aria-label="Search">
	  </form>
  </nav>
	
  <div class="container-fluid">
    <div class="row">
		<nav class="col-md-2 d-none d-md-block bg-light sidebar">
			 <div class="profile clearfix profile--new">
				<?if(!isset($_SESSION['MM_AUTH_CONFIRMED']) || empty($_SESSION['MM_AUTH_ACCOUNT']) || $_SESSION['MM_AUTH_CONFIRMED']==false):?>
					<div class="profile_pic">
						<img src="<?php echo base_url() ?>assets/images/avatar.png" alt="" class="img-circle profile_img">
					</div>
					<div class="profile_info auth-block">
						<a href="" id="login_wm" class="js-auth">Enter with <br>MetaMask</a>
					</div>
				<?elseif($_SESSION['MM_AUTH_CONFIRMED']==true && !empty($_SESSION['MM_AUTH_ACCOUNT'])):?>
					<div class="profile_pic">
						<img src="<?php echo base_url() ?>assets/images/avatar.png" alt="" class="img-circle profile_img">
					</div>
					<div class="profile_info login-block">
						<span>Welcome,</span>
						<h2 id="address_name" data-addr="<?=$_SESSION['MM_AUTH_ACCOUNT']?>" title="<?=$_SESSION['MM_AUTH_ACCOUNT']?>"><a href="javascript:void(0)" class="colored-link"><?=$_SESSION['MM_AUTH_ACCOUNT']?></a></h2>
					</div>
				<?endif?>
			  </div> 	
			  <div class="sidebar-sticky mt-2">
				<ul class="nav flex-column">
				  <li class="nav-item">
					<a class="nav-link <?=empty($this->uri->segment(1))?'active':''?>" href="<?php echo base_url() ?>">
					  <span data-feather="grid"></span>
					  Dashboard 
					</a>
				  </li>
				
				  <li class="nav-item">
					<a class="nav-link <?=$this->uri->segment(1)=="deposits"?'active':''?>" href="/deposits/" >
					  <span data-feather="credit-card"></span>
					  Deposits
					</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link <?=$this->uri->segment(1)=="operators"?'active':''?>" href="/operators/" >
					  <span data-feather="users"></span>
					  Operators
					</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link <?=$this->uri->segment(1)=="grants"?'active':''?>" href="/grants/">
					  <span data-feather="award"></span>
					  Grants
					</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link <?=$this->uri->segment(1)=="keeptransfers"?'active':''?>" href="/keeptransfers/">
					  <span data-feather="repeat"></span>
					  KEEP Transfers
					</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link <?=$this->uri->segment(1)=="transfers"?'active':''?>" href="/transfers/">
					  <span data-feather="repeat"></span>
					  tBTC Transfers
					</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link <?=$this->uri->segment(1)=="tbtc_mints"?'active':''?>" href="/tbtc_mints/">
					  <span data-feather="layers"></span>
					  tBTC mints
					</a>
				  </li>
				  

				  
				</ul>
			  </div>
		</nav>
