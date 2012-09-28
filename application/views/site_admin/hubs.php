<?php

/**
 * Site administration hub management controller
 *
 * Handles hub management actions for each site
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Hubs extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for central
		$this->load->model('site_admin/hubs_model');
		$this->lang->load('site_admin');
		$this->session->enforce_login('admin/login');
	}

	// --------------------------------------------------------------------

	/**
	 * Hub management screen
	 *
	 * @access	public
	 * @param	int		page number for the site list
	 */
	public function manage($page = 1)
	{
		

		// Load the view
		$this->template->load('site_admin/hubs_manage', $data);
	}

	// --------------------------------------------------------------------
}

?>