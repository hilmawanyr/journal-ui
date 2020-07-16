<?php

	header('Content-type: application/xml');
	header('Content-Disposition: attachment; filename:"export.xml"');
	header('Content-Transfer-Encoding: binary');

	$dom = new DOMDocument();

		$dom->encoding = 'utf-8';

		$dom->xmlVersion = '1.0';

		$dom->formatOutput = true;

		$root = $dom->createElement('Data');

		$doi_node = $dom->createElement('DOI', $data['doi']);
		$root->appendChild($doi_node);

		$title_node = $dom->createElement('Title', $data['title']);
		$root->appendChild($title_node);

		$type_node = $dom->createElement('Type', $data['type']);
		$root->appendChild($type_node);

		if (!empty($data['author'])) {
			$author_node = $dom->createElement('AuthorList');

			foreach ($data['author'] as $key => $author) {
				$author_name = $dom->createElement('Author', $author['name']);
				$author_node->appendChild($author_name);
			}

			$root->appendChild($author_node);
		}

		if (!empty($data['issn'])) {
			$issn_node = $dom->createElement('ISSN', $data['issn']);
			$root->appendChild($issn_node);
		}

		if (!empty($data['isbn'])) {
			$isbn_node = $dom->createElement('ISBN', $data['isbn']);
			$root->appendChild($isbn_node);
		}

		if (!empty($data['url'])) {
			// response from EuropePMC
			if (is_array($data['url'])) {
				if (count($data['url']) > 1) {
					$parent_url_node = $dom->createElement('URL');
					foreach ($data['url'] as $urls) {
						$child_url_node = $dom->createElement('url', $urls);
						$parent_url_node->appendChild($child_url_node);
					}
					$root->appendChild($parent_url_node);				
				} else {
					$url_node = $dom->createElement('URL', implode('', $data['url']));
					$root->appendChild($url_node);
				}

			// response from Crossref
			} else {
				$url_node = $dom->createElement('URL', $data['url']);
				$root->appendChild($url_node);	
			}
		}

		if (!empty($data['publisher'])) {
			$publisher_node = $dom->createElement('Publisher', htmlspecialchars($data['publisher']	));
			$root->appendChild($publisher_node);
		}

		if (!empty($data['subject'])) {
			$subject_node = $dom->createElement('Subject', $data['subject']);
			$root->appendChild($subject_node);
		}

		if (!empty($data['issue'])) {
			$issue_node = $dom->createElement('Issue', $data['issue']);
			$root->appendChild($issue_node);
		}

		if (!empty($data['license'])) {
			// response from Crossref
			if (is_array($data['license'])) {
				$license_node = $dom->createElement('License');
				foreach ($data['license'] as $value) {
					$url_license_node = $dom->createElement('URL', $value->URL);
					$license_node->appendChild($url_license_node);

					$datetime_license_node = $dom->createElement('DateTime', $value->start->{'date-time'});
					$license_node->appendChild($datetime_license_node);

					$timestamp_license_node = $dom->createElement('TimeStamp', $value->start->timestamp);
					$license_node->appendChild($timestamp_license_node);
				}
				$root->appendChild($license_node);

			// reponse from EuropePMC
			} else {
				$license_node = $dom->createElement('License', $data['license']);
				$root->appendChild($license_node);
			}
		}

		if (!empty($data['prefix'])) {
			$prefix_node = $dom->createElement('Prefix', $data['prefix']);
			$root->appendChild($prefix_node);
		}

		if (!empty($data['volume'])) {
			$volume_node = $dom->createElement('Volume', $data['volume']);
			$root->appendChild($volume_node);
		}

		if (!empty($data['abstract'])) {
			$abstract_node = $dom->createElement('Abstract', htmlspecialchars($data['abstract']));
			$root->appendChild($abstract_node);
		}

		if (!empty($data['funder'])) {
			$funder_node = $dom->createElement('Funder', $data['funder']);
			foreach ($data['funder'] as $funder) {
				$doi_funder_node = $dom->createElement('DOI', $funder->doi);
				$funder_node->appendChild($doi_funder_node);

				$name_funder_node = $dom->createElement('Name', $funder->name);
				$funder_node->appendChild($name_funder_node);

				$assertedby_funder_node = $dom->createElement('DOIAssertedBy', $funder->{'doi-asserted-by'});
				$funder_node->appendChild($assertedby_funder_node);
			}
			$root->appendChild($funder_node);
		}

		if (!empty($data['published_online'])) {
			$published_online_node = $dom->createElement('PublishedOnline', $data['published_online']);
			$root->appendChild($published_online_node);
		}

		// $dom->createElement('Title', $data['title']);

		// $dom->createElement('Author', $data['author']);

		// $attr_movie_id = new DOMAttr('movie_id', '5467');

		// $movie_node->setAttributeNode($attr_movie_id);

	// $child_node_title = $dom->createElement('Title', 'The Campaign');

	// 	$movie_node->appendChild($child_node_title);

	// 	$child_node_year = $dom->createElement('Year', 2012);

	// 	$movie_node->appendChild($child_node_year);

	// $child_node_genre = $dom->createElement('Genre', 'The Campaign');

	// 	$movie_node->appendChild($child_node_genre);

	// 	$child_node_ratings = $dom->createElement('Ratings', 6.2);

	// 	$movie_node->appendChild($child_node_ratings);

		// $root->appendChild($movie_node);

		$dom->appendChild($root);
	
	echo $dom->saveXML();