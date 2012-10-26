<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Site Library for Moksha
 *
 * This class handles Moksha site operations
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Site {

	var $CI;

	// --------------------------------------------------------------------

	/**
	 * Current site ID and URL. This is 0 for central
	 *
	 * @access public
	 * @var int
	 */	
	var $site_id;
	var $site_url;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
		$this->init();
	}

	// --------------------------------------------------------------------

	/**
	 * Get site details against a specific site ID
	 *
	 * @access	public
	 * @param	int		site identifier
	 * @return	array	containing site details
	 */
	public function fetch($site_id)
	{
		$query = $this->CI->db->get_where('central_sites', array('site_id' => $site_id));
		return $query->row();
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new site to the DB
	 *
	 * @access	public
	 * @param	string	site URL
	 * @return	int		site ID on success, false on error
	 */
	public function add($site_url)
	{
		$this->CI->load->dbforge();
		$this->CI->config->load('schema');

		$success = $this->CI->db->insert('central_sites', array('site_url' => $site_url));
		$site_id = $this->CI->db->insert_id();

		if ($success)
		{
			// Generate site specific tables
			foreach ($this->CI->config->item('schema') as $table => $schema)
			{
				if (substr($table, 0, 5) == 'site_')
				{
					// Add fields to the table
					$this->CI->dbforge->add_field($schema['fields']);

					// Add keys if any are set
					if (isset($schema['keys']) AND is_array($schema['keys']))
					{
						foreach ($schema['keys'] as $columns => $is_primary)
						{
							if (strpos($columns, ',') !== FALSE)
							{
								$columns = explode(',', $columns);
							}

							$this->CI->dbforge->add_key($columns, $is_primary);
						}
					}

					// Drop table if it exists
					if ($this->CI->db->table_exists("{$table}_{$site_id}"))
					{
						$this->CI->dbforge->drop_table("{$table}_{$site_id}");
					}

					$this->CI->dbforge->create_table("{$table}_{$site_id}");
				}
			}

			// Write the site configuration
			site_config('status',		ONLINE, $site_id);
			site_config('login',		ENABLED, $site_id);
			site_config('registration',	ENABLED, $site_id);
			site_config('captcha',		ENABLED, $site_id);
			site_config('stats',		ENABLED, $site_id);

			// Create anonymous user
			$anonymous = array(
				'user_name'		=> 'anonymous',
				'password'		=> 'anonymous',
				'email_address'	=> 'anonymous',
				'roles'			=> '',
				'founder'		=> 0
			);

			// Create admin user with the same credentials of currently logged in user
			$admin = array(
				'user_name'		=> user_data('user_name'),
				'password'		=> user_data('password'),
				'email_address'	=> user_data('email_address'),
				'roles'			=> ROLE_ADMIN,
				'founder'		=> 1
			);

			$this->CI->db->insert("site_users_{$site_id}", $anonymous);
			$this->CI->db->insert("site_users_{$site_id}", $admin);
		}

		return $success ? $site_id : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update a specific site's URL
	 *
	 * @access	public
	 * @param	int		site identifier
	 * @param	string	site URL
	 * @return	bool	true if successful
	 */
	public function update($site_id, $site_url)
	{
		return $this->CI->db->update('central_sites', array('site_url' => $site_url), array('site_id' => $site_id));
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a site from the DB
	 *
	 * @access	public
	 * @param	int		site identifier
	 * @return	bool	true if successful
	 */
	public function delete($site_id)
	{
		$this->CI->load->dbforge();
		$this->CI->config->load('schema');

		$success = $this->CI->db->delete('central_sites', array('site_id' => $site_id));

		if ($success)
		{
			// Drop all hubs
			$hubs = $this->CI->db->get("site_hubs_{$site_id}")->result();

			foreach ($hubs as $hub)
			{
				$table = "site_hub_{$hub->hub_id}_{$site_id}";

				if ($hub->driver == HUB_DATABASE AND $this->CI->db->table_exists($table))
				{
					$this->CI->dbforge->drop_table($table);
				}
			}

			// Drop all site tables
			foreach ($this->CI->config->item('schema') as $table => $schema)
			{
				if (substr($table, 0, 5) == 'site_')
				{
					$this->CI->dbforge->drop_table("{$table}_{$site_id}");
				}
			}
		}

		return $success;
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize environment variables we need later
	 *
	 * @access	private
	 */
	private function init()
	{
		// Set the default timezone to GMT
		date_default_timezone_set('GMT');

		// Validate site installation
		$this->check_installation();

		// Check and load current site
		$this->load_site();
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the Moksha installation
	 *
	 * @access	private
	 */
	private function check_installation()
	{
		$file_path = APPPATH.'config/database.php';

		if (file_exists($file_path = APPPATH.'config/database.php'))
		{
			include($file_path);

			if (isset($db) AND ! empty($db['default']['hostname']))
			{
				$this->CI->load->database();
				return;
			}
		}

		if ( ! in_install())
		{
			redirect(base_url('install'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Initializes the site identification and ACL
	 *
	 * @access	private
	 */
	private function load_site()
	{
		if ( ! in_install())
		{
			if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')
			{
				$protocol = "https://";
			}
			else
			{
				$protocol = "http://";
			}

			// 1. We strip off the protocol from the base URL
			// 2. We remove :80, if set explicitly
			// 3. We remove the trailing slash
			$this->site_url = str_replace($protocol, '', base_url());
			$this->site_url = str_replace(':80/', '/', $this->site_url);
			$this->site_url = rtrim($this->site_url, '/');

			// Obtain the site ID
			$query = $this->CI->db->get_where('central_sites', array('site_url' => $this->site_url));

			// Set the Site ID if site is found
			// Otherwise, exit with an error message
			if ($query->num_rows() == 1)
			{
				$this->site_id = $query->row()->site_id;
			}
			else if (in_central())
			{
				$this->site_id = 0;
			}
			else
			{
				show_error($this->CI->lang->line('invalid_site'));
			}

			// Load conditional resources
			$resources	= $this->CI->config->item('conditional');
			$uri_key	= $this->CI->uri->segment(1);

			if (isset($resources[$uri_key]))
			{
				$resource = $resources[$uri_key];

				if (isset($resource['libraries']))
				{
					foreach ($resource['libraries'] as $library)
					{
						$this->CI->load->library($library);
					}
				}

				if (isset($resource['helpers']))
				{
					foreach ($resource['helpers'] as $helper)
					{
						$this->CI->load->helper($helper);
					}
				}

				if (isset($resource['languages']))
				{
					foreach ($resource['languages'] as $language)
					{
						$this->CI->lang->load($language);
					}
				}
			}
		}
	}

	// --------------------------------------------------------------------
}
// END Site class

/* End of file Site.php */
/* Location: ./application/libraries/Site.php */