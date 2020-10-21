<?= $this->extend('backend/layout/template'); 
?>

    <?= $this->section('content'); ?>
        <div class="page-wrapper" style="padding-top: 0px; margin-left: 0px !important;">
            
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                      <div class="row">
                        <div class="col-md-5 col-sm-6">
                            <div class="card" style="display: inline-block; padding-bottom: 20px;" align="center">
                                <div class="card-body">
                                <?php
                                  foreach ($meja->getResult() as $key) {
                                ?>
                                <div style="display: inline-block; margin: 5px;">
                                  <button onclick="showbillingbymeja(<?= $key->meja_id ?>)" class="btn btn-info font-weight-bold" style="font-size: 20px; padding: 10px;"><?= $key->meja_nm?></button>
                                </div>
                                <?php } ?>
                                </div>
                                <hr>
                                 <button style="font-size: 20px; width: 40%;" type="button" onclick="diskon()" class="btn btn-rounded btn-warning">Diskon</button>
                                 <button style="font-size: 20px; width: 40%;" type="button" onclick="member()" class="btn btn-rounded btn-primary">Member</button>
                            </div>
                        </div>
                        <div class="col-md-7 col-sm-6">
                            <div class="card">
                                <div class="card-body" id="cardbody"> 
                                </div>
                            </div>
                        </div>
                      </div>
            </div>
            <div id="responsive-modal" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                              
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->

<script type="text/javascript">
function showbillingbymeja(id) {
    $.ajax({
     url : "<?= base_url('kasir/getbymejaidkasir') ?>",
     type: "post",
     data : {'id':id},
     success:function(data){
      $('#cardbody').html(data);
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

function diskon() {
    var id = $('#meja_id').val();
    var billing_id = $('#billing_id').val();
    if (id == undefined) {
      Swal.fire({
        title:"PILIH MEJA TERLEBIH DAHULU!",
        text:"Data gagal ditampilkan!",
        type:"warning",
        showCancelButton:!0,
        confirmButtonColor:"#556ee6",
        cancelButtonColor:"#f46a6a"
      })
    } else {
      $.ajax({
       url : "<?= base_url('kasir/discountkasir') ?>",
       type: "post",
       data: {id:id,billing_id:billing_id},
       success:function(data){
        $('#responsive-modal').html(data);
        $('#responsive-modal').modal('show');
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

function showcheckout(id,gt) {
    var id = $('#meja_id').val();
    if (id == undefined) {
      Swal.fire({
        title:"PILIH MEJA TERLEBIH DAHULU!",
        text:"Data gagal ditampilkan!",
        type:"warning",
        showCancelButton:!0,
        confirmButtonColor:"#556ee6",
        cancelButtonColor:"#f46a6a"
      })
    } else {
      $.ajax({
       url : "<?= base_url('kasir/showcheckout') ?>",
       type: "post",
       data: {id:id,gt:gt},
       success:function(data){
        $('#responsive-modal').html(data);
        $('#responsive-modal').modal('show');
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

function member() {
    var id = $('#meja_id').val();
    var billing_id = $('#billing_id').val();
    if (id == undefined) {
      Swal.fire({
        title:"PILIH MEJA TERLEBIH DAHULU!",
        text:"Data gagal ditampilkan!",
        type:"warning",
        showCancelButton:!0,
        confirmButtonColor:"#556ee6",
        cancelButtonColor:"#f46a6a"
      })
    } else {
      $.ajax({
       url : "<?= base_url('kasir/memberkasir') ?>",
       type: "post",
       data: {id:id,billing_id:billing_id},
       success:function(data){
        $('#responsive-modal').html(data);
        $('#responsive-modal').modal('show');
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

function addDiscount(id,di,bi) {
    $.ajax({
     url : "<?= base_url('kasir/adddiscounttobill') ?>",
     type: "post",
     data: {id:id,di:di,bi:bi},
     success:function(data){
      if (data == 'true') {
        showbillingbymeja(id);
      } else {
        Swal.fire({
            title:"Gagal!",
            text:"Data gagal disimpan!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
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

function addmember(id,di,bi){
  $.ajax({
     url : "<?= base_url('kasir/addmembertobill') ?>",
     type: "post",
     data: {id:id,di:di,bi:bi},
     success:function(data){
      if (data == 'true') {
        showbillingbymeja(id);
        $('#responsive-modal').modal('hide');
      } else {
        Swal.fire({
            title:"Gagal!",
            text:"Data gagal disimpan!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
        
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

function removedcmember(id,di) {
  $.ajax({
     url : "<?= base_url('kasir/removedcmember') ?>",
     type: "post",
     data: {id:id,di:di},
     success:function(data){
      if (data == 'true') {
        showbillingbymeja(id);
      } else {
        Swal.fire({
            title:"Error!",
            text:"Data gagal disimpan!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
      }
    },
    error:function(){
        Swal.fire({
            title:"Error!",
            text:"Data gagal disimpan!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
    }
  });
}
    
function removedc(id,di) {
  $.ajax({
     url : "<?= base_url('kasir/removedc') ?>",
     type: "post",
     data: {id:id,di:di},
     success:function(data){
      if (data == 'true') {
        showbillingbymeja(id);
      } else {
        Swal.fire({
            title:"Error!",
            text:"Data gagal disimpan!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
      }
    },
    error:function(){
        Swal.fire({
            title:"Error!",
            text:"Data gagal disimpan!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
    }
  });
}

function cetakmenu(id,btn) {
  b = $(btn);
  b.attr('data-old', b.text());
  b.text('wait');
  $.ajax({
     url : "<?= base_url('kasir/cetakmenu') ?>",
     type: "post",
     data: {id:id},
     success:function(data){
      window.location.href = data;
      b.text(b.attr('data-old'));
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

function cetakbilling(id,btn) {
  b = $(btn);
  b.attr('data-old', b.text());
  b.text('wait');
  $.ajax({
     url : "<?= base_url('kasir/cetakbilling') ?>",
     type: "post",
     data: {id:id},
     success:function(data){
      window.location.href = data;
      b.text(b.attr('data-old'));
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

function checkout(id,gt,btn) {
  var meja_id = $('#meja_id').val();
  var billing_id = $('#billing_id').val();
  var paid = $("input[name='payplan']").val();
  var payplan_id = $("input[name=payplan]:checked").data('payplan-id');;
  b = $(btn);
  b.attr('data-old', b.text());
  b.text('wait');
  $.ajax({
     url : "<?= base_url('kasir/cetakcheckout') ?>",
     type: "post",
     data: {id:id,gt:gt,meja_id:meja_id,billing_id:billing_id,payplan_id:payplan_id,paid:paid},
     success:function(data){
      window.location.href = data;
      b.text(b.attr('data-old'));
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