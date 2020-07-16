<section class="content">
	<div class="row">
		<div class="col-lg-3 col-md-3 col-xs-12">
			<div class="box box-warning">
				<div class="box-body">
					<h5 class="card-title"><i class="fa fa-filter"></i> Filter</h5>
					<hr>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Enter DOI">
					</div>
					<div class="form-group">
						<select class="form-control">
							<option selected="" disabled="">Subject</option>
							<option value="1"></option>
							<option value="2"></option>
						</select>
					</div>
					<div class="form-group">
						<select class="form-control">
							<option selected="" disabled="">Article</option>
							<option value="1"></option>
							<option value="2"></option>
						</select>
					</div>
					<div class="form-group">
						<select class="form-control">
							<option selected="" disabled="">Institute</option>
							<option value="1"></option>
							<option value="2"></option>
						</select>
					</div>
					<button type="button" class="btn btn-warning">
						<i class="fa fa-search"></i> Search
					</button>
				</div>
			</div>
		</div>

		<div class="col-lg-9 col-md-9 col-xs-12">
			<div class="box box-warning">
				<div class="box-body">
					<form action="<?= base_url('search') ?>" method="get">
						<input type="hidden" name="source" value="<?= $this->session->userdata('HOST'); ?>">
						<div class="form-group">
							<div class="input-group input-group-lg">
					            <input
									type="text"
									name="keyword"
									value="<?= $this->input->get('keyword') ?>"
									class="form-control form-control-lg"
									placeholder="Enter keyword"
									required="" />
					            <span class="input-group-btn">
					              	<button type="button" class="btn btn-warning btn-flat">
					              		<i class="fa fa-search"></i>
					              	</button>
					            </span>
				            </div>
						</div>
					</form>
					<div class="breadcrumb">
						<span class="breadcrumb-item active">
							<b>Page: <?= !is_null($this->input->get('page')) ? (($this->input->get('page')/10)+1) : 1; ?> / </b>
						</span>
						<span class="breadcrumb-item">Result: <?= $total ?> / </span>
						<span class="breadcrumb-item">
							<b>Export List: <span id="total-citation"></span></b>
						</span>
					</div>

					<?php foreach ($list as $item) : ?>
						<blockquote class="blockquote">
							<h4><?= $item['title'] ?></h4>
							<p class="mb-0 text-muted">
								<?= ucwords(str_replace('-', ' ', $item['type'])) ?>
							</p>
							<p class="mb-0" style="font-size: 14px">DOI:
								<?= $item['doi'] ?>
							</p>
							<p class="mb-0" style="font-size: 14px">Authors:
								<?= $item['author'] ?>
							</p>

							<?php if (is_array($item['url'])) : ?>
								<?php foreach ($item['url'] as $urls) : ?>
									<a href="<?= $urls ?>" style="font-size: 14px">
										<i class="fa fa-link"></i>
										<?= $urls ?>
									</a>
								<?php endforeach; ?>
							<?php else : ?>
								<a href="<?= $item['url'] ?>" style="font-size: 14px">
									<i class="fa fa-link"></i>
									<?= $item['url'] ?>
								</a>
							<?php endif; ?>
							
						</blockquote>
						<button
							type="button"
							class="btn btn-warning"
							id="export-btn-<?= md5($item['doi']) ?>"
							onclick="add_to_export_list('<?= $item['doi'] ?>','<?= md5($item['doi']) ?>')">
							<i class="fa fa-plus" id="invite-icon"></i> Add to export list
						</button>

						<div class="btn-group">
							<button type="button" class="btn btn-warning">Action</button>
							<button
								type="button"
								class="btn btn-warning dropdown-toggle"
								data-toggle="dropdown"
								aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<?php 
								$search = ['.', '/', '-'];
								$replace = [':::', ':', '::'];
								$encode_doi = str_replace($search,$replace,$item['doi']); 
							?>
							<ul class="dropdown-menu" role="menu">
								<li>
									<a
										href="#myModal"
										data-toggle="modal"
								  		onclick="detail('<?= $encode_doi ?>')">
								  	Detail
								  </a>
								</li>
								<li>
									<a href="<?= base_url('xml_export/'.$encode_doi) ?>" target="_blank">Export XML</a>
									<a href="<?= base_url('csv_export/'.$encode_doi) ?>" target="_blank">Export CSV</a>
								</li>
							</ul>
						</div>
						<hr>
					<?php endforeach; ?>

					<?= $this->pagination->create_links(); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<div class="modal fade" id="myModal">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title">Detail</h5>
			</div>
			<div class="modal-body" id="body">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="inviteModal">
	<div class="modal-dialog modal-lg" role="dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title">Invite</h5>
			</div>
			<form action="<?= base_url('invite') ?>" method="post" class="form-horizontal">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="from" class="col-form-label col-sm-2">From</label>
								<div class="col-sm-10">
									<input type="text" value="" class="form-control" id="from" required="">
								</div>
							</div>
							<div class="form-group">
								<label for="to" class="col-form-label col-sm-2">To</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="recipient" id="to" readonly="" required="">
								</div>
							</div>
							<div class="form-group">
								<label for="subject" class="col-form-label col-sm-2">Subject</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="subject" id="subject" required="">
								</div>
							</div>
						</div>
					</div>
					<textarea id="summernote" name="message" required=""></textarea>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn bg-yellow"><i class="fa fa-paper-plane"></i> Send</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	function invite(arg) {
		$('#to').val(arg);
	}

	$(document).ready(function() {
	    // summernote plugin
	    $('#summernote').summernote({
	      value: 'lalala',
	      height: 180,
	      toolbar: [
	        ['style', ['style']],
	        ['font', ['bold', 'underline', 'italic', 'clear']],
	        ['color', ['color']],
	        ['para', ['ul', 'ol', 'paragraph']],
	        ['table', ['table']],
	        ['insert', ['link']],
	        ['view', ['fullscreen', 'codeview', 'help']]
	      ]
	    });
	});

	function detail (doi) {
		$.ajax({
			url: '<?= base_url('article/detail/') ?>' + doi,
			beforeSend: function() {
				$('#body').html(`
					<center>
						<img src="<?= base_url('assets/img/loading.gif') ?>" style="width: 30%;">
					</center>
				`)
			},
			success: function(res) {
				$('#body').html(res);
			}
		})
	}


	var number = document.getElementById('total-citation');
	number.innerHTML = !localStorage.getItem('articles')
		? 0
		: JSON.parse(localStorage.getItem('articles')).length;

	function add_to_export_list(doi, btnID) {
		var articleCollection = localStorage.getItem('articles')
			? JSON.parse(localStorage.getItem('articles'))
			: [];

		var doiObj = {DOI: doi};

		var art = articleCollection.map(function(idx) { return idx.DOI }).indexOf(doi);
		if (art !== -1) { articleCollection.splice(art, 1); }

		console.log(articleCollection)

		articleCollection.push(doiObj)
		localStorage.setItem('articles', JSON.stringify(articleCollection))

		number.innerHTML = JSON.parse(localStorage.getItem('articles')).length;
	    $("i", "#export-btn-"+btnID).toggleClass("fa fa-plus fa fa-minus");
	}
</script>
