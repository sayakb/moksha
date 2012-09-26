<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Hub driver parent class
 *
 * Defines the structure for the hub driver classes
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Hub_driver {

	var $CI;

	// --------------------------------------------------------------------
	
	/**
	 * Array for holding the hub result
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
	 * __get
	 *
	 * Allows access to the result array in an object oriented fashion
	 *
	 * @access	private
	 * @param	string	Name of the param
	 */
	function __get($key)
	{
		if (isset($this->_result[$key]))
		{
			return $this->_result[$key];
		}
		else
		{
			return NULL;
		}
	}

	// --------------------------------------------------------------------
}
// END Hub_driver class

/* End of file Hub_driver.php */
/* Location: ./application/libraries/hub/Hub_driver.php */