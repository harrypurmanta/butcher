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
                                <div class="card-body" id="cardbodymeja">
                                
                                </div>
                                <hr>
                                 <button style="font-size: 20px; width: 40%;" type="button" onclick="diskon()" class="btn btn-rounded btn-warning">Diskon</button>
                                 <button style="font-size: 20px; width: 40%;" type="button" onclick="member()" class="btn btn-rounded btn-primary">Member</button>
                                <div style="margin-top: 10px;">
                                  <button style="font-size: 20px; width: 40%;" type="button" onclick="trancshistori()" class="btn btn-rounded btn-info">Histori Transaksi</button>
                                  <button style="font-size: 20px; width: 40%;" type="button" onclick="trancshistori()" class="btn btn-rounded btn-info">Histori Transaksi</button>
                                </div>
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
            <div class="d-none" id='loader-wrapper'>
                <div class="loader"></div>
            </div>
            <div id="responsive-modal" class="modal modal-lg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                              
            </div>

            <div id="modaltambahmember" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                              
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <script src="../assets/plugins/jquery/jquery.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
  setInterval(function(){ 
    $.ajax({
       url : "<?= base_url('kasir/cardbodymeja') ?>",
       beforeSend: function () { 
          $("#loader-wrapper").removeClass("d-none")
       },
       success:function(data){
        $('#cardbodymeja').html(data);
        $("#loader-wrapper").addClass("d-none");
      },
      error:function(){meja
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
  }, 3000);
});

function formtambahmember() {
  $.ajax({
     url : "<?= base_url('kasir/formtambahmember') ?>",
     type: "post",
     success:function(data){
      $('#modaltambahmember').html(data);
      $('#modaltambahmember').modal('show');
      $('#responsive-modal').modal('hide');
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

function formtambahdiskon() {
  $.ajax({
     url : "<?= base_url('kasir/formtambahdiskon') ?>",
     success:function(data){
      $('#modaltambahmember').html(data);
      $('#modaltambahmember').modal('show');
      $('#responsive-modal').modal('hide');
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
          $('#modaltambahmember').modal('hide');
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

function simpandiskon() {
  var discount_nm = $('#namadiscount').val();
  var nilaidiscount = $('#nilaidiscount').val();
  if (discount_nm == "" || nilaidiscount == "") {
    Swal.fire({
        title:"Nama discount dan nilai harus di isi!!",
        text:"GAGAL!",
        type:"warning",
        showCancelButton:0,
        confirmButtonColor:"#556ee6",
        cancelButtonColor:"#f46a6a"
    })
  } else {
      $.ajax({
      url : "<?= base_url('discount/save') ?>",
      type: "post",
      data : {'discount_nm':discount_nm,'nilaidiscount':nilaidiscount},
      success:function(_data){
        if (_data=='already') {
          Swal.fire({
              title:"Nama discount sudah ada!!",
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
          $('#modaltambahmember').modal('hide');
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
    $.ajax({
       url : "<?= base_url('kasir/discountkasir') ?>",
       type: "post",
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
   $.ajax({
       url : "<?= base_url('kasir/memberkasir') ?>",
       type: "post",
       success:function(data){
        $('#responsive-modal').html(data);
        $('#responsive-modal').modal('show');
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

function addDiscount(di) {
    var id = $('#meja_id').val();
    var bi = $('#billing_id').val();
    if (id == undefined) {
      Swal.fire({
          title:"PILIH MEJA TERLEBIH DAHULU!",
          text:"error",
          type:"warning",
          showCancelButton:0,
          confirmButtonColor:"#556ee6",
          cancelButtonColor:"#f46a6a"
      })
    } else {
      $.ajax({
       url : "<?= base_url('kasir/adddiscounttobill') ?>",
       type: "post",
       data: {id:id,di:di,bi:bi},
       success:function(data){
        if (data == 'true') {
          showbillingbymeja(id);
          $('#responsive-modal').modal('hide');
        } else if (data == 'already') {
          Swal.fire({
              title:"Diskon sudah digunakan!",
              text:"error",
              type:"warning",
              showCancelButton:0,
              confirmButtonColor:"#556ee6",
              cancelButtonColor:"#f46a6a"
          })
        } else {
          Swal.fire({
              title:"Gagal!",
              text:"Data gagal disimpan!",
              type:"warning",
              showCancelButton:0,
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
    
}

function addmember(di){
    var id = $('#meja_id').val();
    var bi = $('#billing_id').val();
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


  
}

function removedcmember(id,di,discount_id) {
  $.ajax({
     url : "<?= base_url('kasir/removedcmember') ?>",
     type: "post",
     data: {id:id,di:di,discount_id:discount_id},
     success:function(data){
      if (data == 'true') {
        showbillingbymeja(id);
        $('#responsive-modal').modal('hide');
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
    
function removedc(id,di,discount_id) {
  $.ajax({
     url : "<?= base_url('kasir/removedc') ?>",
     type: "post",
     data: {id:id,di:di,discount_id:discount_id},
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

function removemember(id,billing_id) {
  $.ajax({
     url : "<?= base_url('kasir/removemember') ?>",
     type: "post",
     data: {id:id,billing_id:billing_id},
     success:function(data){
      if (data == 'true') {
        showbillingbymeja(id);
      } else {
        Swal.fire({
            title:"Member gagal dihapus!",
            text:"Error!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
      }
    },
    error:function(){
        Swal.fire({
            title:"Member gagal dihapus!",
            text:"Error!",
            type:"warning",
            showCancelButton:!0,
            confirmButtonColor:"#556ee6",
            cancelButtonColor:"#f46a6a"
        })
    }
  });
}

function cetakmenu(id,btn) {
  cetakmenudrinks(id,btn);
  setTimeout(
  function() 
  {
    cetakmenufood(id,btn);
  }, 10000);
}

function cetakmenudrinks(id,btn) {
    b = $(btn);
      b.attr('data-old', b.text());
      b.text('wait');
      $.ajax({
         url : "<?= base_url('kasir/cetakmenudrinks') ?>",
         type: "post",
         data: {id:id},
         success:function(data){
          if (data == "false") {
              Swal.fire({
                title:"Tidak ada order Minuman!",
                text:"Data tidak di print!",
                type:"warning",
                showCancelButton:!0,
                confirmButtonColor:"#556ee6",
                cancelButtonColor:"#f46a6a"
            })
          } else {
              window.location.href = data;
              showbillingbymeja(id);
          }
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

function cetakmenufood(id,btn){
    b = $(btn);
      b.attr('data-old', b.text());
      b.text('wait');
      $.ajax({
         url : "<?= base_url('kasir/cetakmenufood') ?>",
         type: "post",
         data: {id:id},
         success:function(data){
          if (data == "false") {
              Swal.fire({
                title:"Tidak ada order Makanan!",
                text:"Data tidak di print!",
                type:"warning",
                showCancelButton:!0,
                confirmButtonColor:"#556ee6",
                cancelButtonColor:"#f46a6a"
            })
          } else {
              window.location.href = data;
              showbillingbymeja(id);
          }
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
var billing_id = $('#billing_id').val();
  b = $(btn);
  b.attr('data-old', b.text());
  b.text('wait');
  $.ajax({
     url : "<?= base_url('kasir/cetakbilling') ?>",
     type: "post",
     data: {id:id,billing_id:billing_id},
     success:function(data){
      showbillingbymeja(id);
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
  var paymen_tunai = $("input[name='paymen_tunai']").val();
  var paymen_tunai_id = $("input[name=paymen_tunai]").data('paymen-id');
  var payplan_value = $("input[name='payplan']").val();
  var payplan_value_id = $("input[name=payplan]:checked").data('payplan-id');
  if (paymen_tunai == "") {
    var paid = payplan_value;
    var payplan_id = payplan_value_id;
  } else {
    var paid = paymen_tunai;
    var payplan_id = paymen_tunai_id;
  }

  if (gt > paid) {
    Swal.fire({
        title:"Nilai input terlalu kecil dari total billing !!",
        text:"Error !!",
        type:"warning",
        showCancelButton:0,
        confirmButtonColor:"#556ee6",
        cancelButtonColor:"#f46a6a"
    })
  } else {
    b = $(btn);
    b.attr('data-old', b.text());
    b.text('wait');
    Swal.fire({
          title: 'Apakah anda yakin?',
          text: "Setelah tekan yes, data tidak dapat dikembalikan",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
          if (result.value == true) {
              $.ajax({
                 url : "<?= base_url('kasir/cetakcheckout') ?>",
                 type: "post",
                 data: {id:id,gt:gt,meja_id:meja_id,billing_id:billing_id,payplan_id:payplan_id,paid:paid},
                 success:function(data){
                  if (data == 'false') {
                    Swal.fire({
                        title:"Data tidak ada !!",
                        text:"Silahkan refresh halaman !!",
                        type:"warning",
                        showCancelButton:0,
                        confirmButtonColor:"#556ee6",
                        cancelButtonColor:"#f46a6a"
                    })
                  } else {
                    $('#responsive-modal').modal('hide');
                    window.location.href = data;
                    b.text(b.attr('data-old'));
                    showbillingbymeja(id);

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
       })
  }
  

  
}
</script>

<?= $this->endSection(); ?>