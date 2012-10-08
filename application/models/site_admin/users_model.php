<?php

/**
 * User management model
 *
 * Model for managing users for a site
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
		$user = $this->db->get("site_users_{$this->bootstrap->site_id}")->row();

		// We need the role in <role1>|<role2>|.. format
		$user->user_roles = implode('|', unserialize($user->user_roles));

		return $user;
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

		$query = $this->db->limit($config['per_page'], $offset)->order_by('user_name')->get("site_users_{$this->bootstrap->site_id}");
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
		$roles = $this->db->get("site_roles_{$this->bootstrap->site_id}")->result();

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
		return $this->db->count_all_results("site_users_{$this->bootstrap->site_id}");
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
		$query = $this->db->get_where("site_users_{$this->bootstrap->site_id}", array('user_id' => $user_id));

		if ($query->num_rows() == 1)
		{
			return $query->row()->user_founder == 1;
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
		$roles		= $this->input->post('user_roles');
		$roles_ary	= explode('|', $roles);

		// Add user data
		$data = array(
			'user_name'		=> $this->input->post('username'),
			'user_password'	=> password_hash($this->input->post('password')),
			'user_email'	=> $this->input->post('email'),
			'user_roles'	=> serialize($roles_ary)
		);

		return $this->db->insert("site_users_{$this->bootstrap->site_id}", $data);
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
		$roles		= $this->input->post('user_roles');
		$roles_ary	= explode('|', $roles);

		// Admin role must be there for a founder user
		if ($is_founder && ! in_array(ROLE_ADMIN, $roles_ary))
		{
			$roles_ary[] = ROLE_ADMIN;
		}

		// Add user data
		$data = array(
			'user_name'		=> $this->input->post('username'),
			'user_email'	=> $this->input->post('email'),
			'user_roles'	=> serialize($roles_ary)
		);

		if (!empty($password))
		{
			$data['user_password'] = password_hash($this->input->post('password'));
		}

		return $this->db->update("site_users_{$this->bootstrap->site_id}", $data, array('user_id' => $user_id));
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
		return $this->db->delete("site_users_{$this->bootstrap->site_id}", array('user_id' => $user_id));
	}

	// --------------------------------------------------------------------
}