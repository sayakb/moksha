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
	 * Currently active hub data
	 *
	 * @var	array
	 */
	var $_context = array();

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
	 * Loads a hub and sets the current driver
	 *
	 * @access	public
	 * @param	string	hub name
	 * @return	object	of class Hub
	 */
	public function load($hub)
	{
		$this->CI->db_s->where('hub_name', $name);
		$this->CI->db_s->where('site_id', $this->CI->bootstrap->site_id);

		$_context = $this->CI->db_s->get("hubs_{$this->CI->bootstrap->site_id}")->row_array();

		return $this;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Check if a hub exists in the DB for the current site
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @return	bool	true if the hub exists
	 */
	public function hub_exists($name)
	{
		$this->CI->db_s->where('hub_name', $name);
		$this->CI->db_s->where('site_id', $this->CI->bootstrap->site_id);
		
		return $this->CI->count_all_results("hubs_{$this->CI->bootstrap->site_id}") === 1;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a new hub for this site
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	string	hub driver name (see constants.php)
	 * @param	mixed	metadata related to the hub
	 * @return	object	of class Hub_driver
	 */
	public function create($name, $driver, $data = FALSE)
	{
		$this->_get_instance($driver)->create($name, $data);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Gets an instance of a specific hub driver
	 *
	 * @access	private
	 * @param	string	driver to load
	 * @return	object	instance of the driver class
	 */
	private function _get_instance($driver)
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

	/**
	 * __call
	 *
	 * Relays the driver object so that it's directly accessible
	 *
	 * @access	private
	 * @param	string	method name
	 * @param	mixed	method arguments
	 */
	function __call($name, $args)
	{
		// Check if a method exists locally
		if (method_exists($this, $name))
		{
			return $this->$name($args);
		}

		// Method doesn't exist, check if we have a hub loaded
		// If we do, we relay the driver's methods
		else if (isset($this->_context['hub_id']))
		{
			$driver = $this->_get_instance($this->_context['hub_driver']);

			if (method_exists($driver, $name))
			{
				return $driver->$name($args);
			}
		}

		// Invalid method, die die die!!
		throw new Exception("Call to undefined method: {$name}");
	}

	// --------------------------------------------------------------------
}
// END Hub class

/* End of file Hub.php */
/* Location: ./application/libraries/Hub/Hub.php */