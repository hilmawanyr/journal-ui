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
				'author' => isset($value->authorList) 
								? $this->_handle_author_pmc($value->authorList->author) 
								: '-',
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

	/*============================================================
	=            Block Section for Handle CSV Service            =
	============================================================*/
	
	public function convert_csv(string $host, object $data) : array
	{
		switch ($host) {
			case 'CRF':
				return $this->_csv_crf_export($data);
				break;

			case 'PMC':
				return $this->_csv_pmc_export($data);
				break;
			
			default:
				return [];
				break;
		}
	}

	protected function _csv_crf_export(object $data) : array
	{
		$response['title'] = $this->_resolve_title_crf($data->title);
		$response['doi'] = isset($data->DOI) ? $data->DOI : '';
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
		$response['publised_online'] = isset($data->{'published-online'}) 
											? implode('-', (array)$data->{'published-online'}->{'date-parts'}[0]) 
											: '';
		$response['reference'] = isset($data->reference) ? $data->reference : '';
		return $response;
	}

	protected function _csv_pmc_export(object $data) : array
	{
		$response['title']  = isset($data->{'0'}->title) ? $data->{'0'}->title : '';
		$response['doi']    = isset($data->{'0'}->doi) ? $data->{'0'}->doi : '#';
		$response['author'] = isset($data->{'0'}->authorList) 
								? $this->_handle_author_pmc($data->{'0'}->authorList->author, 1) 
								: '-';
		$response['type']   = isset($data->{'0'}->pubTypeList) ? implode(', ',$data->{'0'}->pubTypeList->pubType) : '';
		$response['issn']   = isset($data->{'0'}->journalInfo) ? $data->{'0'}->journalInfo->journal->issn : '';
		$response['isbn']   = '';

		$response['subject'] = '';
		$response['url']     = isset($data->{'0'}->fullTextUrlList) 
								? implode(', ', $this->_resolve_pmc_url($data->{'0'}->fullTextUrlList->fullTextUrl)) 
								: '';

		$response['publisher'] = isset($data->{'0'}->bookOrReportDetails) 
									? $data->{'0'}->bookOrReportDetails->publisher
									: '';

		$response['issue']     = isset($data->{'0'}->journalInfo) 
									? (isset($data->{'0'}->journalInfo->issue) ? $data->{'0'}->journalInfo->issue : '') 
									: '';

		$response['license'] = isset($data->{'0'}->license) ? $data->{'0'}->license : '';
		$response['prefix']  = '';
		$response['volume']  = isset($data->{'0'}->journalInfo) 
								? (isset($data->{'0'}->journalInfo->volume) ? $data->{'0'}->journalInfo->volume : '')
								: '';

		$response['funder']          = '';
		$response['abstract']        = isset($data->{'0'}->abstractText) ? $data->{'0'}->abstractText : '';
		$response['member']          = '';
		$response['publised_online'] = isset($data->{'0'}->firstPublicationDate) ? $data->{'0'}->firstPublicationDate : '';
		$response['reference']       = '';
		return $response;	
	}

	/*=====  End of Section comment block  ======*/

	/*===========================================================================
	=            Section for Handle Detail that Used on Modal Detail            =
	===========================================================================*/	

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
				return [];
				break;
		}
	}

	private function _cnvrt_dtl_crf(object $data) : array
	{
		$response['title'] = $this->_resolve_title_crf($data->title);
		$response['doi'] = isset($data->DOI) ? $data->DOI : '';
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
		$response['title']  = isset($data->{'0'}->title) ? $data->{'0'}->title : '';
		$response['doi']    = isset($data->{'0'}->doi) ? $data->{'0'}->doi : '#';
		$response['author'] = isset($data->{'0'}->authorList) 
								? $this->_handle_author_pmc($data->{'0'}->authorList->author) 
								: '-';
		$response['type']   = isset($data->{'0'}->pubTypeList) ? implode(', ',$data->{'0'}->pubTypeList->pubType) : '';
		$response['issn']   = isset($data->{'0'}->journalInfo) ? $data->{'0'}->journalInfo->journal->issn : '';
		$response['isbn']   = '';

		$response['subject'] = '';
		$response['url']     = isset($data->{'0'}->fullTextUrlList) 
								? $this->_resolve_pmc_url($data->{'0'}->fullTextUrlList->fullTextUrl) 
								: '';

		$response['publisher'] = isset($data->{'0'}->bookOrReportDetails) 
									? $data->{'0'}->bookOrReportDetails->publisher
									: '';

		$response['issue']     = isset($data->{'0'}->journalInfo) 
									? (isset($data->{'0'}->journalInfo->issue) ? $data->{'0'}->journalInfo->issue : '') 
									: '';

		$response['license'] = isset($data->{'0'}->license) ? $data->{'0'}->license : '';
		$response['prefix']  = '';
		$response['volume']  = isset($data->{'0'}->journalInfo) 
								? (isset($data->{'0'}->journalInfo->volume) ? $data->{'0'}->journalInfo->volume : '')
								: '';

		$response['funder']          = '';
		$response['abstract']        = isset($data->{'0'}->abstractText) ? $data->{'0'}->abstractText : '';
		$response['member']          = '';
		$response['publised_online'] = isset($data->{'0'}->firstPublicationDate) ? $data->{'0'}->firstPublicationDate : '';
		$response['reference']       = '';
		return $response;	
	}

	/*=====  End of Section for Handle Detail that Used on Modal Detail  ======*/

	/*===========================================================================
	=            Section for Handle Servive that Used for XML export            =
	===========================================================================*/
	
	public function convert_xml(string $host, object $data) : array
	{
		switch ($host) {
			case 'CRF':
				return $this->_crf_xml_convert($data);
				break;

			case 'PMC':
				return $this->_pmc_xml_convert($data);
				break;
			
			default:
				return [];
				break;
		}
	}

	protected function _crf_xml_convert(object $data) : array
	{
		$response['title'] = $this->_resolve_title_crf($data->title);
		$response['doi'] = isset($data->DOI) ? $data->DOI : '';
		$response['author'] = isset($data->author) ? $this->_crf_convert_xml_author($data->author) : '-';
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

	protected function _crf_convert_xml_author(array $data) : array
	{
		foreach ($data as $key => $val) {
			$family = isset($val->family) ? $val->family : '';
			$given = isset($val->given) ? $val->given : '';
			$orcid = isset($val->ORCID) ? '(ORCID: '.$val->ORCID.')' : '';
			$author[] = ['name' => $given.' '.$family.' '.$orcid];
		}
		return $author;
	}

	protected function _pmc_xml_convert(object $data) : array
	{
		$response['title']  = isset($data->{'0'}->title) ? $data->{'0'}->title : '';
		$response['doi']    = isset($data->{'0'}->doi) ? $data->{'0'}->doi : '#';
		$response['author'] = isset($data->{'0'}->authorList) 
								? $this->_handle_author_pmc_xml($data->{'0'}->authorList->author) 
								: '';
		$response['type']   = isset($data->{'0'}->pubTypeList) ? implode(', ',$data->{'0'}->pubTypeList->pubType) : '';
		$response['issn']   = isset($data->{'0'}->journalInfo) ? $data->{'0'}->journalInfo->journal->issn : '';
		$response['isbn']   = '';

		$response['subject'] = '';
		$response['url']     = isset($data->{'0'}->fullTextUrlList) 
								? implode(', ', $this->_resolve_pmc_url($data->{'0'}->fullTextUrlList->fullTextUrl)) 
								: '';

		$response['publisher'] = isset($data->{'0'}->bookOrReportDetails) 
									? $data->{'0'}->bookOrReportDetails->publisher
									: '';
		$response['issue']     = isset($data->{'0'}->journalInfo) 
									? (isset($data->{'0'}->journalInfo->issue) ? $data->{'0'}->journalInfo->issue : '') 
									: '';

		$response['license'] = isset($data->{'0'}->license) ? $data->{'0'}->license : '';
		$response['prefix']  = '';
		$response['volume']  = isset($data->{'0'}->journalInfo) 
								? (isset($data->{'0'}->journalInfo->volume) ? $data->{'0'}->journalInfo->volume : '')
								: '';

		$response['funder']          = '';
		$response['abstract']        = isset($data->{'0'}->abstractText) ? $data->{'0'}->abstractText : '';
		$response['member']          = '';
		$response['publised_online'] = isset($data->{'0'}->firstPublicationDate) ? $data->{'0'}->firstPublicationDate : '';
		$response['reference']       = '';
		return $response;
	}

	protected function _handle_author_pmc_xml(array $authors) : array
	{
		$data = [];
		foreach ($authors as $key => $value) {
			$_author = $value->fullName;
			$getEmail = isset($value->authorAffiliationsList) 
						? $this->_handle_get_email_pmc($value->authorAffiliationsList->authorAffiliation)
						: '';
				
			if (!empty($getEmail)) {
				foreach ($getEmail as $email) {
					$_email = substr($email, -1) == '.' ? substr($email, 0 ,-1) : $email;
					$name = [ 'name' => $_author.'['.$_email.']' ];
					array_push($data, $name);
				}
			} else {
				$name = [ 'name' => $_author ];
				array_push($data, $name);
			}
		}
		return $data;
	}
	
	/*=====  End of Section for Handle Servive that Used for XML export  ======*/
	

	protected function _handle_get_email_pmc(array $data) : array
	{
		$arr = [];
		foreach ($data as $mail) {
			$explodeString = explode(' ', $mail);
			foreach ($explodeString as $exp) {
				if (strpos($exp, '@')) {
					array_push($arr, $exp);
				}
			}
		}
		return $arr;	
	}

	protected function _handle_author_pmc(array $authors, int $is_for_csv=0) : string
	{
		$arr = [];
		foreach ($authors as $author => $value) {
			$_author = $value->fullName;
			$email = isset($value->authorAffiliationsList) 
						? $this->_handle_get_email_pmc($value->authorAffiliationsList->authorAffiliation)
						: '';
			if (!empty($email)) {
				foreach ($email as $_email) {
					$adjustEmail = substr($_email, -1) == '.' ? substr($_email, 0 ,-1) : $_email;
					if ($is_for_csv == 1) {
						$response = $_author.'['.$adjustEmail.']';
					} else {
						$response = '<a href="#inviteModal" data-toggle="modal" onclick="invite(\''.$adjustEmail.'\')">';
						$response .= '<i class="fa fa-envelope"></i> '.$_author.'</a>';
					}

					array_push($arr, $response);
				}
			} else {
				array_push($arr, $_author);
			}
		}
		$convertResponseToString = implode(', ', $arr);
		return $convertResponseToString;
	}

}

/* End of file ConvertResponse.php */
/* Location: ./application/libraries/ConvertResponse.php */
