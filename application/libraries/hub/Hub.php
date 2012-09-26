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
		$this->_obj_by_driver($driver)->create($name, $data);

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
			$schema = $this->_obj_by_hub($name)->schema($this->_details($name)->hub_id);
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
	 * @param	array	where claus for data selection
	 * @param	array	order by claus
	 * @param	array	limit to be applied
	 * @return	object	of class Hub
	 */
	public function get($name, $where = FALSE, $order_by = FALSE, $limit = FALSE)
	{
		// Determine the cache key
		$serial_where = serialize($where);
		$serial_order = serialize($order_by);
		$serial_limit = serialize($limit);

		$key = "hubdata_{$this->CI->bootstrap->site_id}_{$name}_{$serial_where}{$serial_order}{$serial_limit}";
		
		if ( ! $this->_result = $this->CI->cache->get($key))
		{
			$this->_result = $this->_obj_by_hub($name)->get($this->_details($name)->hub_id, $where, $order_by, $limit);
			$this->CI->cache->write($this->_result, $key);
		}

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
		return $this->_result;
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
			return $this->_result[0];
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
		$this->_obj_by_hub($name)->drop($this->_details($name)->hub_id);

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
	private function _details($name)
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
	private function _obj_by_hub($name)
	{
		return $this->_obj_by_driver($this->_details($name)->hub_driver);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Gets an instance of a specific hub driver
	 *
	 * @access	private
	 * @param	string	driver to load
	 * @return	object	instance of the driver class
	 */
	private function _obj_by_driver($driver)
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