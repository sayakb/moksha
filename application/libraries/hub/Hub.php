<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Hub Library for Moksha
 *
 * This class handles hub management
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Hub {

	var $CI;
	var $_drivers = array();
	var $_hub_details = array();
	var $_filters = array();
	var $_result = array();
	var $_writable_hubs = array(HUB_DATABASE);

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
		$this->reset();
	}

	// --------------------------------------------------------------------

	/**
	 * Gets all details for the specified hub
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @return	mixed	returns hub details, or false if not found
	 */
	public function details($hub_name)
	{
		// Check if we have the data locally set, this is always faster
		if (isset($this->_hub_details[$hub_name]))
		{
			return $this->_hub_details[$hub_name];
		}

		// Look up the data from cache or DB
		else if ( ! $hub = $this->CI->cache->get("hubidx_{$this->CI->bootstrap->site_id}_{$hub_name}"))
		{
			$hub = $this->CI->db->get_where("site_hubs_{$this->CI->bootstrap->site_id}", array('hub_name' => $hub_name))->row();
			$this->CI->cache->write($hub, "hubidx_{$this->CI->bootstrap->site_id}_{$hub_name}");
		}

		return $hub;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches hub metadata for all hubs in the site
	 * Dot not fetch content or schema
	 *
	 * @access	public
	 * @return	array	containing list of hubs
	 */
	public function fetch_list()
	{
		if ( ! $list = $this->CI->cache->get("hubidx_{$this->CI->bootstrap->site_id}"))
		{
			$list = $this->CI->db->get("site_hubs_{$this->CI->bootstrap->site_id}")->result();
			$this->CI->cache->write($list, "hubidx_{$this->CI->bootstrap->site_id}");
		}

		return $list;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches the name for a hub against its ID
	 *
	 * @access	public
	 * @param	int		hub identifier
	 * @return	string	hub name
	 */
	public function fetch_name($hub_id)
	{
		if ($hub_id != '-1')
		{
			$hub = $this->CI->db->get_where("site_hubs_{$this->CI->bootstrap->site_id}", array('hub_id' => $hub_id));

			if ($hub->num_rows() == 1)
			{
				return $hub->row()->hub_name;
			}
		}

		return HUB_NONE;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns number of hubs this site has
	 *
	 * @access	public
	 * @return	int		containing count of hubs
	 */
	public function count_list()
	{
		return $this->CI->db->count_all("site_hubs_{$this->CI->bootstrap->site_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a new hub for this site
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	string	hub driver name (see constants.php)
	 * @param	mixed	metadata related to the hub
	 * @return	object	of class Hub
	 */
	public function create($hub_name, $driver, $data = FALSE)
	{
		$this->obj_by_driver($driver)->create($hub_name, $data);
		$this->CI->cache->delete_group("hubidx_{$this->CI->bootstrap->site_id}");

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Modifies a hub metadata (name and source)
	 *
	 * @access	public
	 * @param	string	hub name
	 * @param	string	new hub data
	 * @return	object	of class Hub
	 */
	public function modify($hub_name, $new_data)
	{
		$this->CI->db->where('hub_name', $hub_name);
		$this->CI->db->update("site_hubs_{$this->CI->bootstrap->site_id}", $new_data);
		$this->CI->cache->delete_group("hubidx_{$this->CI->bootstrap->site_id}");

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Drops a hub from the database, and related tables, if any
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @return	object	of class Hub
	 */
	public function drop($hub_name)
	{
		$this->obj_by_hub($hub_name)->drop($this->details($hub_name)->hub_id);
		$this->CI->cache->delete_group("hubidx_{$this->CI->bootstrap->site_id}");

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Adds a column to the hub
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	array	new column data
	 * @return	object	of class Hub
	 */
	public function add_column($hub_name, $columns)
	{
		if ($this->is_writable($hub_name))
		{
			$this->obj_by_hub($hub_name)->add_column($this->details($hub_name)->hub_id, $columns);
			$this->CI->cache->delete_group("hubschema_{$this->CI->bootstrap->site_id}_{$hub_name}");
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Drops a column from the hub
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	string	column name
	 * @return	object	of class Hub
	 */
	public function drop_column($hub_name, $column)
	{
		if ($this->is_writable($hub_name))
		{
			$this->obj_by_hub($hub_name)->drop_column($this->details($hub_name)->hub_id, $column);
			$this->CI->cache->delete_group("hubschema_{$this->CI->bootstrap->site_id}_{$hub_name}");
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Renames a hub column
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	string	old column name
	 * @param	string	new column name
	 * @return	object	of class Hub
	 */
	public function rename_column($hub_name, $old_col, $new_col)
	{
		if ($this->is_writable($hub_name))
		{
			$this->obj_by_hub($hub_name)->rename_column($this->details($hub_name)->hub_id, $old_col, $new_col);
			$this->CI->cache->delete_group("hubschema_{$this->CI->bootstrap->site_id}_{$hub_name}");
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches the schema for a hub
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @return	array	hub schema
	 */
	public function schema($hub_name)
	{
		if ( ! $schema = $this->CI->cache->get("hubschema_{$this->CI->bootstrap->site_id}_{$hub_name}"))
		{
			$schema = $this->obj_by_hub($hub_name)->schema($this->details($hub_name)->hub_id);
			$this->CI->cache->write($schema, "hubschema_{$this->CI->bootstrap->site_id}_{$hub_name}");
		}

		return $schema;
	}

	// --------------------------------------------------------------------

	/**
	 * Get column list for a specific hub
	 *
	 * @access	public
	 * @param	string	hub name
	 * @return	array	containing column list
	 */
	public function column_list($hub_name)
	{
		$columns_data	= $this->schema($hub_name);
		$columns_ary	= array();

		foreach ($columns_data as $name => $data_type)
		{
			$columns_ary[$name] = $name;
		}

		return $columns_ary;
	}

	// --------------------------------------------------------------------

	/**
	 * Selects data from a hub
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	array	where claus of the query
	 * @param	array	order by claus of the query
	 * @param	array	limit claus of the query
	 * @return	object	of class Hub
	 */
	public function get($hub_name, $_where = FALSE, $_order_by = FALSE, $_limit = FALSE)
	{
		// Get the filter data for this query
		$where = $this->_filters['where'];
		$order_by = $this->_filters['order_by'];
		$limit = $this->_filters['limit'];
		
		// Merge local filter data
		if (is_array($_where))
		{
			if (isset($_where['AND']) AND is_array($_where['AND']))
			{
				$where['AND'] = array_merge($where['AND'], $_where['AND']);
			}

			if (isset($_where['OR']) AND is_array($_where['OR']))
			{
				$where['OR'] = array_merge($where['OR'], $_where['OR']);
			}
		}

		if (is_array($_order_by))
		{
			$order_by = array_merge($order_by, $_order_by);
		}

		if (is_array($_limit))
		{
			$limit = $_limit;
		}

		// Determine the cache key
		$s_where = serialize($where);
		$s_order = serialize($order_by);
		$s_limit = serialize($limit);

		// Get the hub data and store locally
		$key = "hubdata_{$this->CI->bootstrap->site_id}_{$hub_name}_{$s_where}{$s_order}{$s_limit}";
		
		if ( ! $this->_result = $this->CI->cache->get($key))
		{
			$this->_result = $this->obj_by_hub($hub_name)->get($this->details($hub_name)->hub_id, $where, $order_by, $limit);
			$this->CI->cache->write($this->_result, $key);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns count of rows in a hub
	 *
	 * @access	public
	 * @param	string	hub name
	 * @return	int		record count
	 */
	public function count_all($hub_name)
	{
		return $this->obj_by_hub($hub_name)->count_all($this->details($hub_name)->hub_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Inserts data into a writable hub
	 *
	 * @access	public
	 * @param	string	hub name
	 * @param	array	data to be inserted
	 * @return	int		affected rows
	 */
	public function insert($hub_name, $data)
	{
		if ($this->is_writable($hub_name))
		{
			if ($count = $this->obj_by_hub($hub_name)->insert($this->details($hub_name)->hub_id, $data))
			{
				$this->CI->cache->delete_group("hubdata_{$this->CI->bootstrap->site_id}_{$hub_name}");
				return $count;
			}
		}

		return 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Updates data to a writable hub
	 *
	 * @access	public
	 * @param	string	hub name
	 * @param	array	data to be updated
	 * @param	array	where claus of the query
	 * @return	int		affected rows
	 */
	public function update($hub_name, $data, $_where = FALSE)
	{
		if ($this->is_writable($hub_name))
		{
			// Get the filter data for this query
			$where = $this->_filters['where'];

			// Merge local filter data
			if (is_array($_where))
			{
				if (isset($_where['AND']) AND is_array($_where['AND']))
				{
					$where['AND'] = array_merge($where['AND'], $_where['AND']);
				}

				if (isset($_where['OR']) AND is_array($_where['OR']))
				{
					$where['OR'] = array_merge($where['OR'], $_where['OR']);
				}
			}

			if ($count = $this->obj_by_hub($hub_name)->update($this->details($hub_name)->hub_id, $data, $where))
			{
				$this->CI->cache->delete_group("hubdata_{$this->CI->bootstrap->site_id}_{$hub_name}");
				return $count;
			}
		}

		return 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete data from a writable hub
	 *
	 * @access	public
	 * @param	string	hub name
	 * @param	array	where claus of the query
	 * @return	int		affected rows
	 */
	public function delete($hub_name, $_where = FALSE)
	{
		if ($this->is_writable($hub_name))
		{
			// Get the filter data for this query
			$where = $this->_filters['where'];

			// Merge local filter data
			if (is_array($_where))
			{
				if (isset($_where['AND']) AND is_array($_where['AND']))
				{
					$where['AND'] = array_merge($where['AND'], $_where['AND']);
				}

				if (isset($_where['OR']) AND is_array($_where['OR']))
				{
					$where['OR'] = array_merge($where['OR'], $_where['OR']);
				}
			}

			if ($count = $this->obj_by_hub($hub_name)->delete($this->details($hub_name)->hub_id, $where))
			{
				$this->CI->cache->delete_group("hubdata_{$this->CI->bootstrap->site_id}_{$hub_name}");
				return $count;
			}
		}

		return 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Add WHERE claus to the hub query
	 * This will be typically added as "AND WHERE"
	 *
	 * @access	public
	 * @param	string	column name and operator (optional)
	 * @param	string	column value
	 * @return	object	of class Hub
	 */
	public function where($column, $value)
	{
		$this->_filters['where']['AND'][$column] = $value;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add WHERE claus to the hub query
	 * This will be typically added as "OR WHERE"
	 *
	 * @access	public
	 * @param	string	column name and operator (optional)
	 * @param	string	column value
	 * @return	object	of class Hub
	 */
	public function or_where($column, $value)
	{
		$this->_filters['where']['OR'][$column] = $value;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add ORDER BY claus to the query
	 *
	 * @access	public
	 * @param	string	column name and operator (optional)
	 * @param	string	sort direction
	 * @return	object	of class Hub
	 */
	public function order_by($column, $dir = 'ASC')
	{
		$this->_filters['order_by'][$column] = $dir;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add LIMIT claus to the query
	 *
	 * @access	public
	 * @param	int		number of rows to fetch
	 * @param	int		offset for first row
	 * @return	object	of class Hub
	 */
	public function limit($max_rows, $offset = 0)
	{
		$this->_filters['limit'] = array($max_rows, $offset);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the result of the last query
	 *
	 * @access	public
	 * @return	array	result of the query
	 */
	public function result()
	{
		$result = $this->_result;
		$this->reset();

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the first row from the last query
	 *
	 * @access	public
	 * @return	object	stdClass object for the first row
	 */
	public function row()
	{
		if (is_array($this->_result) AND count($this->_result) > 0)
		{
			$row = $this->_result[0];
			$this->reset();

			return $row;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Parses data filter for a hub
	 *
	 * @access	public
	 * @param	string	hub name that is being linked
	 * @param	string	data filter to be parsed
	 * @param	bool	indicates whether to ignore empty values
	 * @return	mixed	array if filter is parsed, false on error
	 */
	public function parse_filters($hub_name, $filters, $ignore_values = FALSE)
	{
		$hub_columns	= $this->column_list($hub_name);
		$operators		= $this->operators();
		$filters		= explode("\n", $filters);
		$first_filter	= TRUE;

		$parsed = array(
			'AND'	=> array(),
			'OR'	=> array()
		);

		if (is_array($filters))
		{
			foreach ($filters as $filter)
			{
				$filter = trim($filter);
				$condition = substr($filter, 0, 1);

				// First filter needs to start with AND
				if ($first_filter AND $condition == '&')
				{
					$first_filter = FALSE;
				}
				else
				{
					return FALSE;
				}

				// Determine the condition for this filter
				$condition = $condition == '&' ? 'AND' : 'OR';

				// Determine the operator
				foreach ($operators as $opkey => $opval)
				{
					$pos = strpos($filter, $opkey);

					if ($pos !== FALSE)
					{
						$offset = strlen($opkey);
						$operator = $opval;

						break;
					}
					else
					{
						return FALSE;
					}
				}

				// Determine the key and value for the parsed array
				$column = trim(substr($filter, 1, $pos - 1));

				if (in_array($column, $hub_columns))
				{
					$key = trim("{$column} {$operator}");
					$value = expr(trim(substr($filter, $pos + $offset)));

					if ( ! empty($value) OR $ignore_values)
					{
						$parsed[$condition][$key] = $value;
					}
				}
				else
				{
					return FALSE;
				}
			}
		}

		// Return the parsed data
		if (count($parsed['AND']) > 0)
		{
			return $parsed;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Parses the order-by value for a hub
	 *
	 * @access	public
	 * @param	string	hub name that is being linked
	 * @param	string	order-by value
	 * @return	mixed	array if filter is parsed, false on error
	 */
	public function parse_orderby($hub_name, $order_by)
	{
		$hub_columns	= $this->column_list($hub_name);
		$order_by		= explode("\n", $order_by);
		$parsed			= array();

		if (is_array($order_by))
		{
			foreach ($order_by as $column)
			{
				$column = trim($column);

				// Check if column name is valid
				if (in_array($column, $hub_columns))
				{
					$parsed[] = $column;
				}
				else
				{
					return FALSE;
				}
			}
		}

		// Return parsed data
		if (count($parsed) > 0)
		{
			return $parsed;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Parses the limit value for a hub
	 *
	 * @access	public
	 * @param	string	hub limit value
	 * @return	mixed	array if filter is parsed, false on error
	 */
	public function parse_limit($limit)
	{
		$limit = trim($limit);
		$limit = explode(',', $limit);

		if (count($limit) == 1)
		{
			$limit[0] = intval(expr(trim($limit[0])));
			$limit[1] = 0;

			if ($limit[0] != 0)
			{
				return $limit;
			}
		}
		else if (count($limit) == 2)
		{
			$limit[0] = intval(expr(trim($limit[0])));
			$limit[1] = intval(expr(trim($limit[1])));

			if ($limit[0])
			{
				return $limit;
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Checks if a hub is writable
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @return	bool	true if hub is writable
	 */
	public function is_writable($hub_name)
	{
		return in_array($this->details($hub_name)->hub_driver, $this->_writable_hubs);
	}

	// --------------------------------------------------------------------

	/**
	 * Gets a list of equality operators in a filter
	 *
	 * @access	private
	 * @return	array	list of operators
	 */
	private function operators()
	{
		return array(
		'[EQ]'		=> '',
		'[NEQ]'		=> '!=',
		'[GRTR]'	=> '>',
		'[LESS]'	=> '<',
		'[GRTREQ]'	=> '>=',
		'[LESSEQ]'	=> '<=',
		'[LIKE]'	=> '[LIKE]'
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Resets local data to start a fresh query
	 *
	 * @access	private
	 * @return	object	of class Hub
	 */
	private function reset()
	{
		$this->_result	= array();
		$this->_filters	= array(
			'where'		=> array(
				'AND'	=> array(),
				'OR'	=> array()
			),
			'order_by'	=> array(),
			'limit'		=> FALSE,
		);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a hub from the database, and related tables, if any
	 *
	 * @access	private
	 * @param	string	name of the hub
	 * @return	object	instance of the driver class
	 */
	private function obj_by_hub($hub_name)
	{
		return $this->obj_by_driver($this->details($hub_name)->hub_driver);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Gets an instance of a specific hub driver
	 *
	 * @access	private
	 * @param	string	driver to load
	 * @return	object	instance of the driver class
	 */
	private function obj_by_driver($driver)
	{
		$driver = "Hub_{$driver}";
		$driver_path = APPPATH."libraries/hub/drivers/{$driver}.php";

		if ( ! isset($this->_drivers[$driver]) AND file_exists($driver_path))
		{
			include_once($driver_path);
			
			if (class_exists($driver))
			{
				$this->_drivers[$driver] = new $driver();
			}
		}
		
		return $this->_drivers[$driver];
	}

	// --------------------------------------------------------------------
}
// END Hub class

/* End of file Hub.php */
/* Location: ./application/libraries/Hub/Hub.php */