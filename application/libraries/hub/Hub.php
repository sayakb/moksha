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
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
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
	 * @return	bool	true on successful creation
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
		$driver_path = APPPATH . "libraries/hub/drivers/{$driver}.php";

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