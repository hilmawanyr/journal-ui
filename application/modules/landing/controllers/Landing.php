<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Landing extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->session->unset_userdata('HOST');
	}

	public function index()
	{
		$data['nav']  = 'template/landing_nav';
		$data['page'] = 'landing_v';
		$this->load->view('template/template', $data);
	}

}

/* End of file Landing.php */
/* Location: ./application/modules/landing/controllers/Landing.php */
