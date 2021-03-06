<?php

/**
 * Role management model
 *
 * Model for adding, editing and deleting user roles
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Roles_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of user roles
	 *
	 * @access	public
	 * @param	int		page number
	 * @return	array	list of roles
	 */
	public function fetch_roles($page)
	{
		$config = $this->config->item('pagination');
		$offset = $config['per_page'] * ($page - 1);

		$query = $this->db->limit($config['per_page'], $offset)->get("site_roles_{$this->site->site_id}");
		return $query->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Get user role details against a specific user ID
	 *
	 * @access	public
	 * @param	int		role idenfitier
	 * @return	array	containing role details
	 */
	public function fetch_role($role_id)
	{
		$query = $this->db->get_where("site_roles_{$this->site->site_id}", array('role_id' => $role_id));

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
	 * Fetches a count of user roles
	 *
	 * @access	public
	 * @return	int		role count
	 */
	public function count_roles()
	{
		return $this->db->count_all_results("site_roles_{$this->site->site_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new user role
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function add_role()
	{
		$data = array('role_name' => $this->input->post('role_name'));
		$this->admin_log->add('role_create', $data['role_name']);

		return $this->db->insert("site_roles_{$this->site->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Update a user_role
	 *
	 * @access	public
	 * @param	int		role identifier
	 * @return	bool	true if successful
	 */
	public function update_role($role_id)
	{
		$data = array('role_name' => $this->input->post('role_name'));
		$this->admin_log->add('role_modify', $data['role_name']);

		return $this->db->update("site_roles_{$this->site->site_id}", $data, array('role_id' => $role_id));
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a user role from the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function delete_role($role_id)
	{
		$role_name = $this->fetch_role($role_id)->role_name;
		$this->admin_log->add('role_delete', $role_name);

		return $this->db->delete("site_roles_{$this->site->site_id}", array('role_id' => $role_id));
	}

	// --------------------------------------------------------------------
}