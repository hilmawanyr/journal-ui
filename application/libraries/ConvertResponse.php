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
				'title'  => isset($value->title) ? $value->title : '',
				'type'   => implode(', ',$value->pubTypeList->pubType),
				'author' => isset($value->authorString) ? $value->authorString : '',
				'url'    => isset($value->fullTextUrlList) 
								? $this->_resolve_pmc_url($value->fullTextUrlList->fullTextUrl) 
								: '',
				'doi'    => isset($value->doi) ? $value->doi : ''
			];
		}
		return $response;
	}

	protected function _resolve_pmc_url(array $pmcUrl) : array
	{
		foreach ($pmcUrl as $url) {
			$urls[] = $url->url;
		}
		return $urls;
	}

	public function convert_detail(string $host, object $data) : array
	{
		switch ($host) {
			case 'CRF':
				return $this->_cnvrt_dtl_crf($data);
				break;

			case 'PMC':
				return $this->_cnvrt_dtl_pmc($data);
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

	protected function _cnvrt_dtl_pmc(object $data) : array
	{
		$response['title'] = isset($data->{'0'}->title) ? $data->{'0'}->title : '';
		$response['doi'] = isset($data->{'0'}->doi) ? $data->{'0'}->doi : '';
		$response['author'] = isset($data->{'0'}->authorString) ? $data->{'0'}->authorString : '-';
		$response['type'] = isset($data->{'0'}->pubTypeList) ? implode(', ',$data->{'0'}->pubTypeList->pubType) : '';
		$response['issn'] = isset($data->{'0'}->journalInfo) ? $data->{'0'}->journalInfo->journal->issn : '';
		$response['isbn'] = '';
		$response['subject'] = '';
		$response['url'] = isset($data->{'0'}->fullTextUrlList) 
								? $this->_resolve_pmc_url($data->{'0'}->fullTextUrlList->fullTextUrl) 
								: '';
		$response['publisher'] = '';
		$response['issue'] = isset($data->{'0'}->journalInfo) ? $data->{'0'}->journalInfo->issue : '';
		$response['license'] = isset($data->{'0'}->license) ? $data->{'0'}->license : '';
		$response['prefix'] = '';
		$response['volume'] = isset($data->{'0'}->journalInfo) ? $data->{'0'}->journalInfo->volume : '';
		$response['funder'] = '';
		$response['abstract'] = isset($data->{'0'}->abstractText) ? $data->{'0'}->abstractText : '';
		$response['member'] = '';
		$response['publised_online'] = isset($data->{'0'}->firstPublicationDate) ? $data->{'0'}->firstPublicationDate : '';
		$response['reference'] = '';
		return $response;	
	}

}

/* End of file ConvertResponse.php */
/* Location: ./application/libraries/ConvertResponse.php */
