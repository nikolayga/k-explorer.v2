window.account = null;

$(document).ready(function() {
	feather.replace()
	
	if($('#deposites').length>0){
		$('#deposites').DataTable({
			columnDefs: [
			  { targets: 'no-sort', orderable: false }
			],
			 "order": [[ 0, "desc" ]]
		});
	}
	$('[data-toggle="tooltip"]').tooltip()
});
	
$(window).on('load', function(){ 
	$('#status').fadeOut();
    $('#preloader').delay(50).fadeOut(100);
    $('body').delay(50).css({'overflow':'visible'});
});

$(document).on('click','ul._bar_tabs li',function(){
	$('ul._bar_tabs li').removeClass('active');
	$(this).addClass('active');
});

$(document).on('click','.js-auth',function(e){
	e.preventDefault();
	connect();
});

$(window).on('load', function () {
	if (typeof web3 !== 'undefined'){
		if (typeof ethereum !== 'undefined') {
			window.account = "";
			if($('#address_name').length>0) {
				window.account = $('#address_name').data('addr');
				check();
			}
			
			try {
				ethereum.on('accountsChanged', function (accounts) {
					if(accounts==""){
						$.post( "/ajax/auth/", {address: 'none', confirmed:0}, function( data ) {
							updateInterface();
						});
					}else{
						var account = accounts[0];
						
						if(account!=window.account){
							$.post( "/ajax/auth/", {address: account, confirmed:1}, function(data) {
								updateInterface();
								window.account = account;
							});
						}else{
							if($('.auth-block').length>0 && window.account!=account){
								$.post( "/ajax/auth/", {address: account, check:'Y'}, function( data ) {
									if(data=="ok") {
										connect();
									}
								});
							}
						}
					}
				});
			} catch (err) {
				
			}
		}
	}
});

async function connect () {
	if (typeof web3 !== 'undefined'){
		if (typeof ethereum !== 'undefined') {
			var con = await ethereum.enable().catch(function(){
				$.post( "/ajax/auth/", { confirmed:0 }, function( data ) {
					updateInterface();
				});
			});
			
			if(con!=undefined && con[0]!=undefined){
				$.post( "/ajax/auth/", {address: con[0], confirmed:1}, function(data) {
					updateInterface();
				});
			}
		}
	}
}

async function check() {
	if (typeof web3 !== 'undefined'){
		if (typeof ethereum !== 'undefined') {
			var con = await ethereum.enable().catch(function(){
				$.post( "/ajax/auth/", {confirmed:0}, function( data ) {
					updateInterface();
				});
			});

			if(con!=undefined && con[0]!=undefined){
				if (con[0] !== window.account) {
					window.account = con[0];
					$.post( "/ajax/auth/", {address: con[0], confirmed:1}, function(data) {
						updateInterface();
					});
				}else{
					if($('.auth-block').length>0){
						$.post( "/ajax/auth/", {address: con[0], check:'Y'}, function( data ) {
							if(data=="ok") {
								connect();
							}
						});
					}
				}
			}else{
				if($('.login-block').length>0 ){
					$.post( "/ajax/auth/", {confirmed:0}, function( data ) {
						updateInterface();
					});
				}
			}
		}
	}
}


function updateInterface(){
	location.reload();
}
