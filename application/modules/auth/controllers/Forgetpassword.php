<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forgetpassword extends CI_Controller {

	public function find_account()
	{
		$email    = $this->input->post('email');
		$get_user = $this->db->get_where('userlogin', ['username' => $email]);

		if ($get_user->num_rows() == 0) {
			$this->session->set_flashdata('fail', 'Account not found!');
		} else {
			$this->_send_recovery_mail($get_user->row());
			$this->session->set_flashdata('success', 'Please check your email to recovery your password!');
		}

		redirect('auth','refresh');
	}

	protected function _send_recovery_mail(object $user) : void
	{
		$config = array(
	      'protocol'  => 'smtp',
	      'smtp_host' => 'ssl://smtp.gmail.com',
	      'smtp_port' => 465,
	      'smtp_user' => 'hilmawan@ubharajaya.ac.id',
	      'smtp_pass' => '#Hayeer22',
	      'mailtype'  => 'html',
	      'charset'   => 'iso-8859-1'
	    );

	    $message = 'To recovery your password please click here '. base_url('pass_recovery/'.$user->userid);

	    $this->load->library('email', $config);
	    $this->email->set_newline("\r\n");
	    $this->email->from('noreply@mrsd.com', 'MRSD Journal');
	    $this->email->to($user->username);
	    $this->email->subject('Password recovery');
	    $this->email->message($message);
		$this->email->send();
		return;
	}

	public function recovery_password(string $userid) : void
	{
		$data['userid'] = $userid;
		$data['page'] = 'pass_recovery_v';
		$this->load->view('template/template', $data);
	}

	public function recover() : void
	{
		$userid = $this->input->post('userid');
		$password = $this->input->post('password');

		$this->db->update('userlogin', ['password' => password_hash($password, PASSWORD_DEFAULT)], ['userid' => $userid]);

		if ($this->db->affected_rows() == 1) {
			$this->session->set_flashdata('recover_success','Recovery success. Please log in to start your session!');
		} else {
			$this->session->set_flashdata('recover_fail','Recovery fail. The feature is currently unusable!');
		}

		redirect('auth','refresh');
	}

}

/* End of file Forgetpassword.php */
/* Location: ./application/modules/auth/controllers/Forgetpassword.php */