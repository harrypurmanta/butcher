<?= $this->extend('backend/layout/template'); 
?>
    <!-- page css -->
    <link href="../../assets/css/pages/tab-page.css" rel="stylesheet">

    <?= $this->section('content'); ?>
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h3 class="text-themecolor"><?= $subtitle ?></h3>
                </div>
                <div class="col-md-7 align-self-center">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item">Pengaturan</li>
                        <li class="breadcrumb-item active"><?= $subtitle ?></li>
                    </ol>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
          		 <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                              <div class="col-md-12">
                              	<button onclick='tambahuser()' class="btn btn-success">Tambah User</button>
                              </div>
                              <div class="col-md-12 m-t-30">
                              	<table data-toggle="table"  data-mobile-responsive="true" class="table table-striped">
	                              	
	                              		<tr>
	                              			<th>No.</th>
	                              			<th>Username</th>
	                              			<th>Status</th>
	                              			<th>Action</th>
	                              		</tr>
	                              	<tbody>
	                              		<?php
	                              		$no = 1;
	                              			foreach ($user as $key) {
	                              		?>
	                              				<tr>
			                              			<td><?= $no++ ?></td>
			                              			<td><?= $key->user_nm ?></td>
			                              			<td><?= $key->status_cd ?></td>
			                              			<td></td>
			                              		</tr>
	                              			
	                              		<?php } ?>
	                              	</tbody>
	                              </table>
                              </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
            
            </div>
            <div id="modaluser" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->


<script type="text/javascript">
function tambahuser() {
	$.ajax({
       url : "<?= base_url('karyawan/formtambahkaryawan') ?>",
       type: "post",
       beforeSend: function () { 
          $("#loader-wrapper").removeClass("d-none")
       },
       success:function(data){
        $('#modaluser').html(data);
        $('#modaluser').modal('show');
        $("#loader-wrapper").addClass("d-none");
      },
      error:function(){
          Swal.fire({
              title:"Gagal!",
              text:"Data gagal disimpan!",
              type:"warning",
              showCancelButton:0,
              confirmButtonColor:"#556ee6",
              cancelButtonColor:"#f46a6a"
          })
      }

  });
}

function simpanuser() {
    var username = $('#user_nm').val();
    var password = $('#password').val();
    $.ajax({
       url : "<?= base_url('karyawan/simpanuser') ?>",
       type: "post",
       data: {username:username,password:password},
       beforeSend: function () { 
          $("#loader-wrapper").removeClass("d-none")
       },
       success:function(data){
        
      },
      error:function(){
          Swal.fire({
              title:"Gagal!",
              text:"Data gagal disimpan!",
              type:"warning",
              showCancelButton:0,
              confirmButtonColor:"#556ee6",
              cancelButtonColor:"#f46a6a"
          })
      }

  });
}
   


</script>
<?= $this->endSection(); ?>