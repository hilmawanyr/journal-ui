<?php if ($this->session->flashdata('success_mail')) : ?>
	<div class="alert alert-dismissible alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Well done!</strong> <?= $this->session->flashdata('success_mail'); ?>
	</div>
<?php elseif ($this->session->flashdata('success_template')) : ?>
	<div class="alert alert-dismissible alert-success">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Well done!</strong> <?= $this->session->flashdata('success_template'); ?>
	</div>
<?php elseif ($this->session->flashdata('warning')) : ?>
	<div class="alert alert-dismissible alert-warning">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Oh snap!</strong> <?= $this->session->flashdata('warning'); ?>
	</div>
<?php elseif ($this->session->flashdata('danger')) : ?>
	<div class="alert alert-dismissible alert-danger">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong>Oh snap!</strong> <?= $this->session->flashdata('danger'); ?>
	</div>
<?php endif; ?>