<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bootstrap Library for Moksha
 *
 * This class handles Moksha site startup
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Hub {

	var $CI;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
	}
	
	public function hub_exists($name)
	{
	}

	/**
	 * Creates a new hub for this site
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	string	type of hub (see constants.php)
	 * @param	mixed	metadata related to the hub
	 * @return	bool	true on successful creation
	 */
	public function create($name, $type, $data)
	{
		
	}
}
// END Hub class

/* End of file hub.php */
/* Location: ./application/libraries/hub.php */