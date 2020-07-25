<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>UI.Journal | Log in</title>
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
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url('assets/admin-lte/plugins/iCheck/square/blue.css') ?>">
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap">
</head>
<style media="screen">
  .login-page {
    background: #fdfdfd !important;
  }
</style>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?= base_url('auth') ?>"><b>UI</b>Journal</a>
  </div>

  <?php if ($this->session->flashdata('wrong_password')) : ?>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="icon fa fa-frown-o"></i> Oops!
      <?= $this->session->flashdata('wrong_password') ?>
    </div>
  <?php endif; ?>

  <?php if ($this->session->flashdata('account_not_found')) : ?>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <i class="icon fa fa-frown-o"></i> Oops!
      <?= $this->session->flashdata('account_not_found') ?>
    </div>
  <?php endif; ?>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Please sign in</p>

    <form action="<?= base_url('attemp_login') ?>" method="post">
      <div class="form-group has-feedback">
        <input type="email" class="form-control" name="username" placeholder="Email" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        <input type="hidden" name="has_args" value="<?= isset($email) ? $email : ''; ?>">
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" name="password" placeholder="Password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>
    <br>
    <a href="#">I forgot my password</a><br>
    <a href="register.html" class="text-center">Register a new membership</a>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="<?= base_url('assets/admin-lte/bower_components/jquery/dist/jquery.min.js') ?>"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url('assets/admin-lte/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
<!-- iCheck -->
<script src="<?= base_url('assets/admin-lte/lugins/iCheck/icheck.min.js') ?>"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
</body>
</html>
