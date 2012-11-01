<?php

/**
 * Moksha installer model
 *
 * Logic for setting up Moksha on your server
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Install_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Installs Moksha on your server
	 *
	 * @access	public
	 * @return	void
	 */
	public function install_moksha()
	{
		// Load stuff we need for installation
		$this->config->load('schema');
		$this->load->helper('file');
		$this->load->dbforge();

		$file_path = APPPATH.'config/database.php';

		// Build the DB config data
		$config['hostname']	= $this->input->post('hostname');
		$config['port']		= $this->input->post('port');
		$config['username']	= $this->input->post('username');
		$config['password']	= $this->input->post('password');
		$config['database']	= $this->input->post('db_name');

		// Read the configuration data
		$db_conf = read_file($file_path);
		$db_conf = explode("\n", $db_conf);

		foreach ($db_conf as $line)
		{
			foreach ($config as $item => $value)
			{
				$search = "\$db['default']['{$item}']";

				if (strpos($line, $search) === 0)
				{
					$line = "\$db['default']['{$item}'] = '{$value}';";
					break;
				}
			}

			$out_conf[] = $line;
		}

		$out_conf = implode("\n", $out_conf);

		// Write the configuration data
		write_file($file_path, $out_conf);

		// Load the database with the new config
		$this->load->database();
		$this->db->query("SET default_storage_engine=MYISAM");

		// Create the central tables
		foreach ($this->config->item('schema') as $table => $schema)
		{
			if (substr($table, 0, 8) == 'central_')
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

				// Drop table if it exists
				if ($this->db->table_exists($table))
				{
					$this->dbforge->drop_table($table);
				}

				$this->dbforge->create_table($table);
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a central admin user
	 *
	 * @access	public
	 * @return	array	user details
	 */
	public function create_central_admin()
	{
		// Generate a random password
		$random_pw = substr(md5(microtime()), 0, 8);

		// Create the administrator
		$data = array(
			'user_name'		=> 'admin',
			'password'		=> password_hash($random_pw),
			'email_address'	=> '',
			'founder'		=> 1
		);

		$this->db->insert('central_users', $data);

		return array(
			'username' => 'admin',
			'password' => $random_pw
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Checks if Moksha is already installed on the server
	 *
	 * @access	public
	 * @return	void
	 */
	public function check_already_installed()
	{
		$file_path = APPPATH.'config/database.php';

		if (file_exists($file_path = APPPATH.'config/database.php'))
		{
			include($file_path);

			if (isset($db) AND ! empty($db['default']['hostname']))
			{
				redirect(base_url());
			}
		}
		else
		{
			show_error($this->lang->line('conf_missing'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Validates a MySQLi database connection.
	 *
	 * We do not use CI database driver here as it doesn't expose any
	 * option to check database connectivity easily
	 *
	 * @access	public
	 * @return	bool	true if connection was successful
	 */
	public function check_connection()
	{
		// Get the POSTed connection details
		$hostname	= $this->input->post('hostname');
		$port		= $this->input->post('port');
		$username	= $this->input->post('username');
		$password	= $this->input->post('password');
		$database	= $this->input->post('db_name');

		// Try to load the database with the above config
		return @mysqli_connect($hostname, $username, $password, $database, intval($port));
	}

	// --------------------------------------------------------------------
}