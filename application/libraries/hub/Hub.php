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
	 * Gets all details for the specified hub
	 *
	 * @access	public
	 * @param	int		unique identifier for the hub
	 * @return	mixed	returns hub details, or false if not found
	 */
	public function fetch_hub($hub_id)
	{
		if ( ! $hub =  $this->CI->cache->get("tbl_hubs_{$this->CI->bootstrap->site_id}_{$hub_id}"))
		{
			$hub = $this->CI->db_s->get_where("hubs_{$this->CI->bootstrap->site_id}", array('hub_id' => $hub_id))->row();

			if ($hub->hub_driver == HUB_DATABASE)
			{
				$hub->schema = $this->CI->db_s->field_data("hub_{$this->CI->bootstrap->site_id}_{$hub->hub_id}");
			}
			
			$this->CI->cache->save("tbl_hubs_{$this->CI->bootstrap->site_id}_{$hub_id}", $hub);
		}

		return $hub;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a new hub for this site
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	string	hub driver name (see constants.php)
	 * @param	mixed	metadata related to the hub
	 * @return	void
	 */
	public function create($name, $driver, $data = FALSE)
	{
		$this->_obj_by_driver($driver)->create($name, $data);
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
		$this->_obj_by_hub($hub_id)->delete($hub_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a hub from the database, and related tables, if any
	 *
	 * @access	private
	 * @param	int		hub unique identifier
	 * @return	object	instance of the driver class
	 */
	private function _obj_by_hub($hub_id)
	{
		return $this->_obj_by_driver($this->fetch_hub($hub_id)->hub_driver);
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
			include_once(APPPATH.'libraries/hub/Hub_driver.php');
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