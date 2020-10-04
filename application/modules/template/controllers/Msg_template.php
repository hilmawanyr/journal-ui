<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Msg_template extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('login_sess')) {
			redirect('logout','refresh');
		}
	}

	public function index()
	{
		$data['templates'] = $this->db->where("deleted_at")->get('message_template')->result();
		$data['page'] = "msg_template_v";
		$this->load->view('template/template', $data);
	}

	public function detail(int $id)
	{
		$data = $this->db->get_where('message_template', ['id' => $id])->row();
		echo $data->template;
	}

	public function remove(int $id)
	{
		$this->db->update('message_template', ['deleted_at' => date('Y-m-d H:i:s')], ['id' => $id]);

		if ($this->db->affected_rows() == 0) {
			$this->session->set_flashdata('fail_template', 'Fail to remove template!');
			redirect('msg_template','refresh');
		}

		$this->session->set_flashdata('success_template', 'Template remove successfully!');
		redirect('msg_template','refresh');
	}

}

/* End of file Msg_template.php */
/* Location: ./application/modules/mail/controllers/Msg_template.php */