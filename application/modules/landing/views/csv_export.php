<?php 

// tell the browser it's going to be a csv file
header('Content-Type: application/csv');
// tell the browser we want to save it instead of displaying it
header('Content-Disposition: attachment; filename="export.csv";');

$fp = fopen('php://output', 'w');

foreach ($data as $value) {
	fputcsv($fp, $value);
}

fclose($fp);