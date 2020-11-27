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
    
    <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url() ?>/assets/images/favicon.png">

    <!-- Bootstrap Core CSS -->
    <link href="<?=base_url() ?>/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="<?=base_url() ?>/assets/plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>/assets/plugins/datatables.net-bs4/css/responsive.dataTables.min.css">
    <!-- This page CSS -->
    <link rel="stylesheet" href="<?=base_url() ?>/assets/plugins/dropify/dist/css/dropify.min.css">
    <!--alerts CSS -->
    <link href="<?=base_url() ?>/assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?=base_url() ?>/assets/css/style.css" rel="stylesheet">
    <!-- You can change the theme colors from here -->
    <link href="<?=base_url() ?>/assets/css/colors/default-dark.css" id="theme" rel="stylesheet">
    <link href="<?=base_url() ?>/assets/css/custom.css" rel="stylesheet">
</head>
<body>
<div id="main-wrapper">
    <div class="page-wrapper" style="margin: 0px !important;">
        <div class="container-fluid" id="container_content">
            <!-- <div class="row">
                <div class="card">
                    <div class="card-body" > 

                    </div>
                </div>
            </div> -->
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

  // Jquery Dependency





function add(value){
  var currentVal = parseInt($("#qty" + value).val());
  $("#qty" + value).val(currentVal + 1);
  var quanty = currentVal + 1;
  if (!isNaN(currentVal)) {
      $.ajax({
        url : "<?= base_url('meja/updateqty') ?>",
        type: "post",
        data : {'value':value,'quanty':quanty},
        beforeSend: function () { 
		      $("#loader-wrapper").removeClass("d-none");
		    },
        success:function(data){
          $("#loader-wrapper").addClass("d-none");
		      documentready();
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
};

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
               url : "<?= base_url('meja/setnullifieditem')?>",
               type : "POST",
               data : {'id':value},
               beforeSend: function () { 
                  $("#loader-wrapper").removeClass("d-none");
                  
               },
               success:function(){
                  $("#loader-wrapper").addClass("d-none");
                  documentready();
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
          url : "<?= base_url('meja/updateqty') ?>",
          type: "post",
          data : {'value':value,'quanty':qty},
          beforeSend: function () { 
            $("#loader-wrapper").removeClass("d-none");
          },
          success:function(data){
            $("#loader-wrapper").addClass("d-none");
            documentready();
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
};

$(document).ready(function() {
  documentready();
});

function documentready() {
  var id = <?= $uri->getSegment(3); ?>;
	$.ajax({
    url : "<?= base_url('meja/showorderbymeja') ?>",
    data : {'id':id},
    type: "post",

    beforeSend: function () { 
      $("#loader-wrapper").removeClass("d-none");
    },
    success:function(data){
      $('#container_content').html(data);
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
}

function backtowaiters(){
	window.location.href = "<?=base_url()?>/dashboard/waiters";
}

function disableproduk(id){
    $("#loader-wrapper").removeClass("d-none");
    setTimeout(function(){ 
$.ajax({
     url : "<?= base_url('meja/setnullifieditem') ?>",
     data : {'id':id},
     type: "post",
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
    }, 1000);
}

function enableproduk(id){
     $("#loader-wrapper").removeClass("d-none");
    setTimeout(function(){ 
$.ajax({
     url : "<?= base_url('meja/setnormalitem') ?>",
     data : {'id':id},
     type: "post",
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
  
     }, 1000);
}

function verifybilling(id){
var grandtotal = $('#grandtotal').val();

$.ajax({
     url : "<?= base_url('meja/verifybilling') ?>",
     data : {'id':id,'grandtotal':grandtotal},
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