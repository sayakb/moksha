<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Database hub driver
 *
 * This class handles DB interactions for hub transactions
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Hub_db {

	var $CI;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->dbforge();
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a new hub for this site in the DB
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	array	schema for the hub table
	 * @return	object	of class Hub_db
	 */
	public function create($name, $schema)
	{
		if (is_array($schema) AND count($schema) != 0)
		{
			$this->CI->db = $this->CI->db_s;

			// First, insert into the index table
			$data = array(
				'site_id'		=> $this->CI->bootstrap->site_id,
				'hub_name'		=> $name,
				'hub_driver'	=> HUB_DATABASE
			);

			$this->CI->db_s->insert("hubs_{$this->CI->bootstrap->site_id}", $data);

			// Add fields to the table
			$fields = array();
			$key = FALSE;

			foreach ($schema as $name => $type)
			{
				switch ($type)
				{
					case DBTYPE_KEY:
						$key = $name;
						$fields[$name] = array(
							'type'				=> 'BIGINT',
							'constraint'		=> 15,
							'auto_increment'	=> TRUE
						);
						break;

					case DBTYPE_TEXT:
						$fields[$name] = array(
							'type'	=> 'MEDIUMTEXT'
						);
						break;

					case DBTYPE_INT:
						$fields[$name] = array(
							'type'			=> 'INT',
							'constraint'	=> 10
						);
						break;

					case DBTYPE_DATETIME:
						$fields[$name] = array(
							'type'			=> 'INT',
							'constraint'	=> 11,
							'unsigned'		=> TRUE
						);
						break;
				}
			}

			$this->CI->dbforge->add_field($fields);

			// Add primary key if we have an auto_increment column
			if ($key !== FALSE)
			{
				$this->CI->dbforge->add_key($key, TRUE);
			}

			// Now we determine the hub ID and table name, and create the table.
			$hub_id = $this->CI->db_s->insert_id();
			$this->CI->dbforge->create_table("hub_{$this->CI->bootstrap->site_id}_{$hub_id}");
		}

		return $this;
	}

	// --------------------------------------------------------------------
}
// END Hub_db class

/* End of file Hub_db.php */
/* Location: ./application/libraries/Hub_db.php */