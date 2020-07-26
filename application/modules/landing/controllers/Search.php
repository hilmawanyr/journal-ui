<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('pagination');
		$this->load->library('ConvertResponse', NULL, 'cr');
		$this->load->driver('cache', ['adapter' => 'memcached', 'backup' => 'file']);
	}

	/**
	 * Do init search
	 *
	 * @return void
	 */
	public function query() : void
	{
		$this->_query_validation();
		$host      = $this->input->get('source');
		$keyword   = $this->input->get('keyword');
		$page      = !is_null($this->input->get('page')) ? $this->input->get('page') : 0;
		$encodeURL = urlencode($host.$keyword.$page);

		// set used source host
		$this->_set_host($this->input->get('source'));

		// check whether article was exist in cache
		if (!$this->cache->memcached->get($encodeURL)) {
			$fetch_data = $this->_used_host($keyword, $page);
			$this->cache->memcached->save($encodeURL, $fetch_data, 300);
		}

		$data['subjects'] = $this->_subjects();
		$data['types'] = ($this->session->userdata('HOST') == 'CRF') ? $this->_crf_type() : $this->_pmc_type();
		$data['list']  = $this->cache->memcached->get($encodeURL)[0];
		$data['total'] = $this->cache->memcached->get($encodeURL)[1];
		$data['page']  = 'result_v';
		$is_first_page = $page === 0 ? 0 : 1;
		$baseURL = base_url().'search?';
		$this->_pagination($baseURL, $data['total'], $is_first_page);
		$this->load->view('template/template', $data);
	}

	/**
	 * Subjects list
	 * 
	 * @return array
	 */
	protected function _subjects() : array
	{
		$subjects = $this->db->get_where('subjects', ['deleted_at IS NULL' => NULL])->result();
		return $subjects;
	}
	

	/**
	 * Crossref filter for Type
	 * 
	 * @return array
	 */
	protected function _crf_type() : array
	{
		$host = CRF_HOST.'types';
		$header = [CRF_TOKEN];
		$exec = $this->curl->exec($host, $header);
		$response = json_decode($exec);
		$result = $response->message->items;
		return $result;	
	}

	/**
	 * Europe PMC filter for Type
	 * 
	 * @return array
	 */
	protected function _pmc_type() : array
	{
		$types = $this->db->get('pmc_pubtype')->result();
		foreach ($types as $val) {
			$_types[] = (object) [
				'id' => $val->value,
				'label' => $val->label
			];
		}
		return $_types;
	}

	/**
	 * Sanitize query string
	 * 
	 * @return void
	 */
	protected function _query_validation() : void
	{
		if (!$this->input->get('keyword')) {
			$this->session->set_flashdata('unvalid_search', 'Invalid query string');
			redirect('/','refresh');
		}

		if (!$this->input->get('source')) {
			$this->session->set_flashdata('unvalid_search', 'Invalid query string');
			redirect('/','refresh');
		}

		return;
	}

	/**
	 * Set session for used host
	 *
	 * @param string $host
	 * @return void
	 */
	private function _set_host(string $host) : void
	{
		$this->session->set_userdata('HOST', $host);
		return;
	}

	/**
	 * Get data search result from used host
	 *
	 * @param string 	$query (query string for searching)
	 * @param int 		$offset (start row from search result)
	 * @return array
	 */
	private function _used_host(string $query, int $offset) : array
	{
		switch ($this->session->userdata('HOST')) {
			case 'CRF':
				return $this->_get_works_crf($query, $offset);
				break;

			case 'PMC':
				return $this->_get_works_pmc($query, $offset);
				break;

			default:
				redirect('404_override','refresh');
				break;
		}
	}

	/**
	 * Get data by filtered value form result page
	 * 
	 * @return void
	 */	
	public function fiter_query() : void
	{
		$host      = $this->session->userdata('HOST');
		$keyword   = $this->input->get('keyword');
		$filter    = $this->input->get('filter');
		$offset    = !is_null($this->input->get('page')) ? $this->input->get('page') : 0;
		$encodeURL = urlencode($host.$keyword.$filter.$offset);
		
		$data['types'] = ($host == 'CRF') ? $this->_crf_type() : $this->_pmc_type();

		if (!$this->cache->memcached->get($encodeURL)) {
			$fetch_data = ($host == 'CRF') 
							? $this->_filter_q_crf($keyword, $filter, $offset) 
							: $this->_filter_q_pmc($keyword, $filter, $offset);
			$this->cache->memcached->save($encodeURL, $fetch_data, 300);
		}

		$data['subjects'] = $this->_subjects();
		$data['list']  = $this->cache->memcached->get($encodeURL)[0];
		$data['total'] = $this->cache->memcached->get($encodeURL)[1];
		$data['page']  = 'result_v';
		$is_first_page = $offset === 0 ? 0 : 1;
		$baseURL = base_url().'filter_search?';
		$this->_pagination($baseURL, $data['total'], $is_first_page);
		$this->load->view('template/template', $data);
	}

	/**
	 * Hit endpoint for get data from Crossref by filtered search
	 * 
	 * @param string $keyword
	 * @param string $filter
	 * @param int 	 $offset
	 * @return void
	 */
	protected function _filter_q_crf(string $keyword, string $filter, int $offset)
	{
		$endpoint = CRF_HOST.'works?query='.urlencode($keyword).'&filter=type:'.urlencode($filter).'&rows=25&offset='.$offset;
		$header = [CRF_TOKEN];
		$exec = $this->curl->exec($endpoint, $header);
		$response = json_decode($exec);
		$res_number = $response->message->{'total-results'};
		$data = $this->cr->convert('CRF', $response->message->items);
		return [$data, $res_number];
	}

	/**
	 * Get filtered data from EuropePMC
	 * 
	 * @param string $keyword
	 * @param string $filter
	 * @param int 	 $offset
	 * @return void
	 */
	protected function _filter_q_pmc(string $keyword, string $filter, int $offset=0)
	{
		$cm = $offset == 0 ? '*' : '';

		if (!$this->session->userdata('cm')) {
			$this->session->set_userdata('cm', []);
		}

		if ($offset != 0 && $this->session->userdata('cm')) {
			$cm = array_search('next', $this->session->userdata('cm'));
		}

		$host = PMC_HOST.'search?query='.urlencode($keyword).'%20PUB_TYPE:'.urlencode($filter);
		$host .= '&pageSize=25&resultType=core&format=json&cursorMark='.$cm;

		$exec = $this->curl->exec($host);
		$response = json_decode($exec);

		$new_cm = array_merge($this->session->userdata('cm'), 
			[
				$response->request->cursorMark => (string)$offset, 
				$response->nextCursorMark => 'next'
			]);

		$this->session->set_userdata('cm', $new_cm);

		if (count($response->resultList->result) > 0) {
			$res_number = $response->hitCount;
			$data = $this->cr->convert('PMC', $response->resultList->result);	
		} else {
			$res_number = $response->hitCount;
			$data = [];
		}
		
		return [$data, $res_number];
	}

	/**
	 * Get 'works' data from Crosreff base on query string
	 * @param string 	$query (query string)
	 * @param int 		$offset (start row from search result)
	 * @return array
	 */
	private function _get_works_crf(string $query, int $offset=0) : array
	{
		$host = CRF_HOST.'works?query='.urlencode($query).'&rows=25&offset='.$offset;
		$host .= '&select=abstract,URL,member,posted,created,license,ISSN,issue,';
		$host .= 'prefix,author,DOI,funder,archive,subject,subtitle,';
		$host .= 'published-online,publisher-location,reference,title,link,type,';
		$host .= 'publisher,volume,ISBN';

		$header = [CRF_TOKEN];
		$exec = $this->curl->exec($host, $header);
		$response = json_decode($exec);
		$res_number = $response->message->{'total-results'};
		$data = $this->cr->convert('CRF', $response->message->items);
		return [$data, $res_number];
	}

	/**
	 * Get source from EuropePMC
	 * @param string 	$query
	 * @param int 		$offset
	 * @return array
	 */
	protected function _get_works_pmc(string $query, int $page=0) : array
	{
		$cm = $page == 0 ? '*' : '';

		if (!$this->session->userdata('cm')) {
			$this->session->set_userdata('cm', []);
		}

		if ($page != 0 && $this->session->userdata('cm')) {
			$cm = array_search('next', $this->session->userdata('cm'));
		}

		$host = PMC_HOST.'search?query='.urlencode($query).'&pageSize=25&resultType=core&format=json&cursorMark='.$cm;
		$exec = $this->curl->exec($host);
		$response = json_decode($exec);

		$new_cm = array_merge($this->session->userdata('cm'), 
			[
				$response->request->cursorMark => (string)$page, 
				$response->nextCursorMark => 'next'
			]);

		$this->session->set_userdata('cm', $new_cm);

		$res_number = $response->hitCount;
		$data = $this->cr->convert('PMC', $response->resultList->result);
		return [$data, $res_number];
	}

	/**
	 * Get article detail and show it on the modal
	 * @param string $doi
	 * @return void
	 */
	public function modal_detail(string $doi) : void
	{
		$fixDOI = str_replace('_', '=', base64_decode($doi));
		switch ($this->session->userdata('HOST')) {
			case 'CRF':
				$this->_crf_modal_detail($fixDOI);
				break;

			case 'PMC':
				$this->_pmc_modal_detail($fixDOI);
				break;

			default:
				return;
				break;
		}
	}

	/**
	 * Show detail article from Crossref host
	 * @param string $doi
	 * @return void
	 */
	private function _crf_modal_detail(string $doi) : void
	{
		$data['data'] = $this->_crf_get_detail($doi);
		$this->load->view('article_detail_v', $data);
	}

	/**
	 * Show detail article from EuropePMC host
	 * @param string $doi
	 * @return void
	 */
	public function _pmc_modal_detail(string $doi) : void
	{
		$data['data'] = $this->_pmc_get_detail($doi);
		$this->load->view('article_detail_v', $data);
	}

	/**
	 * Get article detail and return it to array
	 * @param string $doi
	 * @return array
	 */
	private function _crf_get_detail(string $doi) : array
	{
		$_doi 	= str_replace('=', '_', $doi);
		$host   = CRF_HOST.'works/'.urlencode($_doi);
		$header = [CRF_TOKEN];
		$exec   = $this->curl->exec($host, $header);
		$result = json_decode($exec);
		$data 	= $this->cr->convert_detail('CRF',$result->message);
		return $data;
	}

	/**
	 * Get detail article and return it to array
	 * @param string $doi
	 * @return array
	 */
	protected function _pmc_get_detail(string $doi) : array
	{
		$_doi 	= str_replace('=', '_', $doi);
		$host   = PMC_HOST.'search?query='.urlencode($_doi).'&resultType=core&format=json';
		$exec   = $this->curl->exec($host);
		$result = json_decode($exec);
		foreach ($result->resultList->result as $value) {
			$data = $this->cr->convert_detail('PMC', $value);	
		}
		return $data;	
	}

	/**
	 * Create pagination element
	 * @param int $total (total row)
	 * @param int $page (page position)
	 * @return object
	 */
	private function _pagination(string $baseURL, int $total, int $page) : object
	{
		if ($page === 0) {
			$queryString = $_SERVER['QUERY_STRING'];
		} else {
			$qs_to_array = explode('&', $_SERVER['QUERY_STRING']);
			array_pop($qs_to_array);
			$queryString = implode('&', $qs_to_array);
		}

		$config['base_url'] = $baseURL.$queryString;
		$config['total_rows'] = $total;
		$config['per_page'] = 25;

		$config['full_tag_open'] = '<div class="row"><div class="col-md-12">';
		$config['full_tag_open'] .= '<ul class="pagination" style="margin: auto;">';
		$config['full_tag_close'] = '</ul></div></div>';

		$config['first_link'] = 'First';
		$config['first_tag_open'] = '<li class="page-item><a class="page-link">';
		$config['first_tag_close'] = '</a></li>';

		$config['last_link'] = 'Last';
		$config['last_tag_open'] = '<li class="page-item><a class="page-link">';
		$config['last_tag_close'] = '</a></li>';

		$config['next_link'] = '&raquo;';
		$config['next_tag_open'] = '<li class="page-item">';
		$config['next_tag_close'] = '</li>';

		$config['prev_link'] = '&laquo;';
		$config['prev_tag_open'] = '<li class="page-item">';
		$config['prev_tag_close'] = '</li>';

		$config['cur_tag_open'] = '<li class="page-item active">';
		$config['cur_tag_open'] .= '<a class="page-link" href="#">';
		$config['cur_tag_close'] = '</a></li>';

		$config['num_tag_open'] = '<li class="page-item">';
		$config['num_tag_close'] = '</li>';

		$config['attributes'] = array('class' => 'page-link');

		return $this->pagination->initialize($config);
	}

}

/* End of file Search.php */
/* Location: ./application/modules/landing/controllers/Search.php */
