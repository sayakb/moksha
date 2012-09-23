<?php

/**
 * User authentication model
 *
 * Handles database interactions for authentication
 *
 * @package		Moksha
 * @category	Authentication
 * @author		Moksha Team
 */
class Auth_model extends CI_Model {

	/**
	 * Validate user based on context
	 *
	 * @access	public
	 * @param	string	Validation context
	 * @return	bool	true if valid
	 */
	public function validate_user($context)
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$hash = password_hash($password);

		// Central DB uses the users table
		// If we are connecting to sites DB - use the site_<slug>_users table_exists

		if ($context == '%central')
		{
			$db = $this->db_c;
			$table = 'users';
		}
		else
		{
			if ($this->db_s->table_exists("site_{$context}_users"))
			{
				$db = $this->db_s;
				$table = "site_{$context}_users";
			}
			else
			{
				return FALSE;
			}
		}

		$filter = array(
			'user_name'		=> $username,
			'user_password'	=> $hash
		);

		return $db->where($filter)->count_all_results($table) === 1;
	}
}