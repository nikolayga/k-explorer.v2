<div class="nav-scroller py-1 mb-2 d-none small-menu">
	<nav class="nav d-flex justify-content-between">
	  <a class="p-2 <?=empty($this->uri->segment(1))?'active':''?>" href="/"><span data-feather="grid"></span> Dashboard</a>
	  <a class="p-2 <?=$this->uri->segment(1)=="deposits"?'active':''?>" href="/deposits/"><span data-feather="credit-card"></span> Deposits</a>
	  <a class="p-2 <?=$this->uri->segment(1)=="operators"?'active':''?>" href="/operators/"><span data-feather="users"></span> Operators</a>
	  <a class="p-2 <?=$this->uri->segment(1)=="grants"?'active':''?>" href="/grants/"><span data-feather="award"></span> Grants</a>
	  <a class="p-2 <?=$this->uri->segment(1)=="keeptransfers"?'active':''?>" href="/keeptransfers/">KEEP Transfers</a>
	  <a class="p-2 <?=$this->uri->segment(1)=="transfers"?'active':''?>" href="/transfers/">tBTC Transfers</a>
	  <a class="p-2 <?=$this->uri->segment(1)=="tbtc_mints"?'active':''?>" href="/tbtc_mints/">tBTC mints</a>
	  
	</nav>
</div>
