<style>
	.input-group-text {
		background-color: #fff !important;
	}

	.push-row {
		margin-top: 7rem !important;
	}

	.center-content {
		margin: 7% auto !important;
		float: none !important;
		text-align: center;
	}
</style>
<section class="content push-row">
  	<div class="row">
  		<div class="col-md-12 col-xs-12">
			<form action="<?= base_url('search') ?>" method="get">
				<div class="form-group">
					<div class="col-md-8 col-xs-12 center-content">
						<h1>Search</h1>
						<div class="form-group">
							<select class="form-control" name="source" required="">
								<option disabled="" selected="" value="">Select source</option>
								<option value="CRF">Crossref</option>
								<option value="PMC">EuropePMC</option>
							</select>
						</div>
						<div class="input-group input-group-lg">
				            <input
								type="text"
								name="keyword"
								class="form-control form-control-lg"
								placeholder="Enter keyword"
								required="" />
				          	<span class="input-group-btn">
				            	<button type="submit" class="btn btn-primary btn-flat">
									<i class="fa fa-search"></i>
								</button>
				          	</span>
	          			</div>
					</div>
				</div>
			</form>
			<?php if ($this->session->flashdata('unvalid_search')) : ?>
				<div class="col-md-4 col-xs-12 center-content">
					<div class="alert alert-warning alert-dismissible">
		                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		               	<?= $this->session->flashdata('unvalid_search'); ?>
		            </div>
				</div>
			<?php endif; ?>
  		</div>
	</div>
</section>
