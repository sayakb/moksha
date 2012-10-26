<?php

/**
 * Site templates model
 *
 * Model for creating and importing site templates
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Templates_model extends CI_Model {

	/**
	 * Array of tables to be imported/exported
	 *
	 * @var array
	 */
	var $tables = array('config', 'hubs', 'hub', 'widgets', 'pages', 'roles');

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Get a list of existing sites
	 *
	 * @access	public
	 * @return	array	list of sites
	 */
	public function fetch_sites()
	{
		$site_ary	= array();
		$site_list	= $this->db->get('central_sites')->result();

		foreach ($site_list as $site)
		{
			$site_ary[$site->site_id] = $site->site_url;
		}

		return $site_ary;
	}

	// --------------------------------------------------------------------

	/**
	 * Checks if a specific site exists
	 *
	 * @access	public
	 * @param	int		site identifier
	 * @return	bool	true if it exists
	 */
	public function check_site_exists($site_id)
	{
		return $this->db->where('site_id', $site_id)->count_all_results('central_sites') == 1;
	}

	// --------------------------------------------------------------------

	/**
	 * Exports template for a site
	 *
	 * @access	public
	 * @return	void
	 */
	public function export_template()
	{
		$site_id	= $this->input->post('site');
		$site_key	= crc32($site_id);
		$template	= array();

		// Get a list of tables
		$this->db->select('table_name');
		$this->db->like('table_name', "site_", 'after');
		$this->db->like('table_name', "_{$site_id}", 'before');

		$table_list = $this->db->get('information_schema.TABLES')->result();

		foreach ($table_list as $table)
		{
			$table_ary = explode('_', $table->table_name);
			$table_key = $table_ary[1];

			// Extract the hub ID from hub tables
			if (count($table_ary) == 4)
			{
				$hub_id = $table_ary[2];
			}
			else
			{
				$hub_id = 0;
			}

			if (in_array($table_key, $this->tables))
			{
				// For hub tables, we dump the schema in the template
				// For others, we dump the data
				if ($hub_id == 0)
				{
					$template[$table_key] = $this->db->get($table->table_name)->result_array();
				}
				else
				{
					$table_key	.= "_{$hub_id}";
					$hub_name	 = $this->hub->fetch_name($hub_id);

					$template[$table_key] = $this->hub->schema($hub_name);
				}
			}
		}

		$template = serialize($template);
		$template = $this->encrypt->encode($template, 'moksha');

		// Download the template file
		force_download("site_{$site_key}.tpl", $template);
	}

	// --------------------------------------------------------------------

	/**
	 * Imports template for a site
	 *
	 * @access	public
	 * @return	void
	 */
	public function import_template()
	{
		// Get file upload configuration
		$upload = $this->config->item('upload');
		$config = $upload['temp'];

		// Initialize the file upload library
		$this->upload->initialize($config);

		// Upload the template file
		if ($this->upload->do_upload('template'))
		{
			$file_path = $config['upload_path'].'__site_tpl_file.tpl';

			// Read the file data
			$template = read_file($file_path);
			@unlink(realpath($file_path));

			// Process the template
			$template = $this->encrypt->decode($template, 'moksha');
			$template = unserialize($template);

			if (is_array($template))
			{
				// Create the empty site first
				$site_url	= $this->input->post('site_url');
				$site_id	= $this->site->add($site_url);
				$hub_ids	= array();

				if ($site_id !== FALSE)
				{
					$this->site->site_id = $site_id;

					foreach ($template as $table => $data)
					{
						$table_name = "site_{$table}_{$site_id}";

						// Hub tables have a _ in the name
						// We process the hub tables once we have inserted
						// data to all other tables
						if (strpos($table, '_') === FALSE)
						{
							$this->db->empty_table($table_name);

							foreach ($data as $row)
							{
								$this->db->insert($table_name, $row);
							}
						}
						else
						{
							$hub_id		= substr($table, strpos($table, '_') + 1);
							$hub_ids[]	= $hub_id;
						}
					}

					// Process the hub tables
					foreach ($hub_ids as $hub_id)
					{
						$hub_name = $this->hub->fetch_name($hub_id);

						// Remove this hub from the index table and create the new hub
						$this->hub->drop($hub_name);
						$this->hub->create($hub_name, HUB_DATABASE, $template["hub_{$hub_id}"]);
					}

					return TRUE;
				}
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------
}