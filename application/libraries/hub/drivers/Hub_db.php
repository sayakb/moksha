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
			$this->CI->load->dbforge();

			// First, insert into the index table
			$data = array(
				'hub_name'		=> $name,
				'hub_driver'	=> HUB_DATABASE
			);

			if ($this->CI->db_s->insert("hubs_{$this->CI->bootstrap->site_id}", $data))
			{
				$ci_schema	= $this->resolve_schema($schema);
				$fields		= $ci_schema->fields;
				$key		= $ci_schema->key;

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
	 * Drops a hub from the database, and related tables, if any
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	void
	 */
	public function drop($hub_id)
	{
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

	/**
	 * Adds a column to the hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @param	array	new column data
	 * @return	void
	 */
	public function add_column($hub_id, $columns)
	{
		$new_cols = $this->resolve_schema($columns)->fields;

		$this->CI->load->dbforge();
		$this->CI->dbforge->add_column("hub_{$this->CI->bootstrap->site_id}_{$hub_id}", $new_cols);
	}

	// --------------------------------------------------------------------

	/**
	 * Drops a column from the hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @param	string	column name
	 * @return	void
	 */
	public function drop_column($hub_id, $column)
	{
		$this->CI->load->dbforge();
		$this->CI->dbforge->drop_column("hub_{$this->CI->bootstrap->site_id}_{$hub_id}", $column);
	}

	// --------------------------------------------------------------------

	/**
	 * Renames a hub column
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @param	string	old column name
	 * @param	string	new column name
	 * @return	void
	 */
	public function rename_column($hub_id, $old_col, $new_col)
	{
		$coldata = array($old_col => array('name' => $new_col));

		$this->CI->load->dbforge();
		$this->CI->dbforge->modify_column("hub_{$this->CI->bootstrap->site_id}_{$hub_id}", $coldata);
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
		// Resolve the WHERE claus
		$this->resolve_where($where);

		// Generate ORDER BY claus
		if (is_array($order_by))
		{
			foreach ($order_by as $column => $dir)
			{
				$this->CI->db_s->order_by($column, $dir);
			}
		}

		// Generate LIMIT claus
		if (is_array($limit))
		{
			$this->CI->db_s->limit($limit[0], $limit[1]);
		}

		// Finally. let's query the table
		return $this->CI->db_s->get("hub_{$this->CI->bootstrap->site_id}_{$hub_id}")->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Inserts data into a writable hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @param	array	data to be inserted
	 * @return	void
	 */
	public function insert($hub_id, $data)
	{
		$this->CI->db_s->insert("hub_{$this->CI->bootstrap->site_id}_{$hub_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Updates data to a writable hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @param	array	data to be updated
	 * @param	array	where claus of the query
	 * @return	void
	 */
	public function update($hub_id, $data, $where)
	{
		$this->resolve_where($where);
		$this->CI->db_s->update("hub_{$this->CI->bootstrap->site_id}_{$hub_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete data from a writable hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @param	array	where claus of the query
	 * @return	void
	 */
	public function delete($hub_id, $where)
	{
		$this->resolve_where($where);
		$this->CI->db_s->delete("hub_{$this->CI->bootstrap->site_id}_{$hub_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Generates CI friendly field array
	 *
	 * @access	private
	 * @param	array	hub style schema
	 * @return	array	having CI style schema and key info
	 */
	private function resolve_schema($schema)
	{
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

		$ci_schema = new stdClass();
		$ci_schema->fields	= $fields;
		$ci_schema->key		= $key;

		return $ci_schema;
	}

	// --------------------------------------------------------------------

	/**
	 * Generates CI friendly WHERE claus
	 *
	 * @access	private
	 * @param	array	hub where claus
	 * @return	void
	 */
	private function resolve_where($where)
	{
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
	}

	// --------------------------------------------------------------------
}
// END Hub_db class

/* End of file Hub_db.php */
/* Location: ./application/libraries/hub/drivers/Hub_db.php */