<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('login_sess')) {
			redirect('/');
		}
	}

	public function index() : void
	{
		$data['page'] = 'signup_v';
		$this->load->view('template/template', $data);
	}

	public function register() : void
	{
		extract(PopulateForm());
		$key = $this->_generateRandomString();

		$data = [
			'firstname' => $firstname,
			'lastname' => $lastname,
			'email' => $email,
			'password' => password_hash($password, PASSWORD_DEFAULT),
			'address' => $address,
			'institution' => $institution,
			'phone' => $phone,
			'_key' => $key,
			'created_at' => date('Y-m-d H:i:s')
		];

		$this->db->insert('signup', $data);

		if ($this->db->affected_rows() == 1) {
			$this->session->set_flashdata('success','Registration success! Please check your email to activate your account!');
			$this->_send_mail([$email, $key]);
		} else {
			$this->session->set_flashdata('fail','Registration fail!');
		}

		redirect('signup','refresh');
	}

	protected function _send_mail(array $data) : void
	{
		$config = array(
	      'protocol'  => 'smtp',
	      'smtp_host' => 'ssl://smtp.gmail.com',
	      'smtp_port' => 465,
	      'smtp_user' => 'hilmawan@ubharajaya.ac.id', //email id
	      'smtp_pass' => '#Hayeer22',
	      'mailtype'  => 'html',
	      'charset'   => 'iso-8859-1'
	    );

	    $message = 'To activate your account please click here '. base_url('activate/'.$data[1]);

	    $this->load->library('email', $config);
	    $this->email->set_newline("\r\n");
	    $this->email->from('noreply@mrsd.com', 'MRSD Journal');
	    $this->email->to($data[0]);
	    $this->email->subject('Account activation');
	    $this->email->message($message);
		$this->email->send();
		return;
	}

	public function activate_account(string $key) : void
	{
		$getAccount = $this->db->get_where('signup', ['_key' => $key])->row();
		$this->_create_user($key, $getAccount);
		$this->_create_login($key, $getAccount);	
		
		$data['page'] = 'activated_success_v';
		$this->load->view('template/template', $data);
	}

	protected function _create_user(string $key, object $user) : void
	{
		$dataMember = [
			'userid' => $key,
			'name' => $user->firstname . ' ' . $user->lastname,
			'email' => $user->email,
			'phone' => $user->phone,
			'address' => $user->address,
			'is_active' => 1
		];
		$this->db->insert('users', $dataMember);
		return;
	}	

	protected function _create_login(string $key, object $user) : void
	{
		$data = [
			'username' => $user->email,
			'password' => $user->password,
			'userid' => $user->_key,
			'group' => 'MBR',
			'level' => 3,
			'is_active' => 1
		];
		$this->db->insert('userlogin', $data);
		return;
	}

	/**
     * Generate random string
     * 
     * @return string
     */
    protected function _generateRandomString() : string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}

/* End of file Signup.php */
/* Location: ./application/modules/auth/controllers/Signup.php */