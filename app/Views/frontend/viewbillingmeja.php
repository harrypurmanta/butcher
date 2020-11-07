<?php
$uri = current_url(true);
?>
<!DOCTYPE html>
<html>
<head>
	<title>DATA MEJA</title>
  <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#dc0000">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#dc0000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#dc0000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    
	  <link href="../../public/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../public/assets/css/style.css" rel="stylesheet">
    <link href="../../public/assets/css/custom.css" rel="stylesheet">
    <link href="../../public/assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

</head>
<body>
<div class="col-lg-12">
<div class="container-fluid" id="container_content">
	

</div>
</div>
<div class="d-none" id='loader-wrapper'>
    <div class="loader"></div>
</div>
<script src="../../public/assets/plugins/jquery/jquery.min.js"></script>
<script src="../../public/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../public/assets/plugins/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="../../public/assets/plugins/sweetalert2/sweet-alert.init.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  var id = <?= $uri->getSegment(3); ?>
	$.ajax({
	 url : "<?= base_url('meja/showorderbymeja') ?>",
   data : {'id':id},
   type: "post",

	 beforeSend: function () { 
	  $("#loader-wrapper").removeClass("d-none");
	 },
	success:function(data){
	  setTimeout(function(){ $("#loader-wrapper").addClass("d-none"); }, 1000);
	},
	error:function(){
  	Swal.fire({
  	  title:"Error!",
  	  type:"warning",
  	  showCancelButton:!0,
  	  confirmButtonColor:"#556ee6",
  	  cancelButtonColor:"#f46a6a"
  	})
	}
	});
});


function backtowaiters(){
	window.location.href = "<?=base_url()?>/dashboard/waiters";
}

function disableproduk(id){
$.ajax({
     url : "<?= base_url('meja/setnullifieditem') ?>",
     data : {'id':id},
     type: "post",
     beforeSend: function () { 
      $("#loader-wrapper").removeClass("d-none");
     },
     success:function(data){
      $("#loader-wrapper").addClass("d-none");
      $( "#div-item" ).load(window.location.href);
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

function enableproduk(id){
$.ajax({
     url : "<?= base_url('meja/setnormalitem') ?>",
     data : {'id':id},
     type: "post",
     beforeSend: function () { 
      $("#loader-wrapper").removeClass("d-none");
     },
     success:function(data){
      $("#loader-wrapper").addClass("d-none");
      $( "#div-item" ).load(window.location.href);

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

function verifybilling(id){
$.ajax({
     url : "<?= base_url('meja/verifybilling') ?>",
     data : {'id':id},
     type: "post",
     beforeSend: function () { 
      $("#loader-wrapper").removeClass("d-none");
     },
     success:function(data){
      $("#loader-wrapper").addClass("d-none");
      $( "#buttonverif" ).load(window.location.href);
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

function batalbilling(id){
$.ajax({
     url : "<?= base_url('meja/batalbilling') ?>",
     data : {'id':id},
     type: "post",
     beforeSend: function () { 
      $("#loader-wrapper").removeClass("d-none");
     },
     success:function(data){
      $("#loader-wrapper").addClass("d-none");
      $( "#buttonverif" ).load(window.location.href);
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
</script>
</body>
</html>