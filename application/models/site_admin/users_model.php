<?php

/**
 * User management model
 *
 * Model for managing users for a site
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
		$this->db->where('user_id', $user_id);
		$query = $this->db->get("site_users_{$this->site->site_id}");

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

		$this->db->where('user_id >', 1);
		$this->db->limit($config['per_page'], $offset);
		$this->db->order_by('user_name');

		$query = $this->db->get("site_users_{$this->site->site_id}");
		return $query->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Gets a list of role names
	 *
	 * @access	public
	 * @param	bool	indicates whether only IDs are to be returned
	 * @return	array	list of role names
	 */
	public function fetch_roles($ids_only = FALSE)
	{
		$roles = $this->db->get("site_roles_{$this->site->site_id}")->result();

		// Add admin role
		$roles = array_merge(array(
			(object)array(
				'role_id'	=> ROLE_ADMIN,
				'role_name'	=> $this->lang->line('administrator')
			)
		), $roles);

		if ($ids_only)
		{
			$role_ids = array();

			foreach ($roles as $role)
			{
				$role_ids[] = $role->role_id;
			}

			return $role_ids;
		}
		else
		{
			return $roles;
		}
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
		return $this->db->count_all_results("site_users_{$this->site->site_id}");
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
		$query = $this->db->get_where("site_users_{$this->site->site_id}", array('user_id' => $user_id));

		if ($query->num_rows() == 1)
		{
			return $query->row()->founder == YES;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new user to the site
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function add_user()
	{
		// Add user data
		$data = array(
			'user_name'		=> $this->input->post('username'),
			'password'		=> password_hash($this->input->post('password')),
			'email_address'	=> $this->input->post('email_address'),
			'active'		=> $this->input->post('active') == ACTIVE ? ACTIVE : BLOCKED,
			'roles'			=> $this->input->post('roles')
		);

		$this->admin_log->add('user_create', $data['user_name']);
		return $this->db->insert("site_users_{$this->site->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Update a user for a site
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function update_user($user_id, $is_founder)
	{
		$status		= $this->input->post('active');
		$roles		= $this->input->post('roles');
		$roles_ary	= explode('|', $roles);

		// Admin role must be there for a founder user
		if ($is_founder AND ! in_array(ROLE_ADMIN, $roles_ary))
		{
			$roles_ary[] = ROLE_ADMIN;
		}

		// Founder cannot be banned
		if ($is_founder AND $status == BLOCKED)
		{
			$status = ACTIVE;
		}

		// Add user data
		$data = array(
			'user_name'		=> $this->input->post('username'),
			'email_address'	=> $this->input->post('email_address'),
			'active'		=> $status == BLOCKED ? BLOCKED : ACTIVE,
			'roles'			=> implode('|', $roles_ary)
		);

		// Update the password, if specified
		$password = $this->input->post('password');

		if ( ! empty($password))
		{
			$data['password'] = password_hash($password);
		}

		$this->admin_log->add('user_modify', $data['user_name']);
		$status = $this->db->update("site_users_{$this->site->site_id}", $data, array('user_id' => $user_id));

		// Update the user session so that changes take effect immediately
		$data = $this->db->get_where("site_users_{$this->site->site_id}", array('user_id' => $user_id))->row();
		update_user_data($data);

		// Kill the user session if blocked
		if ($data->active == BLOCKED)
		{
			kill_session($data->user_name);
		}

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

		return $this->db->delete("site_users_{$this->site->site_id}", array('user_id' => $user_id));
	}

	// --------------------------------------------------------------------
}