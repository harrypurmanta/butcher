<?= $this->extend('backend/layout/template'); 
?>

    <?= $this->section('content'); ?>
        <div class="page-wrapper" style="padding-top: 0px; margin-left: 0px !important;">
            
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                      <div class="row">
                        <div class="col-md-6">
                            <div class="card" style="padding-bottom: 20px;">
                                <input type="hidden" id="value-meja"/>
                                <div class="card-body" id="cardbodymeja">
                                
                                </div>
                                <hr>
                                <div align="center">
                                  <div>
                                   <button style="font-size: 20px; width: 40%;" type="button" onclick="diskon()" class="btn btn-rounded btn-warning">Diskon</button>
                                   <button style="font-size: 20px; width: 40%;" type="button" onclick="member()" class="btn btn-rounded btn-primary">Member</button>
                                  </div>
                                 <!--  <div style="margin-top: 10px;">
                                    <button style="font-size: 20px; width: 40%;" type="button" onclick="trancshistori()" class="btn btn-rounded btn-info">Histori Transaksi</button>
                                    <button style="font-size: 20px; width: 40%;" type="button" onclick="trancshistori()" class="btn btn-rounded btn-info">Histori Transaksi</button>
                                  </div> -->
                                </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="card">
                                  <div class="card-body">
                                    <button type="button" onclick="closekasir()" class="btn btn-outline-info waves-effect waves-light"><i class="fas fa-file-archive fa-3x"></i></button>
                                    <button class="btn btn-outline-info waves-effect waves-light"><i class="fas fa-clipboard-list fa-3x"></i></button>
                                    
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-6">
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
            <!-- <script src="../assets/js/perfect-scrollbar.jquery.min.js"></script> -->
<script type="text/javascript">
// $('#cardbodymeja').perfectScrollbar();
$(document).ready(function(){
  // setInterval(function(){ 
    listmejakasir();
  // }, 2000);
  
});

