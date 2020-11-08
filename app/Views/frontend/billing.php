<?php
$uri = current_url(true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Billing Anda</title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1,string-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
     <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#dc0000">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#dc0000">

    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#dc0000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-capable" content="#dc0000">
    
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url() ?>/assets/images/favicon.png">
 
	<link href="<?=base_url() ?>/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom CSS -->
    <link href="<?=base_url() ?>/assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

    <link href="<?=base_url() ?>/assets/css/style.css" rel="stylesheet">
    <link href="<?=base_url() ?>/assets/css/colors/default-dark.css" id="theme" rel="stylesheet">
    <link href="<?=base_url() ?>/assets/css/custom.css" rel="stylesheet">


</head>
<body>
<div id="main-wrapper">
<div class="page-wrapper" style="margin:0px;">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
<div class="container-fluid" id="container-data">
	<!-- <div class="row">
        <div class="card">
            <div class="card-body" id="container-data"> 
            </div>
        </div>
    </div> -->
</div>
</div>
		</div>
	</div>
</div>
</div>
<div class="d-none" id='loader-wrapper'>
    <div class="loader"></div>
</div>

<script src="<?=base_url() ?>/assets/plugins/jquery/jquery.min.js"></script>
<script src="<?=base_url() ?>/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- Sweet-Alert  -->
<script src="<?=base_url() ?>/assets/plugins/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="<?=base_url() ?>/assets/plugins/sweetalert2/sweet-alert.init.js"></script>

<script type="text/javascript">

$(document).ready(function(){
	var meja_id = <?= $uri->getSegment(3); ?>;
	billing(meja_id);
 
});

function billing(meja_id) {
	$.ajax({
       	url : "<?= base_url('meja/billingcustomer') ?>",
       	type : "POST",
		data : {'meja_id':meja_id},
       beforeSend: function () { 
          $("#loader-wrapper").removeClass("d-none")
       },
       success:function(data){
        $('#container-data').html(data);
        $("#loader-wrapper").addClass("d-none");
      },
      error:function(){
          Swal.fire({
              title:"Gagal!",
              text:"Data gagal disimpan!",
              type:"warning",
              showCancelButton:!0,
              confirmButtonColor:"#556ee6",
              cancelButtonColor:"#f46a6a"
          })
      }
    });
}

function add(value){
	var meja_id = <?= $uri->getSegment(3); ?>;
  var currentVal = parseInt($("#qty" + value).val());
  $("#qty"+value).val(currentVal + 1); 
  var qty = currentVal + 1;    
  if (!isNaN(currentVal)) {
    	$.ajax({
		   url : "<?= base_url('meja/updateqty')?>",
		   type : "POST",
		   data : {'value':value,'quanty':qty},
		   beforeSend: function () { 
		      $("#loader-wrapper").removeClass("d-none");
		   },
		   success:function(){
		        $("#loader-wrapper").addClass("d-none");
		  		billing(meja_id);
		    },
		    error:function(){
				Swal.fire(
					'Gagal!',
					'Silahkan Coba Lagi.',
					'warning'
				)
		    }
		});
  }
}


function minus(value){
	var meja_id = <?= $uri->getSegment(3); ?>;
  	var currentVal = parseInt($("#qty" + value).val()); 
  	$("#qty"+value).val(currentVal - 1);  
    var qty = currentVal - 1;    
    if (qty==0) {
    	Swal.fire({
		    title: 'Yakin menghapus item ini ?',
		    text: "item yang sudah dihapus tidak bisa dikembalikan lagi, tapi anda bisa memesan lagi",
		    type: 'warning',
		    showCancelButton: true,
		    confirmButtonColor: '#3085d6',
		    cancelButtonColor: '#d33',
		    confirmButtonText: 'Yakin'
		}).then((result) => {
		    if (result.value == true) {
		    	$.ajax({
				   url : "<?= base_url('meja/updateqty')?>",
				   type : "POST",
				   data : {'value':value,'quanty':qty},
				   beforeSend: function () { 
				      $("#loader-wrapper").removeClass("d-none");
				      
				   },
				   success:function(){
				      $("#loader-wrapper").addClass("d-none");
				  		billing(meja_id);
				    },
				    error:function(){
						Swal.fire(
							'Gagal!',
							'Silahkan Coba Lagi.',
							'warning'
						)
				    }
				});
		    	
		    }
		 });

    } else if (!isNaN(currentVal)) {
        $.ajax({
		   url : "<?= base_url('meja/updateqty')?>",
		   type : "POST",
		   data : {'value':value,'quanty':qty},
		   beforeSend: function () { 
		      $("#loader-wrapper").removeClass("d-none");
		   },
		   success:function(){
		        $("#loader-wrapper").addClass("d-none");
		        billing(meja_id);
		  
		    },
		    error:function(){
				Swal.fire(
					'Gagal!',
					'Silahkan Coba Lagi.',
					'warning'
				)
		    }
		});
    }
};

function listmenu() {
  window.location.href = "<?=base_url()?>/produk/listmenu2/"+<?= $uri->getSegment(3)?>;
}

function order(id) {
Swal.fire({
    title: 'Apakah anda yakin ??',
    text: "Jika ingin cancel silahkan panggil petugas kami ",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yakin!'
}).then((result) => {
    if (result.value == true) {
    	$.ajax({
		   url : "<?= base_url('meja/orderbilling')?>",
		   type: "POST",
		   data : {id:id},
		   beforeSend: function () { 
		      $("#loader-wrapper").removeClass("d-none")
		   },
		   success:function(){
		      setTimeout(function(){ 
		        $("#loader-wrapper").addClass("d-none");
		        Swal.fire(
		            'Ordered!',
		            'Your order has been send to waiters.',
		            'success'
		        )
		        window.location.href = "<?=base_url()?>/produk/listmenu/"+<?= $uri->getSegment(3)?>;
		      }, 3000);  
		    },
		    error:function(){
		    Swal.fire(
		        'Gagal!',
		        'Silahkan Coba Lagi.',
		        'warning'
		    )
		    }
		  });
    	
    }
 })
}

function cancelorder(id) {
Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
}).then((result) => {
    if (result.value == true) {
    	$.ajax({
		   url : "<?= base_url('meja/cancelbilling')?>",
		   type: "POST",
		   data : {id:id},
		   beforeSend: function () { 
		      $("#loader-wrapper").removeClass("d-none")
		   },
		   success:function(){
		      setTimeout(function(){ 
		        $("#loader-wrapper").addClass("d-none");
		        Swal.fire(
		            'Canceled!',
		            'Your order has been canceled.',
		            'success'
		        )
		        window.location.href = "<?=base_url()?>/produk/listmenu/"+<?= $uri->getSegment(3)?>;
		      }, 3000);  
		    },
		    error:function(){
		    Swal.fire(
		        'Gagal!',
		        'Silahkan Coba Lagi.',
		        'warning'
		    )
		    }
		  });
    	
    }
 })
}
</script>
</body>
</html>