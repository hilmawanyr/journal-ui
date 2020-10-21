<?php

	header('Content-type: application/xml');
	header('Content-Disposition: attachment; filename:"export.xml"');
	header('Content-Transfer-Encoding: binary');

	$dom = new DOMDocument();
	$dom->encoding = 'utf-8';
	$dom->xmlVersion = '1.0';
	$dom->formatOutput = true;

	$root = $dom->createElement('journals');

	$submain = $dom->createElement('jurnal');
	$root->appendChild($submain);

	if (!empty($data['doi'])) {	
		$doinode = $dom->createElement('doi', $data['doi']);
		$submain->appendChild($doinode);
	}

	if (!empty($data['title'])) {
		$titlenode = $dom->createElement('title', $data['title']);
		$submain->appendChild($titlenode);
	}

	if (!empty($data['author'])) {
		foreach ($data['author'] as $key => $val) {
			$authors[] = $val['name'];
		}
		$authornode = $dom->createElement('author', implode(',', $authors));
		$submain->appendChild($authornode);
	}

	if (!empty($data['abstract'])) {
		$abstractnode = $dom->createElement('abstract', $data['abstract']);
		$submain->appendChild($abstractnode);
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
				$submain->appendChild($parent_url_node);				
			} else {
				$url_node = $dom->createElement('urlCrossref', implode('', $data['url']));
				$submain->appendChild($url_node);
			}

		// response from Crossref
		} else {
			$url_node = $dom->createElement('urlCrossref', $data['url']);
			$submain->appendChild($url_node);	
		}
	}

	if (!empty($data['issn'])) {
		$issn_node = $dom->createElement('issn', $data['issn']);
		$submain->appendChild($issn_node);
	}

	if (!empty($data['type'])) {
		$typenode = $dom->createElement('itemType', $data['type']);
		$termAttr = $dom->createAttribute('type');
		$termAttr->value = 'text';
		$typenode->appendChild($termAttr);
		$submain->appendChild($typenode);
	}

	if (!empty($data['language'])) {
		$langnode = $dom->createElement('language', $data['language']);
		$languageTermAttr = $dom->createAttribute('type');
		$languageTermAttr->value = 'code';
		$langnode->appendChild($languageTermAttr);
		$submain->appendChild($langnode);
	}

	if (!empty($data['publisher']) OR !empty($data['date_issue'])) {
		if (!empty($data['publisher'])) {
			$publishernode = $dom->createElement('publisher', htmlspecialchars($data['publisher']));
			$submain->appendChild($publishernode);
		}

		if (!empty($data['date_issue'])) {
			$dateissue = $dom->createElement('dateIssued',$data['date_issue']);
			$submain->appendChild($dateissue);
		}	
	}

	$dom->appendChild($root);
	
	echo $dom->saveXML();