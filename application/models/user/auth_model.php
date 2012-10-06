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
		$username	= $this->input->post('username');
		$password	= $this->input->post('password');
		$hash		= password_hash($password);
		$in_central	= in_central();

		// Choose the database based on whether we are in central or not
		//  - Central DB uses the users table
		//  - If we are connecting to sites DB - use the users_siteId table

		if ($in_central)
		{
			$table = 'central_users';
		}
		else
		{
			$table = "site_users_{$this->bootstrap->site_id}";
		}

		$filter = array(
			'user_name'		=> $username,
			'user_password'	=> $hash
		);

		$query = $this->db->where($filter)->get($table);

		if ($query->num_rows() === 1)
		{
			$user = $query->row();

			// Set the admin role for central, as there is no role data in the DB
			if ($in_central)
			{
				$user_roles = array(ROLE_ADMIN);
			}
			else
			{
				$user_roles = unserialize($user->user_roles);
			}

			$this->session->set_userdata($this->bootstrap->auth_key.'user_id', $user->user_id);
			$this->session->set_userdata($this->bootstrap->auth_key.'user_name', $user->user_name);
			$this->session->set_userdata($this->bootstrap->auth_key.'roles', $user_roles);

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Clears a user's session data
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear_session()
	{
		$this->session->unset_userdata($this->bootstrap->auth_key.'user_id');
		$this->session->unset_userdata($this->bootstrap->auth_key.'user_name');
		$this->session->unset_userdata($this->bootstrap->auth_key.'roles');
	}

	// --------------------------------------------------------------------
}