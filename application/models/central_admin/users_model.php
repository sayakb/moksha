<?php

/**
 * User management model
 *
 * Model for managing central administration users
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Users_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get user details against a specific user ID
	 *
	 * @access	public
	 * @param	int	User ID
	 * @return	array	Containing user details
	 */
	public function get_user($user_id)
	{
		$query = $this->db_c->get_where('users', array('user_id' => $user_id));

		return $query->row();
	}

	/**
	 * Add a new central admin
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function add_user()
	{
		// Get user data
		$user_name = $this->input->post('username');
		$user_email = $this->input->post('email');
		$password = $this->input->post('password');
		$password_hash = password_hash($password);

		// Build the insert query
		$data = array(
			'user_name'		=> $user_name,
			'user_password'	=> $password_hash,
			'user_email'	=> $user_email
		);

		return $this->db_c->insert('users', $data);
	}

	/**
	 * Update a central admi user
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function update_user($user_id)
	{
		// Get user data
		$user_name = $this->input->post('username');
		$user_email = $this->input->post('email');
		$password = $this->input->post('password');
		$password_hash = password_hash($password);

		// Build the insert query
		$data = array(
			'user_name'		=> $user_name,
			'user_email'	=> $user_email
		);

		if (!empty($password))
		{
			$data['user_password'] = $password_hash;
		}

		$this->db_c->where('user_id', $user_id);
		return $this->db_c->update('users', $data);
	}

	/**
	 * Delete a user from the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function delete_user($user_id)
	{
		$this->db_c->where('user_id', $user_id);

		return $this->db_c->delete('users');
	}
}