window.account = null;

$(document).ready(function() {
	feather.replace()
	//$.fn.dataTableExt.pager.numbers_length = 50;
	
	if($('.js--data-table').length>0){
		var sort_col = 0;
		if($('.js--data-table').data('sort-col')) sort_col = parseInt($('.js--data-table').data('sort-col'));
		var table  = $('.js--data-table').DataTable({
			pageLength: 50,
			columnDefs: [
			  { targets: 'no-sort', orderable: false }
			],
			 "order": [[ sort_col, "desc" ]]
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

$(document).on('submit','.js--form',function(e){
	$(this).find('input,select').each(function(){
		if($(this).val()=="") $(this).attr('disabled','disabled');
	});
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
							if($('#address_name').length>0) updateInterface();
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

function number_format( number, decimals = 0, dec_point = '.', thousands_sep = ',' ) {
  let sign = number < 0 ? '-' : '';
  let s_number = Math.abs(parseInt(number = (+number || 0).toFixed(decimals))) + "";
  let len = s_number.length;
  let tchunk = len > 3 ? len % 3 : 0;

  let ch_first = (tchunk ? s_number.substr(0, tchunk) + thousands_sep : '');
  let ch_rest = s_number.substr(tchunk)
    .replace(/(\d\d\d)(?=\d)/g, '$1' + thousands_sep);
  let ch_last = decimals ?
    dec_point + (Math.abs(number) - s_number)
      .toFixed(decimals)
      .slice(2) :
    '';

  return sign + ch_first + ch_rest + ch_last;
}
