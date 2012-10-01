<?php

/**
 * Control management operations
 *
 * Allows you to create, edit and delete controls that can be added to Moksha pages
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Controls extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for controls management
		$this->lang->load('site_admin');
		$this->load->model('site_admin/controls_model');
		$this->session->enforce_login('admin/login');
	}

	// --------------------------------------------------------------------

	/**
	* Create a new control
	*
	* @access	public
	*/
	public function add()
	{
		$this->template->load('site_admin/welcome');
	}

	// --------------------------------------------------------------------
}

?> 
