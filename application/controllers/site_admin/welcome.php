<?php

/**
 * Site administration homepage
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Welcome extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for site admin
		$this->lang->load('site_admin');
		$this->session->enforce_login('admin/login');
	}

	// --------------------------------------------------------------------

	/**
	* Site admin index page
	*
	* @access	public
	*/
	public function index()
	{
		$this->template->load('site_admin/welcome');
	}

	// --------------------------------------------------------------------
}

?> 
