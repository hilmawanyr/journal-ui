<?php

	header('Content-type: application/xml');
	header('Content-Disposition: attachment; filename="xml_export.xml"');
	header('Content-Transfer-Encoding: binary');

	$dom = new DOMDocument();
	$dom->encoding = 'utf-8';
	$dom->xmlVersion = '1.0';
	$dom->formatOutput = true;

	$root = $dom->createElementNS('','modsCollection');
	$root->setAttributeNS('http://www.loc.gov/mods/v3','xsi:schemaLocation', 'http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-3.xsd');

	$mods = $dom->createElement('mods');
	$modsAttr1 = $dom->createAttribute('version');
	$modsAttr1->value = '3.3';
	$modsAttr2 = $dom->createAttribute('ID');
	$modsAttr2->value = $data['doi'];
	$mods->appendChild($modsAttr1);
	$mods->appendChild($modsAttr2);

	$root->appendChild($mods);

	$doi_node = $dom->createElement('doi', $data['doi']);
	$mods->appendChild($doi_node);

	if (!empty($data['title'])) {
		$titleInfo = $dom->createElement('titleInfo');
		$title_node = $dom->createElement('title', $data['title']);
		$titleInfo->appendChild($title_node);
		$mods->appendChild($titleInfo);
	}

	$type_node = $dom->createElement('itemType');
	$type_child = $dom->createElement('itemTypeTerm', $data['type']);
	$termAttr = $dom->createAttribute('type');
	$termAttr->value = 'text';
	$type_child->appendChild($termAttr);
	$type_node->appendChild($type_child);
	$mods->appendChild($type_node);

	if (!empty($data['author'])) {

		foreach ($data['author'] as $key => $author) {
			$author_node = $dom->createElement('name');
			$name_type = $dom->createAttribute('type');
			$name_type->value = 'Personal Name';
			$author_node->appendChild($name_type);
			$author_name = $dom->createElement('namePart', $author['name']);
			$author_node->appendChild($author_name);
			$mods->appendChild($author_node);
		}

	}

	if (!empty($data['issn'])) {
		$issn_node = $dom->createElement('issn', $data['issn']);
		$mods->appendChild($issn_node);
	}

	if (!empty($data['isbn'])) {
		$isbn_node = $dom->createElement('isbn', $data['isbn']);
		$mods->appendChild($isbn_node);
	}

	if (!empty($data['url'])) {
		// response from EuropePMC
		if (is_array($data['url'])) {
			if (count($data['url']) > 1) {
				$parent_url_node = $dom->createElement('urlCrossref');
				foreach ($data['url'] as $urls) {
					$child_url_node = $dom->createElement('url', $urls);
					$parent_url_node->appendChild($child_url_node);
				}
				$mods->appendChild($parent_url_node);				
			} else {
				$url_node = $dom->createElement('urlCrossref', implode('', $data['url']));
				$mods->appendChild($url_node);
			}

		// response from Crossref
		} else {
			$url_node = $dom->createElement('urlCrossref', $data['url']);
			$mods->appendChild($url_node);	
		}
	}

	$lang = $dom->createElement('language');
	$langterm = $dom->createElement('languageTerm',$data['language']);
	$languageTermAttr = $dom->createAttribute('type');
	$languageTermAttr->value = 'code';
	$langterm->appendChild($languageTermAttr);
	$lang->appendChild($langterm);
	$mods->appendChild($lang);

	if (!empty($data['publisher']) OR !empty($data['date_issue'])) {
		$origin_node = $dom->createElement('originInfo',' ');
		if (!empty($data['publisher'])) {
			$publisher_node = $dom->createElement('publisher', htmlspecialchars($data['publisher']));
			$origin_node->appendChild($publisher_node);
		}

		if (!empty($data['date_issue'])) {
			$date_issue = $dom->createElement('dateIssued',$data['date_issue']);
			$origin_node->appendChild($date_issue);
		}
		$mods->appendChild($origin_node);	
	}

	if (!empty($data['subject'])) {
		$subject_node = $dom->createElement('subject');
		$subjectAttr = $dom->createAttribute('authority');
		$subject_node->appendChild($subjectAttr);
		$topic_node = $dom->createElement('topic', $data['subject']);
		$subject_node->appendChild($topic_node);
		$mods->appendChild($subject_node);
	}

	if (!empty($data['issue'])) {
		$issue_node = $dom->createElement('issue', $data['issue']);
		$mods->appendChild($issue_node);
	}

	if (!empty($data['license'])) {
		// response from Crossref
		if (is_array($data['license'])) {
			$license_node = $dom->createElement('license');
			foreach ($data['license'] as $value) {
				$url_license_node = $dom->createElement('URL', $value->URL);
				$license_node->appendChild($url_license_node);

				$datetime_license_node = $dom->createElement('DateTime', $value->start->{'date-time'});
				$license_node->appendChild($datetime_license_node);

				$timestamp_license_node = $dom->createElement('TimeStamp', $value->start->timestamp);
				$license_node->appendChild($timestamp_license_node);
			}
			$mods->appendChild($license_node);

		// reponse from EuropePMC
		} else {
			$license_node = $dom->createElement('license', $data['license']);
			$mods->appendChild($license_node);
		}
	}

	if (!empty($data['prefix'])) {
		$prefix_node = $dom->createElement('prefix', $data['prefix']);
		$mods->appendChild($prefix_node);
	}

	if (!empty($data['volume'])) {
		$volume_node = $dom->createElement('volume', $data['volume']);
		$mods->appendChild($volume_node);
	}

	if (!empty($data['abstract'])) {
		$abstract_node = $dom->createElement('note', htmlspecialchars($data['abstract']));
		$mods->appendChild($abstract_node);
	}

	if (!empty($data['funder'])) {
		$funder_node = $dom->createElement('funder', $data['funder']);
		foreach ($data['funder'] as $funder) {
			$doi_funder_node = $dom->createElement('doi', $funder->doi);
			$funder_node->appendChild($doi_funder_node);

			$name_funder_node = $dom->createElement('name', $funder->name);
			$funder_node->appendChild($name_funder_node);

			$assertedby_funder_node = $dom->createElement('DOIAssertedBy', $funder->{'doi-asserted-by'});
			$funder_node->appendChild($assertedby_funder_node);
		}
		$mods->appendChild($funder_node);
	}

	if (!empty($data['published_online'])) {
		$published_online_node = $dom->createElement('publishedOnline', $data['published_online']);
		$mods->appendChild($published_online_node);
	}

	if (!empty($data['reference'])) {
		$reference_list_node = $dom->createElement('referenceList');
		foreach ($data['reference'] as $key => $val) {
			$reference_node = $dom->createElement('reference', htmlspecialchars($val->unstructured));
			$reference_list_node->appendChild($reference_node);
		}
		$mods->appendChild($reference_list_node);	
	}

	$dom->appendChild($root);
	
	echo $dom->saveXML();