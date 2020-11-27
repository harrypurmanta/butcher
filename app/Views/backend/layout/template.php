<?php
$this->session = \Config\Services::session();
$this->session->start();
?>
<!DOCTYPE html>
<html lang="en">
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
    <link href="<?=base_url() ?>/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
    <!-- This page CSS -->
    <link rel="stylesheet" href="<?=base_url() ?>/assets/plugins/dropify/dist/css/dropify.min.css">
    <!--alerts CSS -->
    <link href="<?=base_url() ?>/assets/plugins/sweetalert2/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?=base_url() ?>/assets/css/style.css" rel="stylesheet">
    <link href="<?=base_url() ?>/assets/css/custom.css" rel="stylesheet">
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
           
     <?php if ($this->session->user_group != "kasir"): ?>
          <header class='topbar'>
            <nav class='navbar top-navbar navbar-expand-md navbar-light'>
                <!-- ============================================================== -->
                <!-- Logo -->
                <!-- ============================================================== -->
                <div class='navbar-header'>
                    <a class='navbar-brand' href='index.html'>
                        <!-- Logo icon --><b>
                            <!--You can put here icon as well // <i class='wi wi-sunset'></i> //-->
                            <!-- Dark Logo icon -->
                            <img src='<?=base_url() ?>/assets/images/logo-icon.png' alt='homepage' class='dark-logo' />
                            <!-- Light Logo icon -->
                            <img src='<?=base_url() ?>/assets/images/logo-light-icon.png' alt='homepage' class='light-logo' />
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text --><span>
                         <!-- dark Logo text -->
                         <img src='<?=base_url() ?>/assets/images/logo-text.png' alt='homepage' class='dark-logo' />
                         <!-- Light Logo text -->    
                         <img src='<?=base_url() ?>/assets/images/logo-light-text.png' class='light-logo' alt='homepage' /></span> </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class='collapse navbar-collapse' id='navbarSupportedContent'>
                    <ul class='navbar-nav mr-auto'>
                        <li class='nav-item'> 
                            <a class='nav-link' href='#'>
                                <span class='hide-menu'>Home </span>
                            </a>
                        </li>
                        <li class='nav-item dropdown'> 
                            <a class='nav-link dropdown-toggle nav-item' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                Pengaturan
                            </a>
                            <div class='dropdown-menu' aria-labelledby='navbarDropdown'>
                                <a class='dropdown-item' href='<?=base_url() ?>/kategori'>Kategori Produk</a>
                                <!-- <a class='dropdown-item' href='<?=base_url() ?>/subkat'>Sub Kategori Produk</a> -->
                                <!-- <a class='dropdown-item' href='<?=base_url() ?>/kategori/option'>Kategori Options</a> -->
                                <a class='dropdown-item' href='<?=base_url() ?>/produk'>Produk</a>
                                <a class='dropdown-item' href='<?=base_url() ?>/discount'>Diskon</a>
                                <a class='dropdown-item' href='<?=base_url() ?>/payplan'>Cara Bayar</a>
                                <a class='dropdown-item' href='<?=base_url() ?>/membertype'>Tipe Member</a>
                                <a class='dropdown-item' href='<?=base_url() ?>/meja'>Meja</a>
                                <a class='dropdown-item' href='<?=base_url() ?>/karyawan'>Karyawan</a>
                            </div>
                        </li>
                        <li class='nav-item'> 
                            <a class='nav-link' href='<?=base_url() ?>/produk/listmenu'>
                                <span class='hide-menu'>Menu Item </span>
                            </a>
                        </li>
                        <li class='nav-item'> 
                            <a class='nav-link' href='<?=base_url() ?>/member'>
                                <span class='hide-menu'>Member </span>
                            </a>
                        </li>
                        <li class='nav-item'> 
                            <a class='nav-link' href='<?=base_url() ?>/kasir'>
                                <span class='hide-menu'>Kasir </span>
                            </a>
                        </li>
                        <li class='nav-item'> 
                            <a class='nav-link' href='#'>
                                <span class='hide-menu'>Laporan </span>
                            </a>
                        </li>
                    </ul>
                    <ul class='navbar-nav my-lg-0'>
                        <!-- ============================================================== -->
                        <!-- Profile -->
                        <!-- ============================================================== -->
                        <li class='nav-item dropdown'>
                            <a class='nav-link dropdown-toggle waves-effect waves-dark' href='#' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><img src='<?=base_url() ?>/assets/images/users/1.jpg' alt='user' class='profile-pic' /></a>
                            <div class='dropdown-menu dropdown-menu-right animated flipInY'>
                                <ul class='dropdown-user'>
                                    <li>
                                        <div class='dw-user-box'>
                                            <div class='u-img'><img src='<?=base_url() ?>/assets/images/users/1.jpg' alt='user'></div>
                                            <div class='u-text'>
                                                <h4>Steave Jobs</h4>
                                                <p class='text-muted'><a href='https://www.wrappixel.com/cdn-cgi/l/email-protection' class='__cf_email__' data-cfemail='6f190e1d1a012f08020e0603410c0002'>[email&#160;protected]</a></p><a href='pages-profile.html' class='btn btn-rounded btn-danger btn-sm'>View Profile</a></div>
                                        </div>
                                    </li>
                                    <li role='separator' class='divider'></li>
                                    <li><a href='#'><i class='ti-user'></i> My Profile</a></li>
                                    <li><a href='#'><i class='ti-wallet'></i> My Balance</a></li>
                                    <li><a href='#'><i class='ti-email'></i> Inbox</a></li>
                                    <li role='separator' class='divider'></li>
                                    <li><a href='#'><i class='ti-settings'></i> Account Setting</a></li>
                                    <li role='separator' class='divider'></li>
                                    <li><a href='<?=base_url()?>/login/logout'><i class='fa fa-power-off'></i>Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
     <?php endif ?>

       <?= $this->renderSection('content'); ?>

            <footer class="footer"> © 2020 Lavita Bella </footer>
        </div>
    </div>
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