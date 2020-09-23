<?php
use App\Models\Produkmodel;
use App\Models\Imagesmodel;
$uri = current_url(true);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Daftar Menu</title>
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
<body style="height: 100vh; width: 100%; background-color: #dc0000;">
<div class="col-lg-12 col-md-12">
<div class="container-fluid" id="container_content">
  <div style="background-color: #dc0000; height: 100%;" class="col-lg-12 col-md-12">
      <input type="hidden" id="meja_id" value="<?= $uri->getSegment(3) ?>"/>
      <div id="div-menukategori">
        <?php
        foreach ($kategori as $k) {
        ?>
        <button style="width: 100%; margin-top: 30px; border-radius: 10px; font-size: 50px; font-weight: bold; background-color: white; color:black;" class="btn btn-success" type="button" onclick="showmenubykat(<?= $k['kategori_id']?>)"><?= $k['kategori_nm'] ?></button>
        <?php } ?>
      </div>

      <?php 
      $ret = "";
      foreach ($kategori as $k2) {
        $ret .= "<!-- LIST MENU -->"
            . "<div style='display: none;' id='menu_".$k2['kategori_id']."'>"
            . "<div align='center'>"
            . "<div onclick='backtolistmenu(".$k2['kategori_id'].")' style='display: inline-block; float: left; margin-top: 15px;'><img style='max-height: 100%; width: 160px;' src='../../images/lib/arrowback.png'></div>"
            . "<div style='display: inline-block;'><span style='font-size: 80px; font-weight: bold; color: white;' >".$k2['kategori_nm']."</span></div>"
            . "</div>"
              . "<div class='table-responsive' style='margin-top: 30px;'>";
                $produkmodel = new Produkmodel();
                $produk = $produkmodel->getbyKatId($k2['kategori_id']);
        $ret .= "<table id='myTable' align='center' style='margin-top: 5px; background-color: #dc0000;'>";
                foreach ($produk->getResult() as $key) {
                $harga = str_replace(0,'', $key->produk_harga);
              
                  $ret .= "<tr class='tr'>"
                    . "<td width='250' height='50' align='left'>"
                    . "<input oninput='javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);' id='qty$key->produk_id' data-produk-id='$key->produk_id' value='0' style='width: 40%; height: 55px; font-size: 40px; font-weight: bold; text-align: center; display: inline-block; top: 20px;' type='number' name='qty[]' maxlength='2' min='0' max='99'/>"
                    . "<button onclick='minus($key->produk_id)' class='btn btn-success font-weight-bold' style='font-size: 37px; height: 55px; width: 55px; line-height: 25px; display: inline-block; margin-bottom: 20px;'><i class='mdi mdi-pencil'></i></button>"
                   
                    . "<td width='550' align='left' style='color: white; font-weight: bold; font-size: 40px;'>$key->produk_nm</td>"
                    . "<td width='150' align='center' style='color: white; font-weight: bold; font-size: 50px;'>$harga</td>"
                    . "</tr>";
              }
        $ret .= "</table>"
             . "<hr>"
             . "<div style='margin-bottom: 20px;' align='center'>";
                  $imagesmodel = new Imagesmodel();
                  $images = $imagesmodel->getimagebykatid($k2['kategori_id']);
                  foreach ($images->getResult() as $key2) {
                $ret .= "<div style='display: inline-block; padding: 10px;'><img src='../../images/$key2->image_nm' style='height: 185px; width: 280px;'></div>";
                  }
                  $ret .= "</div>"
                    . "</div>"
                  . "</div>"
                  . "<!-- END LIST MENU -->";
              }
      
      echo $ret;
      ?>

</div>
 <button onclick="simpanorder()" type="button" class="btn btn-success btn-circle btn-xl" style="position: fixed; bottom: 50px; right: 20px;"><i class="fa fa-check"></i></button>
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
  $.ajax({
   url : "<?= base_url('produk/simpanorder')?>",
   type: "POST",
   data : {meja_id:meja_id,qty:qty,produk_id:produk_id},
   beforeSend: function () { 
      $("#loader-wrapper").removeClass("d-none")
   },
   success:function(){
      setTimeout(function(){ 
        $("#loader-wrapper").addClass("d-none");
        Swal.fire({
          title: "Terima Kasih!",
          text: "Pesanan anda sudah terkirim.",
          type:"success",
          timer: 1000,
          showConfirmButton: false
        });
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