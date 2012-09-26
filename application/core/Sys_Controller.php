<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Application Controller Class extension
 *
 * This class object is the super class for all Moksha controllers
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha team
 */
class Sys_Controller extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Here, we load all globally needed drivers
		$this->load->driver('cache');
	}
}
// END Sys_Controller class

/* End of file Sys_Controller.php */
/* Location: ./application/core/Sys_Controller.php */