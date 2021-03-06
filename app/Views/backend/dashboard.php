<?= $this->extend('backend/layout/template'); ?>
    

    <?= $this->section('content'); ?>
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper" style="margin-top: 35px;">
            
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
            
                <!-- ============================================================== -->
                <!-- Stats box -->
                <!-- ============================================================== -->
                
                <!-- ============================================================== -->
                <!-- Sales overview chart -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div>
                                        <h3 class="card-title m-b-5"><span class="lstick"></span>Sales Overview </h3>
                                    </div>
                                    <div class="ml-auto">
                                        <select class="custom-select b-0">
                                            <option selected="">January 2017</option>
                                            <option value="1">February 2017</option>
                                            <option value="2">March 2017</option>
                                            <option value="3">April 2017</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="chartContainer" style="height: 400px; width: 100%;"></div>
                               
                            </div>
                        </div>
                    </div>
                
                </div>
                
              
            </div>
            <?php
 

    

    // echo json_encode($dataPoints);
?>
            <script src="<?=base_url() ?>/assets/js/canvas.min.js"></script>
            <script>
                window.onload = function () {
                    var dataCustomer = null;
                    $.ajax({
                        url: "<?= base_url('laporan/chartCustomer') ?>",
                        type: 'POST',
                        dataType: 'json',
                        // data: {
                        //     start: start.unix(),
                        //     end: end.unix()
                        // },
                        success: function(data) {
                            dataCustomer = data;
                            // alert(dataCustomer);

                            var chart = new CanvasJS.Chart("chartContainer", {
                                animationEnabled: true,
                                theme: "light2",
                            
                                axisY: {
                                    scaleBreaks: {
                                        autoCalculate: true
                                    }
                                },
                                data: [{
                                    type: "column",
                                    yValueFormatString: "#,##0",
                                    indexLabel: "{y}",
                                    indexLabelPlacement: "inside",
                                    indexLabelFontColor: "white",
                                    dataPoints: dataCustomer,
                                }]
                            });
                            chart.render();
                        }
                    });
                 
                
                 
                }
                </script>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
<?= $this->endSection(); ?>