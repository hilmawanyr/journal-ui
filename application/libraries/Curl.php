<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Curl
{
	protected $ci;

	public function __construct()
	{
        $this->ci =& get_instance();
	}

	public function exec($endpoint, $header='', $verb='GET', $body='')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		
		!empty($header) ? curl_setopt($ch, CURLOPT_HTTPHEADER, array($header)) : '';

		if ($verb !== 'GET') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

}

/* End of file Curl_lib.php */
/* Location: ./application/libraries/Curl_lib.php */
