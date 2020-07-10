<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConvertResponse
{
	protected $ci;

	public function __construct()
	{
        $this->ci =& get_instance();
	}

	public function convert(string $host, array $data) : array
	{
		switch ($host) {
			case 'CRF':
				return $this->_cnvrt_crf($data);
				break;

			case 'PMC':
				return $this->_cnvrt_pmc($data);
				break;
			
			default:
				return NULL;
				break;
		}		
	}

	private function _cnvrt_crf(array $data) : array
	{
		foreach ($data as $value) {
			$title   = isset($value->title) ? $this->_resolve_title_crf($value->title) : '';
			$authors = isset($value->author) ? $this->_resolve_author_crf($value->author) : '';

			$response[] = [
				'title'  => $title,
				'type'   => $value->type,
				'author' => $authors,
				'url'    => $value->URL,
				'doi'    => $value->DOI
			];
			
		}
		return $response;
	}

	private function _resolve_title_crf(array $titles) : string
	{
		$countTitle = count($titles);
		if ($countTitle > 1) {
			$title = '';
			foreach ($titles as $titl) {
				$title = $title.$titl.', ';
			}
			$fixtitle = substr($title, 0, -2);
		} else {
			$fixtitle = $titles[0];
		}
		return $fixtitle;
	}

	private function _resolve_author_crf(array $authors) : string
	{
		$_authors = '';
		foreach ($authors as $key => $author) {
			$orcid  = isset($author->ORCID) ? ' (ORCID: '.$author->ORCID.')' : '';
			$given  = isset($author->given) ? $author->given : '';
			$family = isset($author->given) ? $author->family : '';
			$_authors = $_authors.$given.' '.$family.$orcid.', ';
		}
		return substr($_authors, 0, -2);
	}

	private function _cnvrt_pmc(array $data) : array
	{
		foreach ($data as $value) {
			$response[] = [
				'title'  => $value->title,
				'type'   => $value->pubType,
				'author' => $value->authorString,
				'url'    => '',
				'doi'    => $value->doi
			];
		}
		return $response;
	}

	public function convert_detail(string $host, object $data) : array
	{
		switch ($host) {
			case 'CRF':
				return $this->_cnvrt_dtl_crf($data);
				break;
			
			default:
				return NULL;
				break;
		}
	}

	private function _cnvrt_dtl_crf(object $data) : array
	{
		$response['title'] = $this->_resolve_title_crf($data->title);
		$response['doi'] = $data->DOI;
		$response['author'] = isset($data->author) ? $this->_resolve_author_crf($data->author) : '-';
		$response['type'] = isset($data->type) ? $data->type : '';
		$response['issn'] = isset($data->ISSN) ? implode(', ', $data->ISSN) : '';
		$response['isbn'] = isset($data->ISBN) ? implode(', ', $data->ISBN) : '';
		$response['subject'] = isset($data->subject) ? implode(', ',$data->subject) : '';
		$response['url'] = isset($data->URL) ? $data->URL : '';
		$response['publisher'] = isset($data->publisher) ? $data->publisher : '';
		$response['issue'] = isset($data->issue) ? $data->issue : '';
		$response['license'] = isset($data->license) ? $data->license : '';
		$response['prefix'] = isset($data->prefix) ? $data->prefix : '';
		$response['volume'] = isset($data->volume) ? $data->volume : '';
		$response['funder'] = isset($data->funder) ? $data->funder : '';
		$response['abstract'] = isset($data->abstract) ? $data->abstract : '';
		$response['member'] = isset($data->member) ? $data->member : '';
		$response['publised_online'] = isset($data->{'published-online'}) ? $data->{'published-online'} : '';
		$response['reference'] = isset($data->reference) ? $data->reference : '';
		return $response;
	}

}

/* End of file ConvertResponse.php */
/* Location: ./application/libraries/ConvertResponse.php */
