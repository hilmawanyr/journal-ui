<?php

	header('Content-type: application/xml');
	header('Content-Disposition: attachment; filename:"export.xml"');
	header('Content-Transfer-Encoding: binary');

	$dom = new DOMDocument();
	$dom->encoding = 'utf-8';
	$dom->xmlVersion = '1.0';
	$dom->formatOutput = true;

	$root = $dom->createElementNS('','modsCollection');
	$root->setAttributeNS('http://www.loc.gov/mods/v3','xsi:schemaLocation', 'http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-3.xsd');

	foreach ($collection as $key => $value) {

		$mods = $dom->createElement('mods');
		$modsAttr1 = $dom->createAttribute('version');
		$modsAttr1->value = '3.3';
		$modsAttr2 = $dom->createAttribute('ID');
		$modsAttr2->value = $value['doi'];
		$mods->appendChild($modsAttr1);
		$mods->appendChild($modsAttr2);
		$root->appendChild($mods);

		if ($value['doi']) {
			$doi_node = $dom->createElement('doi', $value['doi']);
			$mods->appendChild($doi_node);
		}

		if ($value['title']) {
			$titleInfo = $dom->createElement('titleInfo');
			$title_node = $dom->createElement('title', htmlspecialchars($value['title']));
			$titleInfo->appendChild($title_node);
			$mods->appendChild($titleInfo);
		}

		if ($value['type']) {
			$type_node = $dom->createElement('itemType');
			$type_child = $dom->createElement('itemTypeTerm', $value['type']);
			$termAttr = $dom->createAttribute('type');
			$termAttr->value = 'text';
			$type_child->appendChild($termAttr);
			$type_node->appendChild($type_child);
			$mods->appendChild($type_node);
		}

		if ($value['author']) {

			foreach ($value['author'] as $key => $author) {
				$author_node = $dom->createElement('name');
				$name_type = $dom->createAttribute('type');
				$name_type->value = 'Personal Name';
				$author_node->appendChild($name_type);
				$author_name = $dom->createElement('namePart', $author['name']);
				$author_node->appendChild($author_name);
				$mods->appendChild($author_node);
			}

		}

		if ($value['issn']) {
			$issn_node = $dom->createElement('issn', $value['issn']);
			$mods->appendChild($issn_node);
		}

		if ($value['isbn']) {
			$isbn_node = $dom->createElement('isbn', $value['isbn']);
			$mods->appendChild($isbn_node);
		}

		if ($value['url']) {	
			$url_node = $dom->createElement('urlCrossref', htmlspecialchars($value['url']));
			$mods->appendChild($url_node);
		}

		if (!empty($value['language'])) {
			$lang = $dom->createElement('language');
			$langterm = $dom->createElement('languageTerm',$value['language']);
			$languageTermAttr = $dom->createAttribute('type');
			$languageTermAttr->value = 'code';
			$langterm->appendChild($languageTermAttr);
			$lang->appendChild($langterm);
			$mods->appendChild($lang);	
		}

		if (!empty($value['publisher']) OR !empty($value['date_issue'])) {
			$origin_node = $dom->createElement('originInfo',' ');
			if (!empty($value['publisher'])) {
				$publisher_node = $dom->createElement('publisher', htmlspecialchars($value['publisher']));
				$origin_node->appendChild($publisher_node);
			}

			if (!empty($value['date_issue'])) {
				$date_issue = $dom->createElement('dateIssued',$value['date_issue']);
				$origin_node->appendChild($date_issue);
			}
			$mods->appendChild($origin_node);	
		}

		if ($value['subject']) {
			$subject_node = $dom->createElement('subject');
			$subjectAttr = $dom->createAttribute('authority');
			$subject_node->appendChild($subjectAttr);
			$topic_node = $dom->createElement('topic', $value['subject']);
			$subject_node->appendChild($topic_node);
			$mods->appendChild($subject_node);
		}

		if ($value['issue']) {
			$issue_node = $dom->createElement('issue', $value['issue']);
			$mods->appendChild($issue_node);
		}

		if ($value['license']) {
			$license_node = $dom->createElement('license', $value['license']);
			$mods->appendChild($license_node);
		}

		if ($value['prefix']) {
			$prefix_node = $dom->createElement('prefix', $value['prefix']);
			$mods->appendChild($prefix_node);
		}

		if ($value['volume']) {
			$volume_node = $dom->createElement('volume', $value['volume']);
			$mods->appendChild($volume_node);
		}

		if ($value['abstract']) {
			$abstract_node = $dom->createElement('abstract', htmlspecialchars($value['abstract']));
			$mods->appendChild($abstract_node);
		}

		if ($value['funder']) {
			$funder_node = $dom->createElement('funder', $value['funder']);
			foreach ($value['funder'] as $funder) {
				$doi_funder_node = $dom->createElement('doi', $funder->doi);
				$funder_node->appendChild($doi_funder_node);

				$name_funder_node = $dom->createElement('name', $funder->name);
				$funder_node->appendChild($name_funder_node);

				$assertedby_funder_node = $dom->createElement('doiAssertedBy', $funder->{'doi-asserted-by'});
				$funder_node->appendChild($assertedby_funder_node);
			}
			$mods->appendChild($funder_node);
		}

		if ($value['published_online']) {
			$published_online_node = $dom->createElement('publishedOnline', $value['published_online']);
			$mods->appendChild($published_online_node);
		}

		if ($value['reference']) {
			$reference_list_node = $dom->createElement('referenceList');
			foreach ($value['reference'] as $key => $val) {
				$reference_node = $dom->createElement('reference', htmlspecialchars($val->unstructured));
				$reference_list_node->appendChild($reference_node);
			}
			$mods->appendChild($reference_list_node);	
		}
		$root->appendChild($mods);
	}
	
	$dom->appendChild($root);
	echo $dom->saveXML();