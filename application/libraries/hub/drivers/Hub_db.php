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
	}

	// --------------------------------------------------------------------

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
	 * Fetches the schema for a hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	array	hub schema
	 */
	public function schema($hub_id)
	{
		return $this->CI->db_s->field_data("hub_{$this->CI->bootstrap->site_id}_{$hub_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Selects data from a hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @param	array	where claus for data selection
	 * @param	array	order by claus
	 * @param	array	limit to be applied
	 * @return	array	result of the query
	 */
	public function get($hub_id, $where, $order_by, $limit)
	{
		// Generate WHERE claus
		if (is_array($where))
		{
			if (isset($where['AND']) AND is_array($where['AND']))
			{
				foreach ($where['AND'] as $column => $value)
				{
					if (strpos($column, '[LIKE]') === FALSE)
					{
						$this->CI->db_s->where($column, $value);
					}
					else
					{
						$column = str_replace(' [LIKE]', '', $column);
						$this->CI->db_s->like($column, $value);
					}
				}
			}

			if (isset($where['OR']) AND is_array($where['OR']))
			{
				foreach ($where['OR'] as $column => $value)
				{
					if (strpos($column, '[LIKE]') === FALSE)
					{
						$this->CI->db_s->or_where($column, $value);
					}
					else
					{
						$column = str_replace(' [LIKE]', '', $column);
						$this->CI->db_s->or_like($column, $value);
					}
				}
			}
		}

		// Generate ORDER BY claus
		if (is_array($order_by))
		{
			foreach ($order_by as $column => $dir)
			{
				$this->CI->db_s->order_by($column, $dir);
			}
		}

		// Generate LIMIT claus
		if ($limit !== FALSE)
		{
			if (count($limit) == 1)
			{
				$this->CI->db_s->limit($limit[0]);
			}
			else if (count($limit) == 2)
			{
				$this->CI->db_s->limit($limit[0], $limit[1]);
			}
		}

		// Finally. let's query the table
		return $this->CI->db_s->get("hub_{$this->CI->bootstrap->site_id}_{$hub_id}")->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Drops a hub from the database, and related tables, if any
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	void
	 */
	public function drop($hub_id)
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