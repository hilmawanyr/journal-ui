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
				'type'   => isset($value->type) ? $value->type : '',
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

	/*===========================================================================
	=            Section for Handle Detail that Used on Modal Detail            =
	===========================================================================*/	

	public function convert_detail(string $host, object $data, string $usedFor='default') : array
	{
		switch ($host) {
			case 'CRF':
				return $this->_cnvrt_dtl_crf($data, $usedFor);
				break;

			case 'PMC':
				return $this->_cnvrt_dtl_pmc($data, $usedFor);
				break;
			
			default:
				return [];
				break;
		}
	}

	private function _cnvrt_dtl_crf(object $data, string $usedFor='default') : array
	{
		$response['title'] = isset($data->title) ? $this->_resolve_title_crf($data->title) : '';
		$response['doi'] = isset($data->DOI) ? $data->DOI : '';

		if ($usedFor == 'default' OR $usedFor == 'csv') {
			$response['author'] = isset($data->author) ? $this->_resolve_author_crf($data->author) : '-';
			
		} elseif ($usedFor == 'xml') {
			$response['author'] = isset($data->author) ? $this->_crf_convert_xml_author($data->author) : '';
		}

		$response['type'] = isset($data->type) ? $data->type : '';
		$response['issn'] = isset($data->ISSN) ? implode(', ', $data->ISSN) : '';
		$response['isbn'] = isset($data->ISBN) ? implode(', ', $data->ISBN) : '';
		$response['subject'] = isset($data->subject) ? implode(', ',$data->subject) : '';
		$response['url'] = isset($data->URL) ? $data->URL : '';
		$response['publisher'] = isset($data->publisher) ? $data->publisher : '';
		$response['issue'] = isset($data->issue) ? $data->issue : '';

		if ($usedFor == 'default' OR $usedFor == 'xml') {
			$response['license'] = isset($data->license) ? $data->license : '';
			
		} elseif ($usedFor == 'csv') {
			$response['license'] = isset($data->license) 
									? $this->_handle_license_crf_csv($data->license) 
									: '';
		}

		$response['prefix'] = isset($data->prefix) ? $data->prefix : '';
		$response['volume'] = isset($data->volume) ? $data->volume : '';
		$response['funder'] = isset($data->funder) ? $data->funder : '';
		$response['abstract'] = isset($data->abstract) ? $data->abstract : '';
		$response['member'] = isset($data->member) ? $data->member : '';

		if ($usedFor == 'default') {
			$response['published_online'] = isset($data->{'published-online'}) ? $data->{'published-online'} : '';
			
		} elseif ($usedFor == 'csv' OR $usedFor == 'xml') {
			$response['published_online'] = isset($data->{'published-online'}) 
											? implode('-', (array)$data->{'published-online'}->{'date-parts'}[0]) 
											: '';
		}

		$response['reference'] = isset($data->reference) ? $data->reference : '';
		return $response;
	}

	protected function _cnvrt_dtl_pmc(object $data, string $usedFor='default') : array
	{
		$response['title']  = isset($data->title) ? $data->title : '';
		$response['doi']    = isset($data->doi) ? $data->doi : '#';

		if ($usedFor == 'default') {
			$response['author'] = isset($data->authorList) 
									? $this->_handle_author_pmc($data->authorList->author) 
									: ($usedFor == 'default' ? '-' : '');

		} elseif ($usedFor == 'xml') {
			$response['author'] = isset($data->authorList) 
									? $this->_handle_author_pmc_xml($data->authorList->author, 1) 
									: '-';
			
		} elseif ($usedFor == 'csv') {
			$response['author'] = isset($data->authorList) 
									? $this->_handle_author_pmc($data->authorList->author, 1) 
									: '-';
		}

		$response['type']    = isset($data->pubTypeList) ? implode(', ',$data->pubTypeList->pubType) : '';
		$response['issn']    = isset($data->journalInfo->journal->issn) ? $data->journalInfo->journal->issn : '';
		$response['isbn']    = '';
		$response['subject'] = '';

		if ($usedFor == 'default') {
			$response['url'] = isset($data->fullTextUrlList) 
								? $this->_resolve_pmc_url($data->fullTextUrlList->fullTextUrl) 
								: '';

		} elseif ($usedFor == 'csv' OR $usedFor == 'xml') {
			$response['url'] = isset($data->fullTextUrlList) 
								? implode(', ', $this->_resolve_pmc_url($data->fullTextUrlList->fullTextUrl)) 
								: '';
		}		

		$response['publisher'] = isset($data->bookOrReportDetails) 
									? $data->bookOrReportDetails->publisher
									: '';

		$response['issue'] = isset($data->journalInfo) 
								? (isset($data->journalInfo->issue) 
									? $data->journalInfo->issue 
									: '') 
								: '';

		$response['license'] = isset($data->license) ? $data->license : '';
		$response['prefix']  = '';
		$response['volume']  = isset($data->journalInfo) 
								? (isset($data->journalInfo->volume) 
									? $data->journalInfo->volume 
									: '')
								: '';

		$response['funder']    = '';
		$response['abstract']  = isset($data->abstractText) ? $data->abstractText : '';
		$response['member']    = '';
		$response['reference'] = '';
		$response['published_online'] = isset($data->firstPublicationDate) ? $data->firstPublicationDate : '';
		return $response;	
	}

	/*=====  End of Section for Handle Detail that Used on Modal Detail  ======*/

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
			$_author = isset($value->fullName) ? $value->fullName : '';
			$email = isset($value->authorAffiliationsList) 
						? $this->_handle_get_email_pmc($value->authorAffiliationsList->authorAffiliation)
						: '';
			if (!empty($email)) {
				foreach ($email as $_email) {
					$adjustEmail = substr($_email, -1) == '.' ? substr($_email, 0 ,-1) : $_email;
					if ($is_for_csv == 1) {
						$response = $_author.'['.$adjustEmail.']';
					} else {
						$response = '<a href="'.base_url('mail/'.str_replace('=', '', base64_encode($_email))).'">';
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

	protected function _handle_license_crf_csv(array $license) : string
	{
		foreach ($license as $key => $lcs) {
			$response[] = $lcs->URL;
		}
		return implode(', ', $response);
	}

}

/* End of file ConvertResponse.php */
/* Location: ./application/libraries/ConvertResponse.php */
