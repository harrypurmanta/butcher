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
                              	<?= csrf_field(); ?>
                                    <div class="form-body">
                                        <div class="row p-t-20">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label"><strong>Cari nama karyawan</strong></label>
                                                    <input type="text" id="employee_nm" class="form-control form-control-lg"  required="">
                                                </div>
                                            </div>
                                        </div>
                                       
                                    </div>
                                    <div class="form-actions">
                                        <button id="caribtn" type="button" class="btn btn-success" onclick="cariemployee()"> <i class="fa fa-search"></i> Cari</button>
                                        <button onclick="window.location.href = '<?=base_url()?>/karyawan/formdaftarkaryawan';" type="button" class="btn btn-inverse">Daftarkan Karyawan</button>
                                    </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
            
            </div>
            <div id="modalkaryawanlist" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                <h4 class="modal-title">Silahkan Pilih Pasien</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body" id="modal_body">

                    </div>
                </div>
                </div>
                                    
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->

              <script type="text/javascript">

    var input = document.getElementById("employee_nm");
    
    input.addEventListener("keyup", function(event) {
      // Number 13 is the "Enter" key on the keyboard
      if (event.keyCode === 13) {
        event.preventDefault();
        document.getElementById("caribtn").click();
      }
    });

    function cariemployee() {
        var person_nm   = $('#employee_nm').val();
        $.ajax({
                url : "<?= base_url('karyawan/cariByname') ?>",
                type: "post",
                data : {'person_nm':person_nm},
                success:function(data){
                    $('#modalkaryawanlist').modal('show');
                    $('#modal_body').html(data);
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


    function clickpatient(id){
        window.location.href = "<?=base_url()?>/karyawan/formdaftarkaryawan/"+id;
    }


</script>
<?= $this->endSection(); ?>