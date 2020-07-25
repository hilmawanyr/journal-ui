<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct(int $islogout=0)
	{
		parent::__construct();
		$this->load->model('auth/auth_model','auth');
	}

	public function index()
	{
		!$this->session->userdata('login_sess') ? $this->load->view('auth_v') : redirect('/');
	}

	public function attemp_login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		// is user exist?
		$is_user_exist = $this->auth->is_user_exist($username, $password);

		// if user exist ...
		if (count($is_user_exist) > 0) {
			foreach ($is_user_exist as $user) {
				// for the correct password
				if (password_verify($password, $user->password)) {
					// create authentcation log
					$where = ['userid' => $user->userid, 'activity' => 1];
					$auth_log = $this->db->insert('auth_log', $where);
					// create session
					$create_sess = $this->_create_login_sess($user->userid);
					// fly to home!
					if (!empty($this->input->post('has_args'))) {
						redirect('mail/'.$this->input->post('has_args'),'refresh');
					} else {
						redirect('/','refresh');	
					}
				}

				// Oops! Password is wrong ..
				$this->session->set_flashdata('wrong_password', 'Your password is wrong!');
				redirect('auth','refresh');
			}
		}

		// if user doesn't exist ...
		$this->session->set_flashdata('account_not_found', 'Account not found!');
		redirect('auth','refresh');
	}

	protected function _create_login_sess(string $userid) : void
	{
		$userdata = $this->auth->get_user_data($userid);
		$array = array(
			'userid' => $userdata->userid,
			'name'   => $userdata->name,
			'email'  => $userdata->email,
			'group'  => $userdata->group,
			'level'  => $userdata->level
		);
		$this->session->set_userdata('login_sess', $array);
		return;
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('/','refresh');
	}

	public function redirect_auth(string $data) : void
	{
		!$this->session->userdata('login_sess')
			? $this->load->view('auth_v', ['email' => $data])
			: header("location:".$_SERVER['HTTP_REFERER']);
	}

}

/* End of file Auth.php */
/* Location: ./application/modules/auth/controllers/Auth.php */
