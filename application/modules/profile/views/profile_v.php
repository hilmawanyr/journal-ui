<section class="content">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-xs-12">
			<div class="box box-primary">
				<div class="box-body">
					<img 
						class="profile-user-img img-responsive img-circle" 
						src="<?= base_url('assets/img/user-default.png') ?>" 
						alt="User profile picture">

					<h3 class="profile-username text-center"><?= $summary['given_name'].' '.$summary['fam_name'] ?></h3>
					<p class="text-muted text-center"><a href="<?= $summary['uri_orcid'] ?>"><?= $summary['uri_orcid'] ?></a></p>
					
					<ul class="list-group list-group-unbordered">
						<li class="list-group-item">
							<b>ORCID ID</b> <a class="text-muted pull-right"><?= $summary['orcid_id'] ?></a>
						</li>
						<li class="list-group-item">
							<b>Works</b> <a class="text-muted pull-right"><?= $summary['number_of_works'] ?></a>
						</li>
					</ul>

					<a href="javascript:void(0);" onclick="history.go(-1)" class="btn btn-primary"><b>&laquo; Back</b></a>
				</div>
			</div>
		</div>

		<div class="col-lg-8 col-md-8 col-xs-12">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#summary" data-toggle="tab">Summary</a></li>
					<li><a href="#works" data-toggle="tab">Works</a></li>
				</ul>
				<div class="tab-content">
					<div class="active tab-pane" id="summary">
						
						<div class="callout">
			                <h4>Biography</h4>
			                <p><?= !empty($summary['biography']) ? $summary['biography'] : '-' ?></p>
			            </div>

			            <div class="callout">
			                <h4>External Identifier</h4>
			                <?php if (empty($summary['ext_identifier'])) {
			                	echo '-';
			                } else { ?>
			                	<ul>
				                	<?php foreach ($summary['ext_identifier'] as $key => $value) : ?>
				                		<li>
				                			<b>Type: </b><?= $value['exteral_id_type'] ?> /
				                			<b>ID: </b><?= $value['exteral_id_value'] ?>
				                		</li>
				                	<?php endforeach; ?>
				                </ul>
			                <?php } ?>
			            </div>

			            <div class="callout">
			                <h4>Education</h4>
			                <?php if (empty($summary['education'])) {
			                	echo '-';
			                } else { ?>
			                	<ul>
					                <?php foreach ($summary['education'] as $key => $value) : ?>
				                		<li>
				                			<b>Department: </b><?= $value['dept_name'] ?> /
				                			<b>Organzation: </b><?= $value['org_name'] ?>
				                		</li>
				                	<?php endforeach; ?>
				                </ul>
			                <?php } ?>
			            </div>

			            <div class="callout">
			                <h4>Employment</h4>
			                <?php if (empty($summary['employment'])) {
			                	echo '-';
			                } else { ?>
			                	<ul>
					                <?php foreach ($summary['employment'] as $key => $value) : ?>
				                		<li>
				                			<b>Department: </b><?= $value['dept_name'] ?> /
				                			<b>Organzation: </b><?= $value['org_name'] ?>
				                		</li>
				                	<?php endforeach; ?>
				                </ul>
			                <?php } ?>			                
			            </div>

			            <div class="callout">
			                <h4>Email</h4>
		                	<?php if (!empty($summary['emails'])) : ?>
		                		<ul>
			                		<?php foreach ($summary['emails'] as $key => $value) : ?>
				                		<li>
				                			<a 
				                				style="color: #34a1eb"
				                				href="<?= base_url('mail/'.str_replace('=','',base64_encode($value->email))) ?>">
				                				<?= $value->email ?>
				                			</a>
				                		</li>
				                	<?php endforeach; ?>
		                		</ul>
		                	<?php else : ?>
		                		-
		                	<?php endif; ?>
			            </div>

					</div>
					<div class="tab-pane" id="works">
						<?php if (empty($summary['works'])) : ?>
							<div class="callout"><h4>No works</h4></div>
						<?php else : ?>
							<?php foreach ($summary['works'] as $key => $value) : ?>
								<div class="callout">
					                <h4><?= $value['title'] ?></h4>
					                <ul>
				                		<li><b>External ID(s):</b></li>
					                	<ul>
					                		<?php foreach ($value['ids'] as $_key => $val) : ?>
					                			<li>
					                				<b><?= $val['external_id_type'] ?></b> ( <?= $val['external_id_value'] ?> )
					                			</li>
					                		<?php endforeach; ?>
					                	</ul>
					                	<li><b>URL:</b> <a href="<?= $value['url'] ?>" style="color: #34a1eb"><?= $value['url'] ?></a></li>
					                	<li><b>Type:</b> <?= $value['type'] ?></li>
					                	<li><b>Publication Date:</b> <?= implode('-', $value['pub_date']) ?></li>
					                </ul>
					            </div>
					        <?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>