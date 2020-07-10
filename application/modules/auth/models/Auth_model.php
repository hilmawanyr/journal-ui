<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

	public function is_user_exist(string $username) : array
	{
		$check = $this->db->get_where('userlogin', ['username' => $username])->result();
		return count($check) > 0 ? $check : array();
	}

	public function get_user_data(string $userid)
	{
		$user = $this->db->query("SELECT u.userid, u.name, u.email, ul.group, ul.level FROM users u
								JOIN userlogin ul ON u.userid = ul.userid
								WHERE u.userid = '{$userid}'")->row();
		return $user;
	}
}

/* End of file Auth_model.php */
/* Location: ./application/modules/auth/models/Auth_model.php */
