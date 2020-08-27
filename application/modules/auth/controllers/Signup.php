<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {
	public $configCaptcha = [];

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('login_sess')) {
			redirect('/');
		}
		$this->configCaptcha = $this->_configCaptcha();
	}

	public function index() : void
	{
		$this->load->helper('captcha');


		$cap = create_captcha($this->configCaptcha);
		
		$data = array(
		        'captcha_time'  => $cap['time'],
		        'ip_address'    => $this->input->ip_address(),
		        'word'          => $cap['word']
		);


		$query = $this->db->insert_string('captcha', $data);
		$this->db->query($query);
		
		$data['cap'] = $cap;
		$data['page'] = 'signup_v';
		$this->load->view('template/template', $data);
	}

	public function register() : void
	{
		extract(PopulateForm());
		$key = $this->_generateRandomString();

		$expiration = time() - (int) $this->configCaptcha['expiration'];

		$this->db->where('captcha_time < ', $expiration)
				 ->where('ip_address', $this->input->ip_address())	
		         ->delete('captcha');

		// Then see if a captcha exists:
		$sql = 'SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?';
		$binds = array($captcha, $this->input->ip_address(), $expiration);
		$query = $this->db->query($sql, $binds);
		$row = $query->row();
		
		if ($row->count == 0) {

			$this->session->set_flashdata('fail','You must submit the word that appears in the image.');

		}else{

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

    /**
     * Config captcha helper
     */
    function _configCaptcha() : array
    {
    	$config = array(
				'expiration'	=> $this->config->item('cap_expiration'),
				'word_length'	=> $this->config->item('cap_word_length'),
				'img_width'		=> $this->config->item('cap_img_width'),
		        'img_path'      => FCPATH .'assets/img/captcha/',
		        'img_url'       => base_url().'assets/img/captcha/'
		);

		return $config;
    }
}

/* End of file Signup.php */
/* Location: ./application/modules/auth/controllers/Signup.php */