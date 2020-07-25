<?php

	header('Content-type: application/xml');
	header('Content-Disposition: attachment; filename:"export.xml"');
	header('Content-Transfer-Encoding: binary');

	$dom = new DOMDocument();
	$dom->encoding = 'utf-8';
	$dom->xmlVersion = '1.0';
	$dom->formatOutput = true;
	$root = $dom->createElement('ArticleList');

	foreach ($collection as $value) {

		$article_node = $dom->createElement('Article');

		if ($value['doi']) {
			$doi_node = $dom->createElement('DOI', $value['doi']);
			$article_node->appendChild($doi_node);
		}

		if ($value['title']) {
			$title_node = $dom->createElement('Title', htmlspecialchars($value['title']));
			$article_node->appendChild($title_node);
		}

		if ($value['type']) {
			$type_node = $dom->createElement('Type', $value['type']);
			$article_node->appendChild($type_node);
		}

		if ($value['author']) {
			$author_node = $dom->createElement('AuthorList');

			foreach ($value['author'] as $key => $author) {
				$author_name = $dom->createElement('Author', $author['name']);
				$author_node->appendChild($author_name);
			}

			$article_node->appendChild($author_node);
		}

		if ($value['issn']) {
			$issn_node = $dom->createElement('ISSN', $value['issn']);
			$article_node->appendChild($issn_node);
		}

		if ($value['isbn']) {
			$isbn_node = $dom->createElement('ISBN', $value['isbn']);
			$article_node->appendChild($isbn_node);
		}

		if ($value['url']) {
			$url_node = $dom->createElement('URL', $value['url']);
			$article_node->appendChild($url_node);	
		}

		if ($value['publisher']) {
			$publisher_node = $dom->createElement('Publisher', htmlspecialchars($value['publisher']	));
			$article_node->appendChild($publisher_node);
		}

		if ($value['subject']) {
			$subject_node = $dom->createElement('Subject', $value['subject']);
			$article_node->appendChild($subject_node);
		}

		if ($value['issue']) {
			$issue_node = $dom->createElement('Issue', $value['issue']);
			$article_node->appendChild($issue_node);
		}

		if ($value['license']) {
			$license_list_node = $dom->createElement('LicenseList');
			foreach ($value['license'] as $values) {
				$license_node = $dom->createElement('License');

				$url_license_node = $dom->createElement('URL', $values->URL);
				$license_node->appendChild($url_license_node);

				$datetime_license_node = $dom->createElement('DateTime', $values->start->{'date-time'});
				$license_node->appendChild($datetime_license_node);

				$timestamp_license_node = $dom->createElement('TimeStamp', $values->start->timestamp);
				$license_node->appendChild($timestamp_license_node);
				
				$license_list_node->appendChild($license_node);
			}
			$article_node->appendChild($license_list_node);
		}

		if ($value['prefix']) {
			$prefix_node = $dom->createElement('Prefix', $value['prefix']);
			$article_node->appendChild($prefix_node);
		}

		if ($value['volume']) {
			$volume_node = $dom->createElement('Volume', $value['volume']);
			$article_node->appendChild($volume_node);
		}

		if ($value['abstract']) {
			$abstract_node = $dom->createElement('Abstract', htmlspecialchars($value['abstract']));
			$article_node->appendChild($abstract_node);
		}

		if ($value['funder']) {
			$funder_node = $dom->createElement('Funder', $value['funder']);
			foreach ($value['funder'] as $funder) {
				$doi_funder_node = $dom->createElement('DOI', $funder->doi);
				$funder_node->appendChild($doi_funder_node);

				$name_funder_node = $dom->createElement('Name', $funder->name);
				$funder_node->appendChild($name_funder_node);

				$assertedby_funder_node = $dom->createElement('DOIAssertedBy', $funder->{'doi-asserted-by'});
				$funder_node->appendChild($assertedby_funder_node);
			}
			$article_node->appendChild($funder_node);
		}

		if ($value['published_online']) {
			$published_online_node = $dom->createElement('PublishedOnline', $value['published_online']);
			$article_node->appendChild($published_online_node);
		}

		if ($value['reference']) {
			$reference_list_node = $dom->createElement('ReferenceList');
			foreach ($value['reference'] as $key => $val) {
				if (isset($val->unstructured)) {
					$reference_node = $dom->createElement('Reference', htmlspecialchars($val->unstructured));
					$reference_list_node->appendChild($reference_node);
				}
			}
			$article_node->appendChild($reference_list_node);	
		}

		$root->appendChild($article_node);
	}

	$dom->appendChild($root);
	echo $dom->saveXML();