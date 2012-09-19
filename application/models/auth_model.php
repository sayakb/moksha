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
	 * @param	context Validation context
	 * @returns	Boolean: true if valid
	 */
	public function validate_user($context)
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$hash = password_hash($password);

		if ($context == '%central')
		{
			$this->load->database('central');
		}
		else
		{
			$this->load->database('sites');

			if ( ! $this->db->table_exists("site_{$context}_users"))
			{
				return FALSE;
			}
		}

		$this->db->where('user_name', $username);
		$this->db->where('user_password', $hash);

		return ($this->db->count_all_results('users') == 1);
	}
}