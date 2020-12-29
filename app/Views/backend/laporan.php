<?= $this->extend('backend/layout/template'); 
?>
    

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
          
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                            	<div class="col-md-12">
                               		<div class="row">
                               			<div class="form-group col-2">
                               				<label class="control-label">Tipe</label>
	                               			<select id="status_cd" class="select2 form-control custom-select">
	                               				<option value="closed">Closed</option>
	                               				<option value="cancel">Void</option>
	                               			</select>
	                               		</div>
	                               			<div class="form-group col-2">
		                               			<label class="control-label">Start Dttm</label>
		                               			<input type="date" class="form-control" placeholder="dd/mm/yyyy" name="start_dttm" id="start_dttm">
		                               		</div>

		                               		<div class="form-group col-2">
		                               			<label class="control-label">End Dttm</label>
		                               			<input type="date" class="form-control" placeholder="dd/mm/yyyy" name="end_dttm" id="end_dttm">
		                               		</div>
		                               		<div class="form-group col-2">
		                               			<button onclick="submitfilterlaporan()" class="btn btn-info">Submit</button>
		                               		</div>
                               	</div>
                               </div>

                               <div class="row">
                               	<div class="col-md-12">
                               		<div id="laporan_content">
                               			
                               		</div>
                               	</div>
                               </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
            <div id="modaledit" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->

<script type="text/javascript">

function submitfilterlaporan() {
	var status_cd = $('#status_cd').val();
	var start_dttm = $('#start_dttm').val();
	var end_dttm = $('#end_dttm').val();
	$.ajax({
    url : "<?= base_url('laporan/reportclosekasir') ?>",
    type: 'post',
    data: {status_cd:status_cd,start_dttm:start_dttm,end_dttm:end_dttm},
    beforeSend: function () { 
      $("#loader-wrapper").removeClass("d-none")
    },
     success:function(data){
      $("#loader-wrapper").addClass("d-none");
      $('#laporan_content').html(data);
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
<?= $this->endSection(); ?>