<?php

	header('Content-type: application/xml');
	header('Content-Disposition: attachment; filename:"export.xml"');
	header('Content-Transfer-Encoding: binary');

	$dom = new DOMDocument();
	$dom->encoding = 'utf-8';
	$dom->xmlVersion = '1.0';
	$dom->formatOutput = true;
	
	$root = $dom->createElement('journals');

	foreach ($collection as $key => $value) {

		$submain = $dom->createElement('jurnal');
		$root->appendChild($submain);

		$doinode = $dom->createElement('doi', $value['doi']);
		$submain->appendChild($doinode);

		if (!empty($value['title'])) {
			$titlenode = $dom->createElement('title', htmlspecialchars($value['title']));
			$submain->appendChild($titlenode);
		}

		if (!empty($value['author'])) {
			foreach ($value['author'] as $key => $val) {
				$authors[] = $val['name'];
			}
			$authornode = $dom->createElement('author', implode(',', $authors));
			$submain->appendChild($authornode);
		}

		if (!empty($value['abstract'])) {
			$abstractnode = $dom->createElement('abstract', $value['abstract']);
			$submain->appendChild($abstractnode);
		}

		if (!empty($value['url'])) {
			$url_node = $dom->createElement('urlCrossref', htmlspecialchars($value['url']));
			$submain->appendChild($url_node);
		}

		if (!empty($value['issn'])) {
			$issn_node = $dom->createElement('issn', $value['issn']);
			$submain->appendChild($issn_node);
		}

		if (!empty($value['type'])) {
			$typenode = $dom->createElement('itemType', $value['type']);
			$termAttr = $dom->createAttribute('type');
			$termAttr->value = 'text';
			$typenode->appendChild($termAttr);
			$submain->appendChild($typenode);
		}

		if (!empty($value['language'])) {
			$langnode = $dom->createElement('language', $value['language']);
			$languageTermAttr = $dom->createAttribute('type');
			$languageTermAttr->value = 'code';
			$langnode->appendChild($languageTermAttr);
			$submain->appendChild($langnode);
		}

		if (!empty($value['publisher']) OR !empty($value['date_issue'])) {
			if (!empty($value['publisher'])) {
				$publishernode = $dom->createElement('publisher', htmlspecialchars($value['publisher']));
				$submain->appendChild($publishernode);
			}

			if (!empty($value['date_issue'])) {
				$dateissue = $dom->createElement('dateIssued',$value['date_issue']);
				$submain->appendChild($dateissue);
			}	
		}
	}

	$dom->appendChild($root);
	echo $dom->saveXML();