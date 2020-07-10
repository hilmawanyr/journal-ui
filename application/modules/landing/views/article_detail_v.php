<h4><b>Title:</b> <?= $data['title'] ?></h4>
<br>
<table class="table table-hover">
	<tr>
		<th>DOI</th>
		<td><?= $data['doi'] ?></td>
	</tr>
	<tr>
		<th>Author</th>
		<td><?= $data['author'] ?></td>
	</tr>
	<tr>
		<th>Type</th>
		<td><?= ucwords(str_replace('-', ' ', $data['type'])) ?></td>
	</tr>

	<?= !empty($data['issn']) 
			? "<tr><th>ISSN</th><td>".ucwords(str_replace('-', ' ', $data['issn']))."</td>" 
			: '' ?>

	<?= !empty($data['isbn']) 
			? "<tr><th>ISBN</th><td>".ucwords(str_replace('-', ' ', $data['isbn']))."</td>" 
			: '' ?>
	<tr>
		<th>URL</th>
		<td><a href="<?= $data['url'] ?>"><i class="fa fa-external-link"></i> <?= $data['url'] ?></a></td>
	</tr>

	<?= !empty($data['publisher'])
			? '<tr><th>Publisher</th><td>'.$data['publisher'].'</td></tr>'
			: '' ?>

	<?= !empty($data['subject'])
			? '<tr><th>Subject</th><td>'.$data['subject'].'</td></tr>'
			: '' ?>

	<?= !empty($data['issue'])
			? '<tr><th>Issue</th><td>'.$data['issue'].'</td></tr>'
			: '' ?>

	<?php if (!empty($data['license'])) {
		$_license = '<tr><th>License</th><td>';
		foreach ($data['license'] as $license) {
			$_license .= '<ul><li>URL: <a href="'.$license->URL.'"><i class="fa fa-external-link"></i> '.$license->URL.'</a></li>';
			$_license .= '<li>Date time: '.str_replace(['T','Z'], ' ', $license->start->{'date-time'}).'</li>';
			$_license .= '<li>Timestamp: '.$license->start->timestamp.'</li></ul>';
		}
		$_license .= '</td></tr>';
		echo $_license;
	} ?>

	<?= !empty($data['prefix'])
			? '<tr><th>Prefix</th><td>'.$data['prefix'].'</td></tr>'
			: '' ?>

	<?= !empty($data['volume'])
			? '<tr><th>Volume</th><td>'.$data['volume'].'</td></tr>'
			: '' ?>

	<?= !empty($data['abstract'])
			? '<tr><th>Abstract</th><td>'.$data['abstract'].'</td></tr>'
			: '' ?>

	<?php if (!empty($data['funder'])) {
		$_funders = '<tr><th>Funder</th><td>';
		foreach ($data['funder'] as $funders) {
			$_funders .= isset($funders->DOI) ? '<ul><li>DOI: '.$funders->DOI.'</li>' : '';
			$_funders .= isset($funders->name) ? '<li>Name: '.$funders->name.'</li>' : '';
			$_funders .= isset($funders->{'doi-asserted-by'}) ? '<li>DOI asserted by: '.$funders->{'doi-asserted-by'}.'</li>' : '';
			$_funders .= isset($funders->award) ? '<li>Award: '.implode(', ', $funders->award).'</li></ul>' : '';
		}
		$_funders .= '</td></tr>';
		echo $_funders;
	} ?>

	<?= !empty($data['published_online'])
			? '<tr><th>Published online date</th><td>'.implode('-',$data['published_online']->{'date-parts'}[0]).'</td></tr>'
			: '' ?>
	
</table>