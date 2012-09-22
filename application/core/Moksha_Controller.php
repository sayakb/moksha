<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Moksha controller class
 *
 * This class object is the super class for all Moksha controllers
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Moksha_Controller extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Validates user session
	 *
	 * @access	private
	 */
	protected function _validate()
	{
		if ($this->session->userdata('authed_%central') !== TRUE)
		{
			redirect(base_url('admin/central/login'), 'refresh');
		}
	}
}
// END Controller class

/* End of file Moksha_Controller.php */
/* Location: ./application/core/Moksha_Controller.php */