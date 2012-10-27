<?php

/**
 * User management model
 *
 * Model for managing central administration users
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Users_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Get user details against a specific user ID
	 *
	 * @access	public
	 * @param	int		user idenfitier
	 * @return	array	containing user details
	 */
	public function fetch_user($user_id)
	{
		$query = $this->db->get_where('central_users', array('user_id' => $user_id));

		if ($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			show_error($this->lang->line('resource_404'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get existing users
	 *
	 * @access	public
	 * @param	int		page number for the list
	 * @return	array	list of users
	 */
	public function fetch_users($page)
	{
		$config = $this->config->item('pagination');
		$offset = $config['per_page'] * ($page - 1);
		$filter = $this->input->post('user_filter');
		
		if ( ! empty($filter))
		{
			$this->db->like('user_name', $filter);
		}

		$query = $this->db->limit($config['per_page'], $offset)->order_by('user_name')->get('central_users');
		return $query->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Return the count of users from the DB
	 *
	 * @access	public
	 * @return	int		having the user count
	 */
	public function count_users()
	{
		return $this->db->count_all_results('central_users');
	}

	// --------------------------------------------------------------------

	/**
	 * Checks if a user is the founder
	 *
	 * @access	public
	 * @param	int		user identifier
	 * @return	bool	true if user is founder
	 */
	public function check_founder($user_id)
	{
		$query = $this->db->get_where('central_users', array('user_id' => $user_id));

		if ($query->num_rows() == 1)
		{
			return $query->row()->founder == 1;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new central admin
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function add_user()
	{
		$data = array(
			'user_name'		=> $this->input->post('username'),
			'password'		=> password_hash($this->input->post('password')),
			'email_address'	=> $this->input->post('email_address')
		);

		$this->admin_log->add('user_create', $data['user_name']);
		return $this->db->insert('central_users', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Update a central admin user
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function update_user($user_id)
	{
		// Update the user data
		$data = array(
			'user_name'		=> $this->input->post('username'),
			'email_address'	=> $this->input->post('email_address')
		);

		// Update the password, if specified
		$password = $this->input->post('password');

		if ( ! empty($password))
		{
			$data['password'] = password_hash($password);
		}

		$this->admin_log->add('user_modify', $data['user_name']);
		$status = $this->db->update('central_users', $data, array('user_id' => $user_id));

		// Update session data if updating self
		// Update the user session so that changes take effect immediately
		$data = $this->db->get_where('central_users', $data, array('user_id' => $user_id))->row();
		update_user_data($data);

		return $status;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a user from the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function delete_user($user_id)
	{
		$user_name = $this->fetch_user($user_id)->user_name;
		$this->admin_log->add('user_delete', $user_name);

		return $this->db->delete('central_users', array('user_id' => $user_id));
	}

	// --------------------------------------------------------------------
}