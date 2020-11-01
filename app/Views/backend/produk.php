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
                                <h4 class="m-b-0 text-white d-inline-block">Tabel Data Produk</h4>
                                <button id="simpankat" type="button" class="btn btn-success d-inline-block float-right" onclick="tambahdata()"> <i class="fa fa-check"></i> Tambah Data</button>
                            </div>
                            <div class="card-body">
                               <div class="table-responsive">
                                    <table id="myTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Nama Produk</th>
                                                <th class="text-center">Kategori</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Tanggal Entri</th>
                                                <th class="text-center">Pegawai</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        	<?php 
                                        		$no=1;
                                        		foreach ($produk as $k) {
                                        	?>

                                             <tr id="accordian-3">
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td><a onclick="showedit(<?= $k->produk_id ?>)"><span style="text-decoration:underline;" class="btn btn-link"><?= $k->produk_nm ?> </span></a>
                                                    Rp. <?= number_format($k->produk_harga) ?>
                                                </td>
                                                <td class="text-center"><?= $k->kategori_nm ?></td>
                                                <td class="text-center"><?= $k->status_cd ?></td>
                                                <td class="text-center"><?= $k->created_dttm ?></td>
                                                <td><?= $k->user_nm ?></td>
                                                <td class="text-center">
                                                    <a class="btn btn-link" onclick="showedit(<?= $k->produk_id ?>)"><span style="text-decoration:underline;">Edit</span></a> |
                                                    <a class="btn btn-link" onclick="hapus(<?= $k->produk_id ?>)"><span style="text-decoration:underline;">Hapus</span></a>
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
            <div id="formadd" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->

              <script type="text/javascript">
  

    function tambahdata() {
    $.ajax({
     url : "<?= base_url('produk/formadd') ?>",
     type: "post",
     success:function(data){
     $('#formadd').modal('show');
     $('#formadd').html(data);
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
        var produk_id = $('#produk_id').val();
        var produk_nm = $('#produk_nm').val();
        var harga = $('#harga').val();
        var kategori_id = $('#kategori_id').val();
        if (produk_nm=='' || harga=='') {
        	Swal.fire({
                    title:"Nama Produk/Harga harus di isi!!",
                    text:"GAGAL!",
                    type:"warning",
                    showCancelButton:!0,
                    confirmButtonColor:"#556ee6",
                    cancelButtonColor:"#f46a6a"
                })
        } else {
            $.ajax({
            url : "<?= base_url('produk/save') ?>",
            type: "post",
            data : {'produk_nm':produk_nm,'harga':harga,'kategori_id':kategori_id,'produk_id':produk_id},
            success:function(){
           
                Swal.fire({
                    title:"Berhasil!",
                    text:"Data berhasil disimpan!",
                    type:"success",
                    showCancelButton:!0,
                    confirmButtonColor:"#556ee6",
                    cancelButtonColor:"#f46a6a"
                })
                $('#formadd').modal('hide');
                $( "#myTable" ).load("<?= base_url('produk') ?> #myTable");
            
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
     url : "<?= base_url('produk/formadd') ?>",
     type: "post",
     data : {'id':id},
     success:function(data){
     $('#formadd').modal('show');
     $('#formadd').html(data);
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
         url : "<?= base_url('produk/hapus') ?>",
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
            $('#formadd').modal('hide');
            $( "#myTable" ).load("<?= base_url('produk') ?> #myTable");
        
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

function update(id) {
	var Produk_nm = $('#produk_nm').val();
        if (Produk_nm=='') {
        	Swal.fire({
                    title:"Nama Produk harus di isi!!",
                    text:"GAGAL!",
                    type:"warning",
                    showCancelButton:!0,
                    confirmButtonColor:"#556ee6",
                    cancelButtonColor:"#f46a6a"
                })
        } else {
            $.ajax({
            url : "<?= base_url('produk/update') ?>",
            type: "post",
            data : {'Produk_nm':Produk_nm,'id':id},
            success:function(_data){
             if (_data=='already') {
                Swal.fire({
                    title:"Nama Produk sudah ada!!",
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
                $('#formadd').modal('hide');
                $( "#myTable" ).load("<?= base_url('produk') ?> #myTable");
                
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