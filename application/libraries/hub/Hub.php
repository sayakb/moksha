<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Hub Library for Moksha
 *
 * This class handles hub management
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Hub {

	var $CI;

	// --------------------------------------------------------------------
	
	/**
	 * Array of hub driver objects
	 *
	 * @var	array
	 */
	var $_drivers = array();

	// --------------------------------------------------------------------

	/**
	 * Array containing hub details in case multiple requests are
	 * made for the same hub
	 *
	 * @var	array
	 */
	var $_hub_details = array();

	// --------------------------------------------------------------------

	/**
	 * Contains all filter criteria for the query
	 *
	 * @var	array
	 */
	var $_filters = array();

	// --------------------------------------------------------------------

	/**
	 * Array containing the result of the last query
	 *
	 * @var	array
	 */
	var $_result = array();

	// --------------------------------------------------------------------

	/**
	 * Array containing all writable hubs
	 *
	 * @var	array
	 */
	var $_writable_hubs = array(HUB_DATABASE);

	// --------------------------------------------------------------------

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
			$hub = $this->CI->db_s->get_where("hubs_{$this->CI->bootstrap->site_id}", array('hub_name' => $hub_name))->row();
			$this->CI->cache->write($hub, "hubidx_{$this->CI->bootstrap->site_id}_{$hub_name}");
		}

		return $hub;
	}

	// --------------------------------------------------------------------

	/**
	 * Gets a list of hubs
	 *
	 * @access	public
	 * @return	array	containing list of hubs
	 */
	public function get_list()
	{
		if ( ! $list = $this->CI->cache->get("hubidx_{$this->CI->bootstrap->site_id}"))
		{
			$list = $this->CI->db_s->get("hubs_{$this->CI->bootstrap->site_id}")->result();
			$this->CI->cache->write($list, "hubidx_{$this->CI->bootstrap->site_id}");
		}

		return $list;
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
	 * Inserts data into a writable hub
	 *
	 * @access	public
	 * @param	string	hub name
	 * @param	array	data to be inserted
	 * @return	object	of class Hub
	 */
	public function insert($hub_name, $data)
	{
		if ($this->is_writable($hub_name))
		{
			$this->obj_by_hub($hub_name)->insert($this->details($hub_name)->hub_id, $data);
			$this->CI->cache->delete_group("hubdata_{$this->CI->bootstrap->site_id}_{$hub_name}");
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Updates data to a writable hub
	 *
	 * @access	public
	 * @param	string	hub name
	 * @param	array	data to be updated
	 * @param	array	where claus of the query
	 * @return	object	of class Hub
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

			$this->obj_by_hub($hub_name)->update($this->details($hub_name)->hub_id, $data, $where);
			$this->CI->cache->delete_group("hubdata_{$this->CI->bootstrap->site_id}_{$hub_name}");
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete data from a writable hub
	 *
	 * @access	public
	 * @param	string	hub name
	 * @param	array	where claus of the query
	 * @return	object	of class Hub
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

			$this->obj_by_hub($hub_name)->delete($this->details($hub_name)->hub_id, $where);
			$this->CI->cache->delete_group("hubdata_{$this->CI->bootstrap->site_id}_{$hub_name}");
		}

		return $this;
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
	 * Checks if a hub is writable
	 *
	 * @access	private
	 * @param	string	name of the hub
	 * @return	bool	true if hub is writable
	 */
	private function is_writable($hub_name)
	{
		return in_array($this->details($hub_name)->hub_driver, $this->_writable_hubs);
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