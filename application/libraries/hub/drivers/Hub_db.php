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
class Hub_db extends Hub_driver {

	/**
	 * Creates a new hub for this site in the DB
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	array	schema for the hub table
	 * @return	void
	 */
	public function create($name, $schema)
	{
		if (is_array($schema) AND count($schema) != 0)
		{
			// Prep and load the dbforge library
			$this->CI->db = $this->CI->db_s;
			$this->CI->load->dbforge();

			// First, insert into the index table
			$data = array(
				'hub_name'		=> $name,
				'hub_driver'	=> HUB_DATABASE
			);

			if ($this->CI->db_s->insert("hubs_{$this->CI->bootstrap->site_id}", $data))
			{
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
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a hub from the database, and related tables, if any
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	void
	 */
	public function delete($hub_id)
	{
		// Prep and load the dbforge library
		$this->CI->db = $this->CI->db_s;
		$this->CI->load->dbforge();
		
		// Drop data table, if it exists
		$table = "hub_{$this->CI->bootstrap->site_id}_{$hub_id}";
		
		if ($this->CI->db_s->table_exists($table))
		{
			$this->CI->dbforge->drop_table($table);
		}
		
		// Remove the hub entry from hub index table
		$this->CI->db_s->delete("hubs_{$this->CI->bootstrap->site_id}", array('hub_id' => $hub_id));
	}

	// --------------------------------------------------------------------
}
// END Hub_db class

/* End of file Hub_db.php */
/* Location: ./application/libraries/hub/drivers/Hub_db.php */