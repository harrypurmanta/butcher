<?php
$this->session = \Config\Services::session();
$this->session->start();
?>
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from www.wrappixel.com/demos/admin-templates/admin-pro/main/index4.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 24 Jun 2020 15:07:17 GMT -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url() ?>/assets/images/favicon.png">
    <title><?= $title; ?></title>
    <!-- Bootstrap Core CSS -->
    <link href="<?=base_url() ?>/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="<?=base_url() ?>/assets/plugins/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url() ?>/assets/plugins/datatables.net-bs4/css/responsive.dataTables.min.css">
    <!-- This page CSS -->
    <link rel="stylesheet" href="<?=base_url() ?>/assets/plugins/dropify/dist/css/dropify.min.css">
    <!--alerts CSS -->
    <link href="<?=base_url() ?>/assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?=base_url() ?>/assets/css/style.css" rel="stylesheet">
    <!-- You can change the theme colors from here -->
    <link href="<?=base_url() ?>/assets/css/colors/default-dark.css" id="theme" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>


<body class="fix-header fix-sidebar card-no-border">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Lavita Bella</p>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
           
        
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->

       <?= $this->renderSection('content'); ?>

         <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer"> © 2020 Lavita Bella </footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="<?=base_url() ?>/assets/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap popper Core JavaScript -->
    <script src="<?=base_url() ?>/assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="<?=base_url() ?>/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="<?=base_url() ?>/assets/js/perfect-scrollbar.jquery.min.js"></script>
    <!--Wave Effects -->
    <script src="<?=base_url() ?>/assets/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="<?=base_url() ?>/assets/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="<?=base_url() ?>/assets/js/custom.min.js"></script>
    <!-- ============================================================== -->
    <!-- This page plugins -->
    <!-- ============================================================== -->
    <!-- Sweet-Alert  -->
    <script src="<?=base_url() ?>/assets/plugins/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="<?=base_url() ?>/assets/plugins/sweetalert2/sweet-alert.init.js"></script>
    <!-- This is data table -->
    <script src="<?=base_url() ?>/assets/plugins/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?=base_url() ?>/assets/plugins/datatables.net-bs4/js/dataTables.responsive.min.js"></script>
    <!--sparkline JavaScript -->
    <script src="<?=base_url() ?>/assets/plugins/sparkline/jquery.sparkline.min.js"></script>
    <!-- jQuery file upload -->
    <script src="<?=base_url() ?>/assets/plugins/dropify/dist/js/dropify.min.js"></script>
    <script>
      
        $(function () {
            $('#myTable').DataTable();
            $(document).ready(function () {
                var table = $('#example').DataTable({
                    "columnDefs": [{
                        "visible": false,
                        "targets": 2
                    }],
                    "order": [
                        [2, 'asc']
                    ],
                    "displayLength": 25,
                    "drawCallback": function (settings) {
                        var api = this.api();
                        var rows = api.rows({
                            page: 'current'
                        }).nodes();
                        var last = null;
                        api.column(2, {
                            page: 'current'
                        }).data().each(function (group, i) {
                            if (last !== group) {
                                $(rows).eq(i).before('<tr class="group"><td colspan="5">' + group + '</td></tr>');
                                last = group;
                            }
                        });
                    }
                });
                // Order by the grouping
                $('#example tbody').on('click', 'tr.group', function () {
                    var currentOrder = table.order()[0];
                    if (currentOrder[0] === 2 && currentOrder[1] === 'asc') {
                        table.order([2, 'desc']).draw();
                    } else {
                        table.order([2, 'asc']).draw();
                    }
                });
            });
        });
        $('#example23').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
        $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
        $('#config-table').DataTable({
            responsive: true
        });
    </script>
    <!-- ============================================================== -->
    <!-- Style switcher -->
    <!-- ============================================================== -->
    <script src="<?=base_url() ?>/assets/plugins/styleswitcher/jQuery.style.switcher.js"></script>
</body>


<!-- Mirrored from www.wrappixel.com/demos/admin-templates/admin-pro/main/index4.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 24 Jun 2020 15:07:21 GMT -->
</html>