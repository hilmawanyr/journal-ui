<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Journal</title>
	<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/font-awesome.min.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/jquery-ui.min.css') ?>">
	<link rel="stylesheet" href="<?= base_url('assets/summernote/summernote.min.css') ?>">
	<script type="text/javascript" src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
</head>
<body>

	<?php $this->load->view($nav); ?>

	<div class="container">

		<?php $this->load->view($page); ?>

	</div>

<script type="text/javascript" src="<?= base_url('assets/js/jquery-ui.min.js') ?>"></script>
<script
	src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
	integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
	crossorigin="anonymous"></script>
<script type="text/javascript" src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/summernote/summernote.min.js') ?>">

</script>
</body>
</html>
