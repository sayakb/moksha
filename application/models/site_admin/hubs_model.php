<?php

/**
 * Hub management model
 *
 * Model for adding, editing and deleting hubs
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Hubs_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Gets a list of hub drivers
	 *
	 * @access	public
	 * @return	array	containing driver list
	 */
	public function fetch_drivers()
	{
		return array(
			HUB_DATABASE	=> $this->lang->line('hub_type_db'),
			HUB_RSS			=> $this->lang->line('hub_type_rss')
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Gets a list of hub column data types
	 *
	 * @access	public
	 * @param	bool	flag indicating whether key column should be excluded
	 * @return	array	containing data type list
	 */
	public function fetch_datatypes($exclude_key = FALSE)
	{
		$data_types = array(
			DBTYPE_NONE		=> '', // Empty first item
			DBTYPE_KEY		=> $this->lang->line('dbtype_key'),
			DBTYPE_INT		=> $this->lang->line('dbtype_int'),
			DBTYPE_TEXT		=> $this->lang->line('dbtype_text'),
			DBTYPE_PASSWORD	=> $this->lang->line('dbtype_password'),
			DBTYPE_DATETIME	=> $this->lang->line('dbtype_datetime')
		);
		
		if ($exclude_key)
		{
			unset($data_types[DBTYPE_KEY]);
		}
		
		return $data_types;
	}

	// --------------------------------------------------------------------

	/**
	 * Get hub details against a specific hub ID
	 *
	 * @access	public
	 * @param	int		hub identifier
	 * @return	array	containing hub details
	 */
	public function fetch_hub($hub_id)
	{
		$query = $this->db->get_where("site_hubs_{$this->site->site_id}", array('hub_id' => $hub_id));
		return $query->row();
	}

	// --------------------------------------------------------------------

	/**
	 * Get hub columns for a specific hub
	 *
	 * @access	public
	 * @param	string	hub name
	 * @return	array	containing column list
	 */
	public function fetch_columns($hub_name)
	{
		return $this->hub->column_list($hub_name);
	}

	// --------------------------------------------------------------------

	/**
	 * Get existing hubs for this site
	 *
	 * @access	public
	 * @param	int		page number for the list
	 * @return	array	list of hubs
	 */
	public function fetch_hubs($page)
	{
		$config = $this->config->item('pagination');
		$offset = $config['per_page'] * ($page - 1);

		$query = $this->db->limit($config['per_page'], $offset)->get("site_hubs_{$this->site->site_id}");
		return $query->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches data from a hub
	 *
	 * @access	public
	 * @return	int		having the hub contents
	 */
	public function fetch_hub_data($hub_name, $page)
	{
		$config = $this->config->item('pagination');
		$offset = $config['per_page'] * ($page - 1);

		$query = $this->hub->limit($config['per_page'], $offset)->get($hub_name);
		return $query->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Return the count of hubs from the DB
	 *
	 * @access	public
	 * @return	int		having the hub count
	 */
	public function count_hubs()
	{
		return $this->hub->count_list();
	}

	// --------------------------------------------------------------------

	/**
	 * Return the total number of rows in a hub
	 *
	 * @access	public
	 * @return	int		having the hub row count
	 */
	public function count_rows($hub_name)
	{
		return $this->hub->count_all($hub_name);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new hub to the database
	 * If its a DB based hub, save the data to a session and take the user
	 * to the column adding screen
	 *
	 * @access	public
	 * @param	string	hub type - we cannot read it from input as we maybe POSTing from the column screen
	 * @return	bool	true if added, false if not or if a DB hub
	 */
	public function add_hub($hub_type)
	{
		$hub_name	= $this->input->post('hub_name');
		$source		= $this->input->post('source');

		if ($hub_type == HUB_RSS)
		{
			return $this->hub->create($hub_name, HUB_RSS, $source);
		}
		else if ($hub_type == HUB_DATABASE)
		{
			$hub_name = $this->session->userdata('hub_name');

			if ($hub_name !== FALSE)
			{
				$col_data		= array();
				$col_names		= $this->input->post('column_names');
				$col_datatypes	= $this->input->post('column_datatypes');

				for ($idx = 0; $idx < 100; $idx++)
				{
					$col_name		= $col_names[$idx];
					$col_datatype	= $col_datatypes[$idx];

					if ($col_names[$idx] != '')
					{
						$col_data[$col_name] = $col_datatype;
					}
					else
					{
						break;
					}
				}

				$this->session->unset_userdata('hub_name');
				return $this->hub->create($hub_name, HUB_DATABASE, $col_data);
			}
			else
			{
				$this->session->set_userdata('hub_name', $this->input->post('hub_name'));
				redirect(base_url('admin/hubs/add/columns'));
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Renames an hub for this site
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function update_hub()
	{
		$hub_name	= $this->input->post('hub_name_existing');
		$driver		= $this->input->post('driver');
		$new_data	= array('hub_name' => $this->input->post('hub_name'));

		// For RSS hub, we update the source as well
		if ($driver == HUB_RSS)
		{
			$new_data['source'] = $this->input->post('source');
		}

		$this->hub->modify($hub_name, $new_data);
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a hub from the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function delete_hub($hub_id)
	{
		$hub_table	= "site_hubs_{$this->site->site_id}";
		$hub_name	= $this->db->where('hub_id', $hub_id)->get($hub_table)->row()->hub_name;

		return $this->hub->drop($hub_name);
	}

	// --------------------------------------------------------------------

	/**
	 * Adds a column to the hub
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function add_column()
	{
		$hub_name	= $this->input->post('hub_name');
		$colum_data	= array($this->input->post('column_name') => $this->input->post('column_datatype'));

		$this->hub->add_column($hub_name, $colum_data);
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Renames a column in the hub
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function rename_column()
	{
		$hub_name	= $this->input->post('hub_name');
		$old_column	= $this->input->post('column_name_existing');
		$new_column	= $this->input->post('column_name');

		$this->hub->rename_column($hub_name, $old_column, $new_column);
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Deleted a column from a hub
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function delete_column()
	{
		$hub_name		= $this->input->post('hub_name');
		$column_name	= $this->input->post('column_name_existing');

		$this->hub->drop_column($hub_name, $column_name);
		return TRUE;
	}

	// --------------------------------------------------------------------
}