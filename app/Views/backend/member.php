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
                        	<div class="card-header bg-info">
                                <h4 class="m-b-0 text-white d-inline">Tabel Data member</h4>
                                <button type="button" onclick="tambahmember()" class="btn btn-success float-right">Tambah Data</button>
                            </div>
                            <div class="card-body">
                               <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Nama member</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Tanggal Entri</th>
                                                <th class="text-center">Pegawai</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        	<?php 
                                        		$no=1;
                                        		foreach ($member as $k) {
                                        	?>

                                            <tr id="accordian-3">
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><a onclick="showedit(<?= $k->member_id ?>)"><span style="text-decoration:underline;" class="btn btn-link"><?= $k->person_nm ?></span></a>
                                                </td>
                                                <td class="text-center"><?= $k->status_cd ?></td>
                                                <td class="text-center"><?= $k->created_dttm ?></td>
                                                <td><?= $k->created_user ?></td>
                                                <td class="text-center">
                                                    <a onclick="showedit(<?= $k->person_id ?>)"><span style="text-decoration:underline;" class="btn btn-link">Edit</span></a> |
                                                    <a onclick="hapus(<?= $k->person_id ?>)"><span style="text-decoration:underline;">Hapus</span></a>
                                                </td>
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
            <div id="modaledit" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                              
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->

              <script type="text/javascript">

function tambahmember() {
	$.ajax({
     url : "<?= base_url('member/formtambah') ?>",
     type: "post",
     success:function(data){
      //_data = JSON.parse(data);
     $('#modaledit').modal('show');
     $('#modaledit').html(data);
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


    function simpan() {
        var person_nm   = $("#person_nm").val();
        var cellphone   = $("#cellphone").val();
        var gender_cd   = $("#gender_cd").val();
        var email       = $("#email").val();
        var ext_id      = $("#ext_id").val();
        var birth_place = $("#birth_place").val();
        var birth_dttm  = $("#birth_dttm").val();
        var addr_txt    = $("#addr_txt").val();
        if (person_nm == "" || cellphone == "") {
        	Swal.fire({
            title:"Nama member harus di isi!!",
            text:"GAGAL!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
            })
        } else {
            var ajaxData = new FormData();
            ajaxData.append('action','forms');
            ajaxData.append('person_nm',person_nm);
            ajaxData.append('cellphone',cellphone);
            ajaxData.append('gender_cd',gender_cd);
            ajaxData.append('email',email);
            ajaxData.append('ext_id',ext_id);
            ajaxData.append('birth_place',birth_place);
            ajaxData.append('birth_dttm',birth_dttm);
            ajaxData.append('addr_txt',addr_txt);
            $.ajax({
            url : "<?= base_url('member/save') ?>",
            type: "post",
            data : ajaxData,
            contentType: false,
            processData: false,
            success:function(data){
             if (data=='Error') {
                Swal.fire({
                    title:"Error coba lagi !!",
                    text:"GAGAL!",
                    type:"warning",
                    showCancelButton:!0,
                    confirmButtonColor:"#556ee6",
                    cancelButtonColor:"#f46a6a"
                })
             } else {
                Swal.fire({
                    title:"Berhasil!",
                    text:"Data berhasil disimpan!",
                    type:"success",
                    showCancelButton:!0,
                    confirmButtonColor:"#556ee6",
                    cancelButtonColor:"#f46a6a"
                })
                $('#modaledit').modal('hide');
                $( "#myTable" ).load("<?= base_url('member') ?> #myTable");
                }
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
}

function showedit(id) {
    $.ajax({
     url : "<?= base_url('member/formedit') ?>",
     type: "post",
     data : {'id':id},
     success:function(data){
      //_data = JSON.parse(data);
     $('#modaledit').modal('show');
     $('#modaledit').html(data);
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

function hapus(id) {
    $.ajax({
     url : "<?= base_url('member/hapus') ?>",
     type: "post",
     data : {'id':id},
     success:function(){
      
        Swal.fire({
            title:"Berhasil!",
            text:"Data berhasil disimpan!",
            type:"success",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
        $( "#myTable" ).load("<?= base_url('member') ?> #myTable");
        
    
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

function update(id) {
    var person_nm   = $("#person_nm").val();
    var cellphone   = $("#cellphone").val();
    var gender_cd   = $("#gender_cd").val();
    var email       = $("#email").val();
    var ext_id      = $("#ext_id").val();
    var birth_place = $("#birth_place").val();
    var birth_dttm  = $("#birth_dttm").val();
    var addr_txt    = $("#addr_txt").val();
    if (id == "" || person_nm == "" || cellphone == "") {
    	Swal.fire({
            title:"Error!!",
            text:"GAGAL!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
    } else {
        var ajaxData = new FormData();
         ajaxData.append('action','forms');
         ajaxData.append('person_nm',person_nm);
         ajaxData.append('cellphone',cellphone);
         ajaxData.append('gender_cd',gender_cd);
         ajaxData.append('email',email);
         ajaxData.append('ext_id',ext_id);
         ajaxData.append('birth_place',birth_place);
         ajaxData.append('birth_dttm',birth_dttm);
         ajaxData.append('addr_txt',addr_txt);
         ajaxData.append('id',id);
        $.ajax({
        url : "<?= base_url('member/update') ?>",
        type: "POST",
        data : ajaxData,
        contentType: false,
        processData: false,
        success:function(_data){
         if (_data=='false') {
            Swal.fire({
                title:"Error silahkan coba lagi!!",
                text:"GAGAL!",
                type:"warning",
                showCancelButton:!0,
                confirmButtonColor:"#556ee6",
                cancelButtonColor:"#f46a6a"
            })
         } else if (_data=='true'){
            Swal.fire({
                title:"Berhasil!",
                text:"Data berhasil disimpan!",
                type:"success",
                showCancelButton:!0,
                confirmButtonColor:"#556ee6",
                cancelButtonColor:"#f46a6a"
            })
            $('#modaledit').modal('hide');
            $( "#myTable" ).load("<?= base_url('member') ?> #myTable");
            }
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
}
</script>

<?= $this->endSection(); ?>