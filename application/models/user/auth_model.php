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
	 * Validates the user credentials
	 *
	 * @access	public
	 * @return	bool	true if valid
	 */
	public function validate_user()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$hash = password_hash($password);

		// Choose the database based on whether we are in central or not
		//  - Central DB uses the users table
		//  - If we are connecting to sites DB - use the users_siteId table

		if ($this->bootstrap->in_central)
		{
			$table = 'users';
		}
		else
		{
			$table = "users_{$this->bootstrap->site_id}";
		}

		$filter = array(
			'user_name'		=> $username,
			'user_password'	=> $hash
		);

		return $this->db->where($filter)->count_all_results($table) === 1;
	}

	// --------------------------------------------------------------------
}