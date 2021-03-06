<?php
use App\Models\Produkmodel;
use App\Models\Imagesmodel;
$uri = current_url(true);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Daftar Menu</title>
  <style>
  html, body {
    height: 100% !important;
    background: #dc0000 !important;
  }

  .full-height {
    height: 100%;
    background: #dc0000;
  }

  .btn-xls {
    width: 150px;
    height: 150px;
    padding: 24px 15px;
    font-size: 70px;
  }
  </style>
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
    <meta name="apple-mobile-web-app-capable" content="yes">
    
	<link href="<?=base_url() ?>/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom CSS -->
    <link href="<?=base_url() ?>/assets/css/style.css" rel="stylesheet">
    <link href="<?=base_url() ?>/assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
<link href="<?=base_url() ?>/assets/css/custom.css" rel="stylesheet">
</head>
<body>
  <div class="page-wrapper" style="margin-left: 0px; padding-bottom: 0px;">
    <div class="container-fluid full-height" id="container_content">
      <div class="row">
        <div class="col-lg-12">
          <input type="hidden" id="meja_id" value="<?= $uri->getSegment(3) ?>"/>
          <div align="center" style="text-align: center;" id="div-menukategori">
            <?php
            foreach ($kategori as $k) {
            ?>
            <div align="center" style="display: inline-block; text-align: center;">
            <button style="height: 160px; width: 160px;  margin: 10px; padding:0px; border-radius: 10px; background-color: white; color:black;" class="btn btn-success" type="button" onclick="showmenubykat(<?= $k->kategori_id?>)">
              <div style=" display: flex;align-items:center;text-align: center; width: 100%; height: 100%; margin: 0px;">
                <img style="width: 100%; height: 100%; margin: 0px; padding: 0px; border-radius: 10px;" src="../../../images/<?=$k->image_nm?>">
                <span style="font-family: Coconut !important; position: absolute; font-size: 20px; font-weight: bold; background: #ffffff85; padding: 5px 25px;"><?= $k->kategori_nm ?></span>
              </div>
            </button>
            </div>
            <?php } ?>
          </div>

          <?php 
          $ret = "";
          foreach ($kategori as $k2) {
            $ret .= "<!-- LIST MENU -->"
                . "<div style='display: none;' id='menu_".$k2->kategori_id."'>"
                . "<div align='center'>"
                . "<div onclick='backtolistmenu(".$k2->kategori_id.")' style='display: inline-block; float: left; margin-top: 15px;'><img style='max-height: 100%; width: 100px;' src='".base_url()."/images/lib/arrowback.png'></div>"
                . "<div style='display: inline-block;'><span style='font-family: Coconut !important; font-size: 50px; font-weight: bold; color: white;' >".$k2->kategori_nm."</span></div>"
                . "</div>"
                  . "<div align='center' style='margin-top: 30px;'>";
                    $produkmodel = new Produkmodel();
                    $produk = $produkmodel->getbyKatId($k2->kategori_id);
            $ret .= "<table class='table-responsive w-100' id='myTable' align='center' style='background-color: #dc0000; font-family: Coconut !important;'>";
                    foreach ($produk->getResult() as $key) {
                    $harga = substr($key->produk_harga, 0,-3);
                    if (strlen($key->produk_nm) <= 28) {
                      $fontsize = "font-size: 18px;";
                    } else {
                      $fontsize = "font-size: 16px;";
                    }
                    
                  
                      $ret .= "<tr>"
                        . "<td style='padding: 3px;' width='20%' align='left'>"
                        . "<input oninput='javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);' id='qty$key->produk_id' data-produk-id='$key->produk_id' value='0' style='width: 80%; height: 60%; font-size: 20px; font-weight: bold; text-align: center; display: inline-block;' type='number' name='qty[]' maxlength='2' min='0' max='99'/></td>"

                        . "<td width='100%' align='left' style='padding: 3px; color: white; font-weight: bold; $fontsize'>$key->produk_nm</td>"

                        . "<td width='5%' align='right' style='padding: 3px; color: white; font-weight: bold; font-size: 20px;'>$harga</td>"
                        . "</tr>";
                  }
            $ret .= "</table>"
                 . "<hr>"
                 . "<div style='margin-bottom: 20px;' align='center'>";
                      $imagesmodel = new Imagesmodel();
                      $images = $imagesmodel->getimagebykatid($k2->kategori_id);
                      foreach ($images->getResult() as $key2) {
                    $ret .= "<div style='display: inline-block; margin: 5px; width: 45%; height: 120px; border-radius: 10px;'><img src='".base_url()."/images/$key2->image_nm' style='border-radius: 10px; height: 100%; width: 100%;'></div>";
                      }
                      $ret .= "</div>"
                        . "</div>"
                      . "</div>"
                      . "<!-- END LIST MENU -->";
                  }
          
          echo $ret;
          ?>

    </div>
     <button onclick="simpanorder()" type="button" class="btn btn-success btn-circle btn-xls" style="position: fixed; bottom: 50px; right: 20px;"><i class="fa fa-check"></i></button>
     <button onclick="listmenu()" type="button" class="btn btn-info btn-circle btn-xls" style="position: fixed; bottom: 50px; left: 30px;"><i class="fas fa-file-alt"></i></button>
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

