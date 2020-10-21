<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Export extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('ConvertResponse', NULL, 'cr');
	}

	/**
	 * Export an article detail to XML base on its host
	 * @param string $doi
	 * @return void
	 */
	public function export_xml(string $doi) : void
	{
		$_DOI = base64_decode($doi);
		switch ($this->session->userdata('HOST')) {
			case 'CRF':
				$this->_crf_xml_export($_DOI);
				break;

			case 'PMC':
				$this->_pmc_xml_export($_DOI);
				break;

			default:
				return;
				break;
		}
	}

	/**
	 * Export article detail from Crossref host to XML format
	 * @param string $doi
	 * @return void
	 */
	private function _crf_xml_export(string $doi) : void
	{
		$_doi 	= str_replace('=', '_', $doi);
		$host   = CRF_HOST.'works/'.urlencode($_doi);
		$header = [CRF_TOKEN];
		$exec   = $this->curl->exec($host, $header);
		$result = json_decode($exec);
		$data['data'] = $this->cr->convert_detail('CRF',$result->message,'xml');
		$this->load->view('xml_export_v2', $data);
	}

	/**
	 * Export article detail from Crossref host to XML format
	 * @param string $doi
	 * @return void
	 */
	private function _pmc_xml_export(string $doi) : void
	{
		$host   = PMC_HOST.'search?query='.urlencode($doi).'&resultType=core&format=json';
		$exec   = $this->curl->exec($host);
		$result = json_decode($exec);
		foreach ($result->resultList->result as $key => $value) {
			$data['data'] = $this->cr->convert_detail('PMC',$value,'xml');
		}
		$this->load->view('xml_export_v2', $data);
	}

	/**
	 * Export to csv
	 * 
	 * @return void
	 */
	public function export_csv(string $doi) : void
	{
		$_DOI = base64_decode($doi);
		switch ($this->session->userdata('HOST')) {
			case 'CRF':
				$this->_crf_csv_export($_DOI);
				break;
			
			case 'PMC':
				$this->_pmc_csv_export($_DOI);
				break;

			default:
				return;
				break;
		}		
	}

	/**
	 * Export data to csv from Crossref
	 * 
	 * @param string $doi
	 * @return void
	 */
	protected function _crf_csv_export(string $doi) : void
	{
		$host   = CRF_HOST.'works/'.urlencode($doi);
		$header = [CRF_TOKEN];
		$exec   = $this->curl->exec($host, $header);
		$result = json_decode($exec);
		$data 	= $this->cr->convert_detail('CRF',$result->message,'csv');
		$this->_to_csv($data);
	}

	/**
	 * Export to csv for EuropePMC
	 * 
	 * @param string $doi
	 * @return void
	 */
	protected function _pmc_csv_export(string $doi) : void
	{
		$host   = PMC_HOST.'search?query='.urlencode($doi).'&resultType=core&format=json';
		$exec   = $this->curl->exec($host);
		$result = json_decode($exec);
		foreach ($result->resultList->result as $key => $value) {
			$data 	= $this->cr->convert_detail('PMC',$value,'csv');
		}
		$this->_to_csv($data);
	}

	/**
	 * Force download csv file
	 * 
	 * @param array $data
	 * @return void
	 */
	protected function _to_csv(array $data) : void
	{
		error_reporting(0);
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="export.csv";');
		$fp = fopen('php://output', 'w');
		
		$arr = [];
		foreach ($data as $key => $value) {
			$header[] = strtoupper($key);
		}

		foreach ($data as $key => $value) {
			$content[] = $value;
		}

		array_push($arr, $header);
		array_push($arr, $content);

		foreach ($arr as $datas) {
			fputcsv($fp, @$datas);
		}

		fclose($fp);
	}

	/**
	 * Export all result
	 * 
	 * @return void
	 */
	public function export_all_xml(string $param) : void
	{
		$_param  = base64_decode($param);
		$keyword = explode('|', $_param)[0];
		$filter  = explode('|', $_param)[1];
		$offset  = explode('|', $_param)[2];
		$host    = $this->session->userdata('HOST');
		switch ($host) {
			case 'CRF':
				$data['collection'] = $this->_crf_export_all_xml($keyword, $filter, $offset);
				$this->load->view('crf_xml_export_all2', $data);
				break;
			
			default:
				$data['collection'] = $this->_pmc_export_all_xml($keyword, $filter, $offset);
				$this->load->view('pmc_xml_export_all2', $data);
				break;
		}		
	}

	/**
	 * Set host for Crossref
	 * 
	 * @return string
	 */
	protected function _crf_host(string $keyword, string $filter='', int $offset) : string
	{
		if (!empty($filter)) {
			$host = CRF_HOST.'works?query='.$keyword;
			$host .= '&filter=type:'.urlencode($filter).'&rows=100&offset='.$offset;
		} else {
			$host = CRF_HOST.'works?query='.$keyword.'&rows=100&offset='.$offset;
			$host .= '&select=abstract,URL,member,posted,created,license,ISSN,issue,';
			$host .= 'prefix,author,DOI,funder,archive,subject,subtitle,';
			$host .= 'published-online,publisher-location,reference,title,link,type,';
			$host .= 'publisher,volume,ISBN';
		}
		return $host;
	}

	/**
	 * Export xml result Crossref
	 * 
	 * @return void
	 */
	protected function _crf_export_all_xml(string $keyword, string $filter='', int $offset) : array
	{
		$host   = $this->_crf_host($keyword, $filter, $offset);
		$header = [CRF_TOKEN];
		$exec   = $this->curl->exec($host, $header);
		$result = json_decode($exec)->message;
		foreach ($result->items as $value) {
			$data[] = $this->cr->convert_detail('CRF', $value, 'xml');
		}
		return $data;
	}

	/**
	 * Set PMC host
	 * 
	 * @return string
	 */
	protected function _pmc_host(string $keyword, string $filter='', string $offset) : string
	{
		if (!empty($filter)) {
			$host = PMC_HOST.'search?query='.urlencode($keyword).'%20PUB_TYPE:'.urlencode($filter);
			$host .= '&pageSize=25&resultType=core&format=json&cursorMark='.$offset;
		} else {
			$host = PMC_HOST.'search?query='.urlencode($keyword);
			$host .= '&pageSize=25&resultType=core&format=json&cursorMark='.$offset;
		}
		return $host;
	}

	/**
	 * Export xml result EuropePMC
	 * 
	 * @return void
	 */
	protected function _pmc_export_all_xml(string $keyword, string $filter='', string $offset) : array
	{
		$host   = $this->_pmc_host($keyword, $filter, $offset);
		$exec   = $this->curl->exec($host);
		$result = json_decode($exec)->resultList->result;
		foreach ($result as $key => $value) {
			$collection[] = $this->cr->convert_detail('PMC',(object)$value,'xml');
		}
		return $collection;
	}

	/**
	 * Convert all result to CSV
	 *
	 * @return void
	 */
	public function export_all_csv(string $param)
	{
		$_param  = base64_decode($param);
		$keyword = explode('|', $_param)[0];
		$filter  = explode('|', $_param)[1];
		$offset  = explode('|', $_param)[2];
		$host    = $this->session->userdata('HOST');
		switch ($host) {
			case 'CRF':
				$this->_crf_export_all_csv($keyword, $filter, $offset);
				break;
			
			default:
				$this->_pmc_export_all_csv($keyword, $filter, $offset);
				break;
		}			
	}

	/**
	 * Export all result PMC to CSV
	 * 
	 * @return 
	 */
	protected function _crf_export_all_csv(string $keyword, string $filter='', string $offset)
	{
		$host   = $this->_crf_host($keyword, $filter, $offset);
		$header = [CRF_TOKEN];
		$exec   = $this->curl->exec($host, $header);
		$result = json_decode($exec);
		// dd($result);
		foreach ($result->message->items as $value) {
			$data[] = $this->cr->convert_detail('CRF',$value,'csv');
		}
		// dd($data);
		$this->_to_csv_all($data);
	}

	/**
	 * Export all result PMC to CSV
	 * 
	 * @return 
	 */
	protected function _pmc_export_all_csv(string $keyword, string $filter='', string $offset)
	{
		$host   = $this->_pmc_host($keyword, $filter, $offset);
		$exec   = $this->curl->exec($host);
		$result = json_decode($exec);
		foreach ($result->resultList->result as $key => $value) {
			$data[] = $this->cr->convert_detail('PMC',$value,'csv');
		}
		$this->_to_csv_all($data);
	}

	/**
	 * Force download csv file
	 * 
	 * @param array $data
	 * @return void
	 */
	protected function _to_csv_all(array $data) : void
	{
		error_reporting(0);
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="export.csv";');
		$fp = fopen('php://output', 'w');

		// header
		$arr = [];
		foreach ($data as $key => $value) {
			$datas = array_keys($value);
			$header = [];
			foreach ($datas as $k => $v) {
				array_push($header, strtoupper($v));
			}
		}
		array_push($arr, $header);
		foreach ($arr as $datas) {
			fputcsv($fp, $datas);
		}

		// content
		foreach ($data as $datax) {
			fputcsv($fp, $datax);
		}

		fclose($fp);
	}

}

/* End of file Export.php */
/* Location: ./application/modules/landing/controllers/Export.php */