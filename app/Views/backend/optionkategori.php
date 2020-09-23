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
                            <div class="card-body">
                              	<?= csrf_field(); ?>
                                <form id="upload-file" method="post" enctype="multipart/form-data">
                                    <div class="form-body">
                                        <div class="row p-t-20">
                                        	<div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">Kategori Produk</label>
                                                   <select class="form-control" name="kategori_id" id="kategori_id">
                                                   	<?php
                                                   		foreach ($kategori as $key) {
                                                   		echo "<option value='".$key['kategori_id']."'>".$key['kategori_nm']."</option>";       
                                                   		}
                                                   	?>
                                                   </select>
                                            </div>
                                        </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label class="control-label">Title Option</label>
                                                    <input type="text" id="option_nm" name="option_nm" class="form-control" placeholder="Option Title" required="">
                                                    <small class="form-control-feedback"> Contoh : Choose Your Doneness </small> </div>
                                            </div>
                                        </div>
                                       
                                    </div>
                                    <div class="form-actions">
                                        <button id="simpankat" type="button" class="btn btn-success" onclick="simpan()"> <i class="fa fa-check"></i> Save</button>
                                        <button type="button" class="btn btn-inverse">Cancel</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                   
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                        	<div class="card-header bg-info">
                                <h4 class="m-b-0 text-white d-inline">Tabel Data Kategori</h4>
                            </div>
                            <div class="card-body">
                               <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Judul</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Tanggal Entri</th>
                                                <!-- <th class="text-center">Pegawai</th> -->
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        	<?php 
                                        		$no=1;
                                        		foreach ($options as $k) {
                                        	?>

                                            <tr id="accordian-3">
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><a onclick="showedit(<?= $k->option_id ?>)"><span style="text-decoration:underline;" class="btn btn-link"><?= $k->option_nm; ?></span></a>
                                                	<ul>
                                                		<?php
                                                		$db = db_connect('default'); 
        												$builder = $db->table('options')->where('parent_id',$k->option_id)->where('status_cd','normal')->get();
                                                		foreach ($builder->getResult() as $key) {
                                                			
                                                		?>
                                                		<li>
                                                			<a onclick="showeditchild(<?= $key->option_id ?>)"><span style="text-decoration:underline;" class="btn btn-link p-0"><?=$key->option_nm?></span></a>
                                                			<a onclick="hapus(<?= $key->option_id ?>,'child')" class="btn btn-link p-0"><span style="color:red;">[-]</span></a>
                                                		</li>
                                                	<?php } ?>
                                                	</ul>
                                                </td>
                                                <td class="text-center"><?= $k->kategori_nm ?></td>
                                                <td class="text-center"><?= $k->created_dttm ?></td>
                                                <!-- <td><?= $k->created_user ?></td> -->
                                                <td class="text-center">
                                                    <a onclick="showedit(<?= $k->option_id ?>)"><span style="text-decoration:underline;" class="btn btn-link p-0">Edit</span></a> |
                                                    <a onclick="addchild(<?= $k->option_id ?>)"><span style="text-decoration:underline;" class="btn btn-link p-0">[+]Child</span></a> |
                                                    <a onclick="hapus(<?= $k->option_id ?>,'options')"><span style="text-decoration:underline;" class="btn btn-link p-0">Hapus</span></a>
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



    function simpan() {
        var option_nm = $('#option_nm').val();
        var kategori_id = $('#kategori_id').val();
        if (option_nm=='') {
        	Swal.fire({
            title:"Title options harus di isi!!",
            text:"GAGAL!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
                })
        } else {
            $.ajax({
            url : "<?= base_url('kategori/saveoption') ?>",
            type: "POST",
            data : {'option_nm':option_nm,'kategori_id':kategori_id},
            success:function(_data){
             if (_data=='already') {
                Swal.fire({
                    title:"Nama kategori sudah ada!!",
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
                 $( "#myTable" ).load("<?= base_url('kategori/option') ?> #myTable");
               // setTimeout(function(){ window.location.href = "<?=base_url()?>/kategori"; }, 1000);
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

function simpanchild(id) {
	var child_nm = $('#child_nm').val();
	$.ajax({
     url : "<?= base_url('kategori/savechild') ?>",
     type: "post",
     data : {'id':id,'child_nm':child_nm},
     success:function(data){
     if (data=='false') {
        Swal.fire({
         title:"Data gagal disimpan!!",
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
        $( "#myTable" ).load("<?= base_url('kategori/option') ?> #myTable");
               // setTimeout(function(){ window.location.href = "<?=base_url()?>/kategori"; }, 1000);
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

function showedit(id) {
    $.ajax({
     url : "<?= base_url('kategori/formeditoption') ?>",
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

function showeditchild(id){
	$.ajax({
     url : "<?= base_url('kategori/formeditchild') ?>",
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

function addchild(id){
	$.ajax({
     url : "<?= base_url('kategori/formaddchild') ?>",
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

function hapus(id,t) {
    $.ajax({
     url : "<?= base_url('kategori/hapus') ?>",
     type: "post",
     data : {'id':id,'t':t},
     success:function(){
      
        Swal.fire({
            title:"Berhasil!",
            text:"Data berhasil disimpan!",
            type:"success",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
         $( "#myTable" ).load("<?= base_url('kategori/option') ?> #myTable");
        // setTimeout(function(){ window.location.href = "<?=base_url()?>/kategori"; }, 1000);
    
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
	var option_nm = $('#option_nm2').val();
	var option_id = $('#option_id2').val();
    var kategori_id = $('#kategori_id2').val();
        if (option_nm=='') {
        	Swal.fire({
                    title:"Nama Option harus di isi!!",
                    text:"GAGAL!",
                    type:"warning",
                    showCancelButton:!0,
                    confirmButtonColor:"#556ee6",
                    cancelButtonColor:"#f46a6a"
                })
        } else {
            $.ajax({
            url : "<?= base_url('kategori/updateoption') ?>",
            type: "POST",
            data : {'kategori_id':kategori_id,'option_id':option_id,'option_nm':option_nm},
            success:function(_data){
             if (_data=='false') {
                Swal.fire({
                    title:"Data gagal disimpan!!",
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
                $( "#myTable" ).load("<?= base_url('kategori/option') ?> #myTable");
                //setTimeout(function(){ window.location.href = "<?=base_url()?>/kategori"; }, 1000);
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

function updatechild(id){
	var child_nm = $('#child_nm2').val();
	$.ajax({
     url : "<?= base_url('kategori/updatechild') ?>",
     type: "post",
     data : {'id':id,'child_nm':child_nm},
     success:function(data){
     if (data=='false') {
        Swal.fire({
         title:"Data gagal disimpan!!",
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
        $( "#myTable" ).load("<?= base_url('kategori/option') ?> #myTable");
               // setTimeout(function(){ window.location.href = "<?=base_url()?>/kategori"; }, 1000);
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
</script>

<?= $this->endSection(); ?>