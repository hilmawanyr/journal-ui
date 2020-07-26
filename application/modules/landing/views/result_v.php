<section class="content">
	<div class="row">
		<div class="col-lg-3 col-md-3 col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					<form action="<?= base_url('filter_search') ?>" method="get">
						<h5 class="card-title"><i class="fa fa-filter"></i> Filter</h5>
						<hr>
						<div class="form-group">
							<select class="form-control" required="" name="keyword">
								<option selected="" disabled="">Subject</option>
								<?php foreach ($subjects as $subject) : ?>
									<option 
										value="<?= $subject->value ?>"
										<?= $_GET['keyword'] == $subject->value ? 'selected=""' : ''; ?>>
										<?= $subject->label ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<select class="form-control" name="filter" required="">
								<option selected="" disabled="">Article</option>
								<?php foreach ($types as $value) : ?>
									<option 
										value="<?= $value->id ?>"
										<?= isset($_GET['filter'])
												? ($_GET['filter'] == $value->id ? 'selected=""' : '')
												: ''; ?>>
										<?= $value->label ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<!-- <div class="form-group">
							<select class="form-control">
								<option selected="" disabled="">Institute</option>
								<option value="1"></option>
								<option value="2"></option>
							</select>
						</div> -->
						<button type="submit" class="btn btn-primary">
							<i class="fa fa-search"></i> Search
						</button>
					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-9 col-md-9 col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					<div class="row">
						<form action="<?= base_url('search') ?>" method="get" class="col-md-10">
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
						              	<button type="submit" class="btn btn-primary btn-flat">
						              		<i class="fa fa-search"></i>
						              	</button>
						            </span>
					            </div>
							</div>
						</form>
						<div class="">
							<div class="btn-group">
								<button type="button" class="btn btn-primary btn-lg">Export</button>
								<button
									type="button"
									class="btn btn-primary btn-lg dropdown-toggle"
									data-toggle="dropdown"
									aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<?php 
										$keyword = urlencode($this->input->get('keyword'));
										$filter  = null !== $this->input->get('filter') 
													? $this->input->get('filter') 
													: '';

										if ($this->session->userdata('HOST') == 'CRF') {
											$offset = null !== $this->input->get('page') 
														? $this->input->get('page') 
														: 0;											
										} else {
											$page = isset($_GET['page']) ? $_GET['page'] : 0;
											$offset = array_search($page, $this->session->userdata('cm'));
										}
										$param = str_replace('=', '', base64_encode($keyword.'|'.$filter.'|'.$offset));
									 ?>
									<li>
										<a 
											href="<?= base_url('export_all_xml/'.$param) ?>" 
											target="_blank">
											Export Result to XML
										</a>
										<a 
											href="<?= base_url('export_all_csv/'.$param) ?>" 
											target="_blank">
											Export Result to CSV
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="breadcrumb">
						<span class="breadcrumb-item active">
							<b>Page: <?= !is_null($this->input->get('page')) ? (($this->input->get('page')/25)+1) : 1; ?> / </b>
						</span>
						<span class="breadcrumb-item">Result: <?= $total ?> / </span>
						<!-- <span class="breadcrumb-item">
							<b>Export List: <span id="total-citation"></span></b>
						</span> -->
					</div>
					
					<?php if (count($list) !== 0) {

						foreach ($list as $item) : ?>
							<blockquote class="blockquote">
								<?= isset($item->notFound) ? $item->notFound : '' ?>
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
							<!-- <button
								type="button"
								class="btn btn-primary"
								id="export-btn-<?= md5($item['doi']) ?>"
								onclick="add_to_export_list('<?= $item['doi'] ?>','<?= md5($item['doi']) ?>')">
								<i class="fa fa-plus" id="invite-icon"></i> Add to export list
							</button> -->
							<?php $encode_doi = str_replace('=', '', base64_encode($item['doi'])); ?>
							<button
								type="button"
								class="btn btn-primary"
								data-toggle="modal"
								data-target="#myModal"
								onclick="detail('<?= $encode_doi ?>')">
								<i class="fa fa-list" id="invite-icon"></i> Detail
							</button>

							<div class="btn-group">
								<button type="button" class="btn btn-primary">Action</button>
								<button
									type="button"
									class="btn btn-primary dropdown-toggle"
									data-toggle="dropdown"
									aria-expanded="false">
									<span class="caret"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<!-- <li>
										<a
											href="#myModal"
											data-toggle="modal"
									  		onclick="detail('<?= $encode_doi ?>')">
									  	Detail
									  </a>
									</li> -->
									<li>
										<a href="<?= base_url('xml_export/'.$encode_doi) ?>" target="_blank">Export XML</a>
										<a href="<?= base_url('csv_export/'.$encode_doi) ?>" target="_blank">Export CSV</a>
									</li>
								</ul>
							</div>
							<hr>
						<?php endforeach; 
						
					} else {
						echo '<blockquote class="blockquote">No data found.</blockquote>';
					} ?>
					
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

<script>
	function detail (doi) {
		$.ajax({
			url: '<?= base_url('article/') ?>' + doi + '/detail',
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
