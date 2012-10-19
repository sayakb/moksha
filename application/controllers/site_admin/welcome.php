<?php

/**
 * Site administration homepage
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Welcome extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		if ( ! check_roles(ROLE_ADMIN))
		{
			redirect('admin/login');
		}

		$this->lang->load('site_admin');
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
