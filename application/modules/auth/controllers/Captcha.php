<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Captcha extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('captcha');
	}

	public function refresh()
	{
		$config = array(
				'expiration'	=> $this->config->item('cap_expiration'),
				'word_length'	=> $this->config->item('cap_word_length'),
				'img_width'		=> $this->config->item('cap_img_width'),
		        'img_path'      => FCPATH .'assets/img/captcha/',
		        'img_url'       => base_url().'assets/img/captcha/'
		);

		$cap = create_captcha($config);
		
		$data = array(
		        'captcha_time'  => $cap['time'],
		        'ip_address'    => $this->input->ip_address(),
		        'word'          => $cap['word']
		);

		$query = $this->db->insert_string('captcha', $data);
		$this->db->query($query);
		
		$image = $cap['image'];

		echo $image;
	}

}

/* End of file Captcha.php */
/* Location: ./application/controllers/Captcha.php */