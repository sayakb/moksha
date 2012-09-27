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
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
		$this->reset();
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
	public function create($name, $driver, $data = FALSE)
	{
		$this->obj_by_driver($driver)->create($name, $data);

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
	public function schema($name)
	{
		if ( ! $schema = $this->CI->cache->get("hubschema_{$this->CI->bootstrap->site_id}_{$name}"))
		{
			$schema = $this->obj_by_hub($name)->schema($this->details($name)->hub_id);
			$this->CI->cache->write($schema, "hubschema_{$this->CI->bootstrap->site_id}_{$name}");
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
	public function get($name, $_where = FALSE, $_order_by = FALSE, $_limit = FALSE)
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
		$serial_where = serialize($where);
		$serial_order = serialize($order_by);
		$serial_limit = serialize($limit);

		// Get the hub data and store locally
		$key = "hubdata_{$this->CI->bootstrap->site_id}_{$name}_{$serial_where}{$serial_order}{$serial_limit}";
		
		if ( ! $this->_result = $this->CI->cache->get($key))
		{
			$this->_result = $this->obj_by_hub($name)->get($this->details($name)->hub_id, $where, $order_by, $limit);
			$this->CI->cache->write($this->_result, $key);
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
	 * Drops a hub from the database, and related tables, if any
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @return	object	of class Hub
	 */
	public function drop($name)
	{
		$this->obj_by_hub($name)->drop($this->details($name)->hub_id);

		return $this;
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
	 * Gets all details for the specified hub
	 *
	 * @access	private
	 * @param	string	name of the hub
	 * @return	mixed	returns hub details, or false if not found
	 */
	private function details($name)
	{
		// Check if we have the data locally set, this is always faster
		if (isset($this->_hub_details[$name]))
		{
			return $this->_hub_details[$name];
		}

		// Look up the data from cache or DB
		else if ( ! $hub = $this->CI->cache->get("hubdetails_{$this->CI->bootstrap->site_id}_{$name}"))
		{
			$hub = $this->CI->db_s->get_where("hubs_{$this->CI->bootstrap->site_id}", array('hub_name' => $name))->row();
			$this->CI->cache->write($hub, "hubdetails_{$this->CI->bootstrap->site_id}_{$name}");
		}

		return $hub;
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a hub from the database, and related tables, if any
	 *
	 * @access	private
	 * @param	string	name of the hub
	 * @return	object	instance of the driver class
	 */
	private function obj_by_hub($name)
	{
		return $this->obj_by_driver($this->details($name)->hub_driver);
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