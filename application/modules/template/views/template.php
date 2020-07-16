<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>UI Journal</title>
  <link rel="shortcut icon" href="<?= base_url('assets/admin-lte/img/logo.ico'); ?>"/>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?= base_url('assets/admin-lte/bower_components/bootstrap/dist/css/bootstrap.min.css') ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('assets/admin-lte/bower_components/font-awesome/css/font-awesome.min.css') ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?= base_url('assets/admin-lte/bower_components/Ionicons/css/ionicons.min.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('assets/admin-lte/dist/css/AdminLTE.min.css') ?>">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?= base_url('assets/admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') ?>">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="<?= base_url('assets/admin-lte/dist/css/skins/_all-skins.min.css') ?>">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
  <!-- jQuery 3 -->
  <script src="<?= base_url('assets/admin-lte/bower_components/jquery/dist/jquery.min.js') ?>"></script>
  <!-- Summernote -->
  <link rel="stylesheet" href="<?= base_url('assets/summernote/summernote.min.css') ?>">
</head>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<style>
  body {
    font-family: 'Quicksand', sans-serif;
  }

  hr {
    border-color: #f0f0f0 !important;
  }
</style>
<body class="hold-transition skin-black-light layout-top-nav">
<div class="wrapper">

  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <a href="<?= base_url('/') ?>" class="navbar-brand"><b>UI.</b>JOURNAL</a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
          <ul class="nav navbar-nav">

            <!-- menu list -->

            <!-- end menu list -->

          </ul>
        </div>
        <!-- /.navbar-collapse -->
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <?php if (!$this->session->userdata('login_sess')) : ?>
              <li class="messages-menu"><a href="#">About</a></li>
              <li class="messages-menu"><a href="#">Tools</a></li>
              <li class="messages-menu"><a href="#">Disclaimer</a></li>
            <?php else : ?>
              <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-bell-o"></i>
                  <span class="label label-warning">10</span>
                </a>
                <ul class="dropdown-menu">
                  <li class="header">You have 10 notifications</li>
                  <li class="footer"><a href="#">View all</a></li>
                </ul>
              </li>
              <li>
                <a href="<?= base_url('mail') ?>">
                  <i class="fa fa-envelope-o"></i>
                </a>
              </li>
              <li>
                <a href="<?= base_url('invitation') ?>">
                  <i class="fa fa-user-plus"></i>
                </a>
              </li>
            <?php endif; ?>

            <!-- User Account Menu -->
            <?php if ($this->session->userdata('login_sess')) : ?>
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <span class="hidden-xs">
                    <?= $this->session->userdata('login_sess')['name'] ?>
                  </span>
                </a>
                <ul class="dropdown-menu">
                  <li class="user-header">
                    <p><?= $this->session->userdata('login_sess')['name'] ?></p>
                  </li>
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="#" class="btn btn-default btn-flat">Profile</a>
                    </div>
                    <div class="pull-right">
                      <a href="<?= base_url('logout') ?>" class="btn btn-default btn-flat">
                        Sign out
                      </a>
                    </div>
                  </li>
                </ul>
              </li>
            <?php else : ?>
              <li class="messages-menu"><a href="<?= base_url('auth') ?>">Login</a></li>
            <?php endif; ?>

          </ul>
        </div>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container">
      <?php $this->load->view($page); ?>
      <!-- /.content -->
    </div>
    <!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="container">
      <div class="pull-right hidden-xs">
        <b>Version</b> 1.0.0
      </div>
      <strong>Copyright &copy; 2020 <a href="javascript:void(0)">EDUTEKNO</a>.</strong>
    </div>
    <!-- /.container -->
  </footer>
</div>
<!-- ./wrapper -->

<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url('assets/admin-lte/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
<script src="<?= base_url('assets/admin-lte/bower_components/datatables.net/js/jquery.dataTables.min.js') ?>"></script>
<!-- DataTables -->
<script src="<?= base_url('assets/admin-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('assets/admin-lte/dist/js/adminlte.min.js') ?>"></script>
<!-- Summernote -->
<script type="text/javascript" src="<?= base_url('assets/summernote/summernote.min.js') ?>"></script>
<script>
  $(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
  });

  $(function () {
    $('#example1').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
</script>
</body>
</html>