function openkasir() {
  $.ajax({
    url : "<?= base_url('kasir/openkasir') ?>",
    beforeSend: function () { 
      $("#loader-wrapper").removeClass("d-none")
    },
     success:function(data){
      $("#loader-wrapper").addClass("d-none");
      $('#modaltambahmember').html(data);
      $('#modaltambahmember').modal('show');
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

function closekasir() {
  $.ajax({
    url : "<?= base_url('kasir/closekasir') ?>",
    beforeSend: function () { 
      $("#loader-wrapper").removeClass("d-none")
    },
     success:function(data){
      $("#loader-wrapper").addClass("d-none");
      $('#modaltambahmember').html(data);
      $('#modaltambahmember').modal('show');
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

function simpanopenkasir(){
  var open_dttm = $('#open_dttm').val();
  var nilaimodal = $('#nilaimodal').val();
  Swal.fire({
          title: 'Apakah anda yakin?',
          text: "Setelah tekan yes, data tidak dapat dikembalikan",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, confirm it!'
      }).then((result) => {
          if (result.value == true) {
              $.ajax({
                 url : "<?= base_url('kasir/simpanopenkasir') ?>",
                 type: "post",
                 data: {open_dttm:open_dttm,nilaimodal:nilaimodal},
                 success:function(data){
                  if (data == "belumfinish") {
                    Swal.fire({
                        title:"Ada billing yang belum di selesaikan !!",
                        text:"Silahkan selesaikan terlebih dahulu !!",
                        type:"warning",
                        showCancelButton:0,
                        confirmButtonColor:"#556ee6",
                        cancelButtonColor:"#f46a6a"
                    })
                  } else if (data == 'false') {
                    Swal.fire({
                        title:"Data tidak ada !!",
                        text:"Silahkan refresh halaman !!",
                        type:"warning",
                        showCancelButton:0,
                        confirmButtonColor:"#556ee6",
                        cancelButtonColor:"#f46a6a"
                    })
                  } else {
                    $('#modaltambahmember').modal('hide');
                    listmejakasir();
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

function simpanclosekasir(){
  var closed_dttm = $('#closed_dttm').val();
  Swal.fire({
          title: 'Apakah anda yakin?',
          text: "Setelah tekan yes, data tidak dapat dikembalikan",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, confirm it!'
      }).then((result) => {
          if (result.value == true) {
              $.ajax({
                 url : "<?= base_url('kasir/simpanclosekasir') ?>",
                 type: "post",
                 data: {closed_dttm:closed_dttm},
                 success:function(data){
                  if (data == "belumfinish") {
                    Swal.fire({
                        title:"Ada billing yang belum di selesaikan !!",
                        text:"Silahkan selesaikan terlebih dahulu !!",
                        type:"warning",
                        showCancelButton:0,
                        confirmButtonColor:"#556ee6",
                        cancelButtonColor:"#f46a6a"
                    })
                  } else if (data == 'false') {
                    Swal.fire({
                        title:"Data tidak ada !!",
                        text:"Silahkan refresh halaman !!",
                        type:"warning",
                        showCancelButton:0,
                        confirmButtonColor:"#556ee6",
                        cancelButtonColor:"#f46a6a"
                    })
                  } else {
                    $('#modaltambahmember').modal('hide');
                    listmejakasir();
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

function listmejakasir() {
  $.ajax({
       url : "<?= base_url('kasir/cardbodymeja') ?>",
       beforeSend: function () { 
          $("#loader-wrapper").removeClass("d-none")
       },
       success:function(data){
        $('#cardbodymeja').html(data);
        $("#loader-wrapper").addClass("d-none");
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



function btntambahpesanan(id) {
  $.ajax({
    url : "<?= base_url('kasir/daftarkategorikasir') ?>",
    type: "post",
    beforeSend: function () { 
      $("#loader-wrapper").removeClass("d-none")
    },
     success:function(data){
      $('#cardbodymeja').html(data);
      $("#loader-wrapper").addClass("d-none");
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

function clickkategori(id) {
  var meja_id = $("#meja_id").val();
  $("#value-meja").val(meja_id);
  $.ajax({
     url : "<?= base_url('kasir/getprodukbykategori') ?>",
     type: "post",
     data: {id:id},
     beforeSend: function () { 
        $("#loader-wrapper").removeClass("d-none")
    },
     success:function(data){
      $('#cardbodymeja').html(data);
      $("#loader-wrapper").addClass("d-none");
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

function addproduk(produk_id) {
  $.ajax({
     url : "<?= base_url('kasir/showadddetail') ?>",
     type: "post",
     data: {produk_id:produk_id},
     beforeSend: function () { 
        $("#loader-wrapper").removeClass("d-none")
    },
     success:function(data){
      $("#loader-wrapper").addClass("d-none");
      $('#modaltambahmember').html(data);
      $('#modaltambahmember').modal('show');
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

function simpanproduk(produk_id) {
  var meja_id = $("#value-meja").val();
  var jumlah = $("#jumlah").val();
  var catatan = $("#catatan").val();
  $.ajax({
      url : "<?= base_url('kasir/addproduktobill') ?>",
      type: "POST",
      data: {produk_id:produk_id,meja_id:meja_id,jumlah:jumlah,catatan:catatan},
      success:function(data){
        if (data == 'true') {
          showbillingbymeja(meja_id);
          $('#modaltambahmember').modal('hide');
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
      beforeSend: function () { 
          $("#loader-wrapper").removeClass("d-none")
      },
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
        $("#loader-wrapper").addClass("d-none");
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
      beforeSend: function () { 
          $("#loader-wrapper").removeClass("d-none")
      },
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
        $("#loader-wrapper").addClass("d-none");
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
  $("#value-meja").val(id);
    $.ajax({
     url : "<?= base_url('kasir/getbymejaidkasir') ?>",
     type: "post",
     data : {'id':id},
     success:function(data){
      var _data = JSON.parse(data);
      if (_data.status == 'kategori') {
        $('#cardbodymeja').html(_data.produk);
        $('#cardbody').html(_data.billing);
      } else {
        $('#cardbodymeja').html(_data.produk);
        $('#cardbody').html(_data.billing);
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
          confirmButtonText: 'Yes, confirm it!'
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

  // if (gt > paid) {
  //   Swal.fire({
  //       title:"Nilai input terlalu kecil dari total billing !!",
  //       text:"Error !!",
  //       type:"warning",
  //       showCancelButton:0,
  //       confirmButtonColor:"#556ee6",
  //       cancelButtonColor:"#f46a6a"
  //   })
  // } else {
   
  // }
}

function removeitem(meja_id,id) {
  Swal.fire({
      title: 'Yakin menghapus item ini ?',
      text: "item yang sudah dihapus tidak bisa dikembalikan lagi, tapi anda bisa memesan lagi",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yakin'
  }).then((result) => {
      if (result.value == true) {
        $.ajax({
         url : "<?= base_url('kasir/setnullifieditem')?>",
         type : "POST",
         data : {'value':id},
         beforeSend: function () { 
            $("#loader-wrapper").removeClass("d-none");
         },
         success:function(){
            $("#loader-wrapper").addClass("d-none");
          showbillingbymeja(meja_id);
        },
        error:function(){
          Swal.fire(
            'Gagal!',
            'Silahkan Coba Lagi.',
            'warning'
          )
        }
      });
        
      }
   });
}

function addjumlah(){
  var currentVal = parseInt($("#jumlah").val());
  if (!isNaN(currentVal)) {
    $("#jumlah").val(currentVal + 1);
  }
};

function minusjumlah(){
  var currentVal = parseInt($("#jumlah").val());
  if (!isNaN(currentVal)) {
      if (currentVal == 0) {
      } else {
          $("#jumlah").val(currentVal - 1);
      }
  }
  
};

function additem(value){
  var currentVal = parseInt($("#inputqty"+value).val());
  $("#inputqty"+value).val(currentVal + 1);
  $("#jumlahqty"+value).val(currentVal + 1);
  var id = $("#value-meja").val();
  var quanty = currentVal + 1;
  if (!isNaN(currentVal)) {
      $.ajax({
        url : "<?= base_url('kasir/updateqty') ?>",
        type: "post",
        data : {'value':value,'quanty':quanty},
        beforeSend: function () { 
          $("#loader-wrapper").removeClass("d-none");
        },
        success:function(data){
          $("#loader-wrapper").addClass("d-none");
          showbillingbymeja(id);
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
};

function minusitem(value){
    var currentVal = parseInt($("#inputqty"+value).val());
    $("#inputqty"+value).val(currentVal - 1);
    $("#jumlahitem"+value).val(currentVal - 1);  
    var qty = currentVal - 1;     
    var id = $("#value-meja").val();
    if (qty==0) {
        Swal.fire({
            title: 'Yakin menghapus item ini ?',
            text: "item yang sudah dihapus tidak bisa dikembalikan lagi, tapi anda bisa memesan lagi",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yakin'
        }).then((result) => {
            if (result.value == true) {
              $.ajax({
               url : "<?= base_url('kasir/setnullifieditem')?>",
               type : "POST",
               data : {'value':value},
               beforeSend: function () { 
                  $("#loader-wrapper").removeClass("d-none");
               },
               success:function(){
                  $("#loader-wrapper").addClass("d-none");
                showbillingbymeja(id);
              },
              error:function(){
                Swal.fire(
                  'Gagal!',
                  'Silahkan Coba Lagi.',
                  'warning'
                )
              }
            });
              
            }
         });
    } else if (!isNaN(currentVal)) {
        $.ajax({
          url : "<?= base_url('kasir/updateqty') ?>",
          type: "post",
          data : {'value':value,'quanty':qty},
          beforeSend: function () { 
            $("#loader-wrapper").removeClass("d-none");
          },
          success:function(data){
            $("#loader-wrapper").addClass("d-none");
            showbillingbymeja(id);
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
};
</script>

<?= $this->endSection(); ?>