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
