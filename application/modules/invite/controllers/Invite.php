<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invite extends CI_Controller {

	protected $userid;

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('login_sess')) {
			redirect('auth','refresh');
		}
		$this->userid = $this->session->userdata('login_sess')['userid'];
	}

	public function index()
	{
		$data['data'] = $this->db->get_where('invited', ['sender' => $this->userid])->result();
		$data['pagename'] = 'Invitation';
		$data['page'] = 'invite_v';
		$this->load->view('template/template', $data);
	}

}

/* End of file Invite.php */
/* Location: ./application/modules/invite/controllers/Invite.php */