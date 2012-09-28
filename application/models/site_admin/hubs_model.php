<?php

/**
 * Hub management model
 *
 * Model for adding, editing and deleting hubs
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
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
	 * @return	array	containing data type list
	 */
	public function fetch_datatypes()
	{
		return array(
			NULL			=> NULL, // Empty first item
			DBTYPE_KEY		=> $this->lang->line('dbtype_key'),
			DBTYPE_INT		=> $this->lang->line('dbtype_int'),
			DBTYPE_TEXT		=> $this->lang->line('dbtype_text'),
			DBTYPE_DATETIME	=> $this->lang->line('dbtype_datetime')
		);
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
		$per_page = $this->config->item('per_page');
		$offset = $per_page * ($page - 1);

		$query = $this->db->limit($per_page, $offset)->get("site_hubs_{$this->bootstrap->site_id}");
		return $query->result();
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
		$hub_source	= $this->input->post('hub_source');

		if ($hub_type == HUB_RSS)
		{
			return $this->hub->create($hub_name, HUB_RSS, $hub_source);
		}
		else if ($hub_type == HUB_DATABASE)
		{
			$hub_name = $this->session->userdata('hub_name');

			if ($hub_name !== FALSE)
			{
				$col_data		= array();
				$col_names		= $this->input->post('col_names');
				$col_datatypes	= $this->input->post('col_datatypes');

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
				redirect(base_url('admin/hubs/add/columns', 'refresh'));
			}
		}

		return FALSE;
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
		$hub_table	= "site_hubs_{$this->bootstrap->site_id}";
		$hub_name	= $this->db->where('hub_id', $hub_id)->get($hub_table)->row()->hub_name;

		return $this->hub->drop($hub_name);
	}

	// --------------------------------------------------------------------
}