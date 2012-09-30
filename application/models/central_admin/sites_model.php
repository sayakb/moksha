<?php

/**
 * Sites management model
 *
 * Model for fetching, adding and deleting all Moksha sites
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Sites_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Get site details against a specific site ID
	 *
	 * @access	public
	 * @param	int		site identifier
	 * @return	array	containing site details
	 */
	public function fetch_site($site_id)
	{
		$query = $this->db->get_where('central_sites', array('site_id' => $site_id));
		return $query->row();
	}

	// --------------------------------------------------------------------

	/**
	 * Get existing sites
	 *
	 * @access	public
	 * @param	int		page number for the list
	 * @return	array	list of sites
	 */
	public function fetch_sites($page)
	{
		$config = $this->config->item('pagination');
		$offset = $config['per_page'] * ($page - 1);

		$query = $this->db->limit($config['per_page'], $offset)->get('central_sites');
		return $query->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Return the count of sites from the DB
	 *
	 * @access	public
	 * @return	int		having the site count
	 */
	public function count_sites()
	{
		return $this->db->count_all_results('central_sites');
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new site to the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function add_site()
	{
		$this->load->dbforge();
		$this->config->load('schema');

		$success = $this->db->insert('central_sites', array('site_url' => $this->input->post('site_url')));
		$site_id = $this->db->insert_id();

		// Generate site specific tables
		if ($success)
		{
			foreach ($this->config->item('schema') as $table => $schema)
			{
				// Add fields to the table
				$this->dbforge->add_field($schema['fields']);

				// Add keys if any are set
				if (isset($schema['keys']) AND is_array($schema['keys']))
				{
					foreach ($schema['keys'] as $columns => $is_primary)
					{
						if (strpos($columns, ',') !== FALSE)
						{
							$columns = explode(',', $columns);
						}

						$this->dbforge->add_key($columns, $is_primary);
					}
				}

				$this->dbforge->create_table("{$table}_{$site_id}");
			}

			// Create admin user with the same credentials of currently logged in user
			$user_id	= $this->session->userdata($this->bootstrap->auth_key.'user_id');
			$user_data	= $this->db->where('user_id', $user_id)->get('central_users')->row_array();

			$this->db->insert("site_users_{$site_id}", $user_data);
		}

		return $success;
	}

	// --------------------------------------------------------------------

	/**
	 * Update a specific site's URL
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function update_site($site_id)
	{
		$data = array('site_url' => $this->input->post('site_url'));
		return $this->db->update('central_sites', $data, array('site_id' => $site_id));
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a site from the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function delete_site($site_id)
	{
		$this->load->dbforge();
		$this->config->load('schema');

		$success = $this->db->delete('central_sites', array('site_id' => $site_id));

		if ($success)
		{
			// Drop all hubs
			foreach ($this->hub->fetch_list() as $hub)
			{
				$table = "hub_{$site_id}_{$hub->hub_driver}";

				if ($hub->hub_driver == HUB_DATABASE AND $this->db->table_exists($table))
				{
					$this->dbforge->drop_table($table);
				}
			}

			// Drop all site tables
			foreach ($this->config->item('schema') as $table => $schema)
			{
				$this->dbforge->drop_table("{$table}_{$site_id}");
			}
		}

		return $success;
	}

	// --------------------------------------------------------------------
}