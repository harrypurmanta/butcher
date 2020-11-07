<?php
$uri = current_url(true);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Waiters Dashboard</title>
  <!-- Chrome, Firefox OS and Opera -->
    <meta name="theme-color" content="#dc0000">
    <!-- Windows Phone -->
    <meta name="msapplication-navbutton-color" content="#dc0000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-status-bar-style" content="#dc0000">
    <!-- iOS Safari -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    
	<link href="../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
    <link href="../assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">

</head>
<body>
<div class="col-lg-12 col-md-12">
<div class="container-fluid" id="container_content">
	

</div>

</div>
<div class="d-none" id='loader-wrapper'>
    <div class="loader"></div>
</div>
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/plugins/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="../assets/plugins/sweetalert2/sweet-alert.init.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	setInterval(function(){ 
		$.ajax({
		 url : "<?= base_url('meja/viewmejawaiters') ?>",
			success:function(data){
			  $('#container_content').html(data);
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
	}, 3000);
	
});
</script>
</body>
</html>