function listmenu() {
  window.location.href = "<?=base_url()?>/produk/listmenu/"+<?= $uri->getSegment(3)?>;
}

function add(value){
  var currentVal = parseInt($("#qty" + value).val());    
  if (!isNaN(currentVal)) {
      $("#qty" + value).val(currentVal + 1);
  }
};

function minus(value){
    var currentVal = parseInt($("#qty" + value).val());    
    if (currentVal==0) {
    } else if (!isNaN(currentVal)) {
        $("#qty" + value).val(currentVal - 1);
    }
};

function simpanorder(){
  var qty = [];
  var produk_id = [];  
  var meja_id = $("#meja_id").val();
    $("input[name=\"qty[]\"]").each(function(){
      if (this.value !=0) {
        qty.push(this.value);
        var dataid = this.getAttribute("data-produk-id");
        produk_id.push(dataid);
      }
    });
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, order it!'
}).then((result) => {
    if (result.value == true) {
      $.ajax({
         url : "<?= base_url('produk/simpanorder')?>",
         type: "POST",
         data : {meja_id:meja_id,qty:qty,produk_id:produk_id},
         beforeSend: function () { 
            $("#loader-wrapper").removeClass("d-none")
         },
         success:function(data){
            setTimeout(function(){ 
              $("#loader-wrapper").addClass("d-none");
              if (data == "belumorder") {
                Swal.fire({
                  title: "Anda belum memilih produk !",
                  text: "Pesanan anda belum terkirim.",
                  type:"warning",
                  timer: 5000,
                  showConfirmButton: true
                });
              } else if (data == "false") {
                Swal.fire({
                  title: "Terjadi kesalahan!",
                  text: "Silahkan hubungi petugas.",
                  type:"danger",
                  timer: 5000,
                  showConfirmButton: true
                });
              } else if (data == "true") {
                Swal.fire({
                  title: "Terima Kasih!",
                  text: "Pesanan anda sudah terkirim.",
                  type:"success",
                  timer: 2000,
                  showConfirmButton: false
                });
                window.location.href = "<?=base_url()?>/produk/listmenu/"+<?= $uri->getSegment(3)?>;
              }
              
            }, 3000);   
              
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
 })
}
        
  function closeOver(f, value){
      return function(){
          f(value);
      };
  }

  $(function () {
      var numButtons = 2;    
      for (var i = 1; i <= numButtons; i++) {
          $("#add" + i).click(closeOver(add, i));
          $("#minus" + i).click(closeOver(minus, i));
      }
  });

  function showmenubykat(id) {
    $('#menu_'+id).animate({height: 'toggle'});
    $('#div-menukategori').hide();
  }

  function backtolistmenu(id) {
    $('#div-menukategori').animate({width: 'toggle'});
    $('#menu_'+id).hide();
  }
</script>
</body>
</html>