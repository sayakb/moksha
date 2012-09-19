<?php

/**
 * Site administration controller
 *
 * Processes all workflows related to site administration
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Site_admin extends CI_Controller {

	/**
	* Site admin index page
	*
	* @access	public
	*/
	public function index()
	{
		$this->template->load('site_admin', 'welcome');
	}
}

?> 
