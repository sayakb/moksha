<?php

/**
 * Central administration controller
 *
 * Processes all workflows related to central administration
 * 
 * @package Moksha
 * @category Administration
 * @author Moksha Team
 */
class Central_admin extends CI_Controller {

	/**
	* Central admin index page
	*
	* @access public
	*/
	public function index()
	{
		$this->template->admin('central/welcome');
	}
}

?> 