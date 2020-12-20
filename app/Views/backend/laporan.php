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
                               <div class="row">
                               	<div class="col-md-12">
                               		<div class="form-group">
                               			<select class="select2 form-control custom-select" style="width: 100%; height:36px;">
                               				<option value="finish">Finish</option>
                               				<option value="cancel">Void</option>
                               				<option value="">Void</option>
                               				<option value="">Void</option>
                               				<option value="">Void</option>
                               			</select>
                               		</div>
                               		<div class="form-group">
                               			<label class="control-label">Start Dttm</label>
                               			<input type="date" class="form-control" placeholder="dd/mm/yyyy" name="start_dttm" id="start_dttm">
                               		</div>

                               		<div class="form-group">
                               			<label class="control-label">End Dttm</label>
                               			<input type="date" class="form-control" placeholder="dd/mm/yyyy" name="end_dttm" id="end_dttm">
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
$(document).ready(function($){
    laporan_content();
});

function laporan_content() {
	$.ajax({
    url : "<?= base_url('kasir/closekasir') ?>",
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