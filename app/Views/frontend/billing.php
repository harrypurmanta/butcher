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
<div class="container-fluid">
	<?php
	if ($billing[0]->member_id == 0) {
		$billname = $billing[0]->meja_nm;
	} else {
		$billname = $billing[0]->person_nm;
	}

	if ($billing[0]->statusbilling == 'verified') {
		$collctedby = "<tr>
	          <td align='left'>Collected By</td>
	          <td align='right'>".$billing[0]->collected_nm."</td>
	        </tr>";
	} else {
		$collctedby = "";
	}

	if ($billing[0]->statusbilling == 'normal') {
		$footer = "<button onclick='cancelorder(".$billing[0]->billing_id.")' type='button' class='btn btn-danger float-left' style='font-weight: bold;'>CANCEL</button>
			<button onclick='order(".$billing[0]->billing_id.")' class='btn btn-success float-right' style='font-weight: bold;'>ORDER</button>";
		$buttonmenu = "<div style='display:inline-block;' class='float-left'>
			<button onclick='listmenu()' type='button' class='btn btn-info float-left' style='font-weight: bold;'>MENU</button>
			</div>";
	} else if ($billing[0]->statusbilling == 'waiting') {
		$footer = "<div align='center' class='alert alert-info alert-rounded'> 
						<i class='far fa-handshake'></i> SILAHKAN TUNGGU WAITERS UNTUK KONFIRMASI PESANAN ANDA !!
					</div>";

		$buttonmenu = "<div style='display:inline-block;' class='float-left'>
						<button onclick='listmenu()' type='button' class='btn btn-info float-left' style='font-weight: bold;'>MENU</button>
						</div>";
	} else if ($billing[0]->statusbilling == 'verified') {
		$footer = "<div align='center' class='alert alert-success alert-rounded'> 
						<i class='far fa-handshake'></i>  PESANAN ANDA SEDANG DI PROSES. SILAHKAN TUNGGU !!
					</div>";
		$buttonmenu = "";
	}
	
	
list($dt,$tm) = explode(" ", $billing[0]->created_dttm);
$subtotal = 0;
$ret = "<div>
			<div class='row'>
				<div class='col-3'>
				$buttonmenu
				</div>
				<div class='col-6'>
				<div align='center'>
					<img style='max-height: 100%; width: 80px;' src='../../images/lib/logo.jpeg'>
				</div>
				</div>
			</div>
			<div class='row'>
				<div class='col-md-12'>
				<div align='center' style='margin-top: 30px; font-size: 18px;'>
					<p>
						<span>Butcher Steak & Pasta Palembang</span><br>
						<span>Jl. AKBP Cek Agus No. 284, Palembang</span><br>
						<span>Sumatera Selatan, 30114, 07115626366</span>
					</p>
				</div>
				</div>
			</div>
			
		</div>";
$ret .= "<table width='100%' style='margin-top: 20px; font-size: 22px;'>
	        <tr>
	          <td align='left'>$dt</td>
	          <td align='right'>$tm</td>
	        </tr>
	        <tr>
	          <td align='left'>Bill Name</td>
	          <td align='right'>".$billname."</td>
	        </tr>
	        $collctedby
	      </table>
	      <hr style='border: 1px solid red'>
	      <table style='font-size: 22px;' width='100%'>";
foreach ($billing as $key) {
	$total = $key->produk_harga * $key->qty;
	$subtotal = $subtotal + $total;
	if ($key->statusbilling == 'verified' || $key->statusbilling == 'waiting') {
		$buttonqty = "";
	} else {
		$buttonqty = "<button onclick='minus($key->billing_item_id)' class='btn btn-success font-weight-bold' style='font-size: 25px; height: 25px; width: 25px; line-height: 15px; margin-left:5px;'>-</button>
	       		      <button onclick='add($key->billing_item_id)' class='btn btn-success font-weight-bold' style='font-size: 25px; height: 25px; width: 25px; line-height: 15px;'>+</button>";
	}
	
	$ret .= "<tr>
	        <td colspan='3' align='left' style='font-weight: bold;'>
	            $key->produk_nm
	          </td>
	        </tr>
	        <tr>
	        <input type='hidden' id='qty$key->billing_item_id' value='$key->qty'/>
	          <td align='left' ><span id='spanqty$key->billing_item_id'>$key->qty X </span> </td>
	          <td align='center'>@".number_format($key->produk_harga)."</td>
	          <td align='right'>".number_format($total)."</td>
	        </tr>
	        <tr style='line-height:40px;'>
	        <td>&nbsp </td>
	        <td></td>
	        <td></td>
	        </tr>";
	 }
	$ret .= "</table>
			<hr style='border: 1px solid red'>";

	$tax 		= $subtotal * 0.10;
	$service 	= $subtotal * 0.05;
	$grandtotal = $subtotal + $tax + $service;
	$nilai = round($grandtotal);
	$ratusan = substr($nilai, -3);
	if ($ratusan >= 100) {
	    $akhir = $grandtotal + (1000-$ratusan);
	} else {
	    $akhir = $grandtotal + (100-$ratusan);
	}

	$nilaibulat = $akhir - $grandtotal;

	$ret .= "<table style='margin-top:30px; font-size: 22px;' width='100%'>
	        <tr>
	          <td align='left'>Subtotal</td>
	          <td colspan='2' align='right'>Rp. ".number_format($subtotal)."</td>
	        </tr>
	        <tr>
	          <td align='left'>Tax</td>
	          <td colspan='2' align='right'>Rp. ".number_format($tax)."</td>
	        </tr>
	        <tr>
	          <td align='left'>service</td>
	          <td colspan='2' align='right'>Rp. ".number_format($service)."</td>
	        </tr>
	        <tr>
	          <td align='left'>Rounding Amount</td>
	          <td colspan='2' align='right'>Rp. ".number_format($nilaibulat)."</td>
	        </tr>
	        <tr>
	          <td align='left' style='font-weight:bold;'>Total</td>
	          <td colspan='2' align='right'>Rp. ".number_format($akhir)."</td>
	        </tr>
			</table>
			<hr style='border: 1px solid red;margin-bottom:100px;'>
			<div style='margin-bottom: 150px;'>
			$footer
			</div>";
	echo $ret;
	?>

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
function add(value){
  $("#loader-wrapper").removeClass("d-none");
  var currentVal = parseInt($("#qty" + value).val());    
  if (!isNaN(currentVal)) {
      var qty = $("#qty" + value).val(currentVal + 1);
      	$.ajax({
		   url : "<?= base_url('meja/updateqty')?>",
		   type: "POST",
		   data : {value:value,qty:qty},
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
      document.getElementById("spanqty"+value).textContent = currentVal + 1 + " X";
  }
  setTimeout(function(){ $("#loader-wrapper").addClass("d-none"); }, 1000);
};

function minus(value){
    $("#loader-wrapper").removeClass("d-none");
    var currentVal = parseInt($("#qty" + value).val());    
    if (currentVal==0) {
    } else if (!isNaN(currentVal)) {
        $("#qty" + value).val(currentVal - 1);
        document.getElementById("spanqty"+value).textContent = currentVal - 1 + " X";
    }
    setTimeout(function(){ $("#loader-wrapper").addClass("d-none"); }, 1000);
};

function listmenu() {
  window.location.href = "<?=base_url()?>/produk/listmenu2/"+<?= $uri->getSegment(3)?>;
}

function order(id) {
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