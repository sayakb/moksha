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
	 * Get existing users
	 *
	 * @access	public
	 * @param	int		Page number for the list
	 * @return	array	List of users
	 */
	public function get_users($page)
	{
		$per_page = $this->config->item('per_page');
		$offset = $per_page * ($page - 1);
		$filter = $this->input->post('user_filter');
		
		if ( ! empty($filter))
		{
			$this->db_c->like('user_name', $filter);
		}

		$query = $this->db_c->limit($per_page, $offset)->order_by('user_name')->get('users');
		return $query->result();
	}

	/**
	 * Return the count of users from the DB
	 *
	 * @access	public
	 * @return	int		having the user count
	 */
	public function count_users()
	{
		return $this->db_c->count_all_results('users');
	}

	/**
	 * Checks if a user is the founder
	 *
	 * @access	public
	 * @param	int		User ID
	 * @return	bool	true if user is founder
	 */
	public function check_founder($user_id)
	{
		$query = $this->db_c->get_where('users', array('user_id' => $user_id));

		if ($query->num_rows() == 1)
		{
			return $query->row()->user_founder == 1;
		}
		else
		{
			return FALSE;
		}
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

		return $this->db_c->where('user_id', $user_id)->update('users', $data);
	}

	/**
	 * Delete a user from the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function delete_user($user_id)
	{
		return $this->db_c->where('user_id', $user_id)->delete('users');
	}
}