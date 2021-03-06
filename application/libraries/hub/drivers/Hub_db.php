<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Database hub driver
 *
 * This class handles DB interactions for hub transactions
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
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
			$this->CI->db->query("SET default_storage_engine=MYISAM");

			// First, insert into the index table
			$data = array(
				'hub_name'	=> $name,
				'driver'	=> HUB_DATABASE
			);

			if ($this->CI->db->insert("site_hubs_{$this->CI->site->site_id}", $data))
			{
				$ci_schema	= $this->resolve_ci_schema($schema, TRUE);
				$fields		= $ci_schema->fields;
				$key		= $ci_schema->key;

				$this->CI->dbforge->add_field($fields);

				// Add primary key if we have an auto_increment column
				if ($key !== FALSE)
				{
					$this->CI->dbforge->add_key($key, TRUE);
				}

				// Now we determine the hub ID and table name, and create the table.
				$hub_id = $this->CI->db->insert_id();
				$this->CI->dbforge->create_table("site_hub_{$hub_id}_{$this->CI->site->site_id}");
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
		$table = "site_hub_{$hub_id}_{$this->CI->site->site_id}";

		if ($this->CI->db->table_exists($table))
		{
			$this->CI->dbforge->drop_table($table);
		}

		// Remove the hub entry from hub index table
		$this->CI->db->delete("site_hubs_{$this->CI->site->site_id}", array('hub_id' => $hub_id));
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
		$new_cols = $this->resolve_ci_schema($columns)->fields;

		$this->CI->load->dbforge();
		$this->CI->dbforge->add_column("site_hub_{$hub_id}_{$this->CI->site->site_id}", $new_cols);
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
		$this->CI->dbforge->drop_column("site_hub_{$hub_id}_{$this->CI->site->site_id}", $column);
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
		$data_type = NULL;
		$schema = $this->CI->db->field_data("site_hub_{$hub_id}_{$this->CI->site->site_id}");

		foreach ($schema as $column)
		{
			if ($column->name == $old_col)
			{
				$data_type = $column->type;
			}
		}

		if ( ! empty($data_type))
		{
			$coldata = array(
				$old_col => array(
					'name' => $new_col,
					'type' => $data_type
				)
			);

			$this->CI->load->dbforge();
			$this->CI->dbforge->modify_column("site_hub_{$hub_id}_{$this->CI->site->site_id}", $coldata);
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
		$fields = $this->CI->db->field_data("site_hub_{$hub_id}_{$this->CI->site->site_id}");
		return $this->resolve_hub_schema($fields);
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
				$this->CI->db->order_by($column, $dir);
			}
		}

		// Generate LIMIT claus
		if (is_array($limit))
		{
			$this->CI->db->limit($limit[0], $limit[1]);
		}

		// Finally. let's query the table
		return $this->CI->db->get("site_hub_{$hub_id}_{$this->CI->site->site_id}")->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Returns count of rows in a hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	int		record count
	 */
	public function count_all($hub_id)
	{
		return $this->CI->db->count_all("site_hub_{$hub_id}_{$this->CI->site->site_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Returns count of each item in a hub column
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	int		item count
	 */
	public function count_items($hub_id, $column)
	{
		// Get the item counts
		$this->CI->db->select("`{$column}`, COUNT(`{$column}`) AS itemcount");
		$this->CI->db->group_by($column);

		$counts = $this->CI->db->get("site_hub_{$hub_id}_{$this->CI->site->site_id}")->result();

		// Build up a friendly array out of it
		$count_ary = array();

		foreach ($counts as $count)
		{
			$count_ary[$count->$column] = $count->itemcount;
		}

		return $count_ary;
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
		$this->format_data($hub_id, $data);
		$this->inject_metadata($data);
		$this->CI->db->insert("site_hub_{$hub_id}_{$this->CI->site->site_id}", $data);

		return $this->CI->db->affected_rows();
	}

	// --------------------------------------------------------------------

	/**
	 * Updates data to a writable hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @param	array	data to be updated
	 * @param	array	where claus of the query
	 * @return	int		affected rows
	 */
	public function update($hub_id, $data, $where)
	{
		$this->format_data($hub_id, $data);
		$this->resolve_where($where);
		$this->inject_metadata($data);
		$this->CI->db->update("site_hub_{$hub_id}_{$this->CI->site->site_id}", $data);

		return $this->CI->db->affected_rows();
	}

	// --------------------------------------------------------------------

	/**
	 * Delete data from a writable hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @param	array	where claus of the query
	 * @return	int		affected rows
	 */
	public function delete($hub_id, $where)
	{
		$this->resolve_where($where);
		$this->CI->db->delete("site_hub_{$hub_id}_{$this->CI->site->site_id}");

		return $this->CI->db->affected_rows();
	}

	// --------------------------------------------------------------------

	/**
	 * Generates CI friendly field array
	 *
	 * @access	private
	 * @param	array	hub style schema
	 * @param	bool	add metadata columns
	 * @return	array	having CI style schema and key info
	 */
	private function resolve_ci_schema($schema, $add_meta = FALSE)
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
						'type'				=> 'bigint',
						'constraint'		=> 15,
						'auto_increment'	=> TRUE
					);
					break;

				case DBTYPE_INT:
					$fields[$name] = array(
						'type'				=> 'int',
						'constraint'		=> 10
					);
					break;

				case DBTYPE_TEXT:
					$fields[$name] = array(
						'type'				=> 'mediumtext'
					);
					break;

				case DBTYPE_PASSWORD:
					$fields[$name] = array(
						'type'				=> 'varchar',
						'constraint'		=> 128
					);
					break;

				case DBTYPE_DATETIME:
					$fields[$name] = array(
						'type'				=> 'datetime'
					);
					break;
			}
		}

		// Add metadata columns
		if ($add_meta)
		{
			$fields['_moksha_author'] = array(
				'type'			=> 'varchar',
				'constraint'	=> 100,
				'null'			=> false
			);

			$fields['_moksha_timestamp'] = array(
				'type'			=> 'int',
				'constraint'	=> 11,
				'unsigned'		=> true,
				'null'			=> false
			);
		}

		$ci_schema = new stdClass();
		$ci_schema->fields	= $fields;
		$ci_schema->key		= $key;

		return $ci_schema;
	}

	// --------------------------------------------------------------------

	/**
	 * Generates hub style schema from a CI schema
	 *
	 * @access	private
	 * @param	array	ci style schema and key info
	 * @return	array	hub style schema
	 */
	private function resolve_hub_schema($ci_fields)
	{
		$hub_schema = array();

		foreach ($ci_fields as $field)
		{
			// Skip reserved columns
			if (in_array($field->name, array('_moksha_author', '_moksha_timestamp')))
			{
				continue;
			}

			// Resolve general columns to moksha style schema
			if ($field->type == 'bigint')
			{
				$hub_schema[$field->name] = DBTYPE_KEY;
			}

			if ($field->type == 'int')
			{
				$hub_schema[$field->name] = DBTYPE_INT;
			}

			if ($field->type == 'mediumtext')
			{
				$hub_schema[$field->name] = DBTYPE_TEXT;
			}

			if ($field->type == 'varchar')
			{
				$hub_schema[$field->name] = DBTYPE_PASSWORD;
			}

			if ($field->type == 'datetime')
			{
				$hub_schema[$field->name] = DBTYPE_DATETIME;
			}
		}

		return $hub_schema;
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
						$this->CI->db->where($column, $value);
					}
					else
					{
						$column = str_replace(' [LIKE]', '', $column);
						$this->CI->db->like($column, $value);
					}
				}
			}

			if (isset($where['OR']) AND is_array($where['OR']))
			{
				foreach ($where['OR'] as $column => $value)
				{
					if (strpos($column, '[LIKE]') === FALSE)
					{
						$this->CI->db->or_where($column, $value);
					}
					else
					{
						$column = str_replace(' [LIKE]', '', $column);
						$this->CI->db->or_like($column, $value);
					}
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Formats row data to their respective formats
	 *
	 * @access	private
	 * @param	int		hub identifier
	 * @param	array	data to be injected to
	 * @return	void
	 */
	private function format_data($hub_id, &$data)
	{
		$schema = $this->schema($hub_id);

		foreach ($data as $column => $value)
		{
			switch ($schema[$column])
			{
				case DBTYPE_INT:
					$data[$column] = intval($data[$column]);
					break;

				case DBTYPE_PASSWORD:
					$data[$column] = password_hash($data[$column]);
					break;

				case DBTYPE_DATETIME:
					$data[$column] = date('Y-m-d H:i:s', strtotime($data[$column]));
					break;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Inject metadata info when inserting/updating data
	 *
	 * @access	private
	 * @param	array	data to be injected to
	 * @return	void
	 */
	private function inject_metadata(&$data)
	{
		$data = array_merge($data, array(
			'_moksha_author'	=> user_data('user_name'),
			'_moksha_timestamp'	=> time()
		));
	}

	// --------------------------------------------------------------------
}
// END Hub_db class

/* End of file Hub_db.php */
/* Location: ./application/libraries/hub/drivers/Hub_db.php */