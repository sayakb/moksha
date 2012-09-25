<?php

/**
 * Primary site controller
 *
 * Processes and generates the site output
 * 
 * @package		Moksha
 * @category	Site
 * @author		Moksha Team
 */
class Parser extends CI_Controller {

	/**
	* Entry point for the site parser
	*
	* @access	public
	*/
	public function index()
	{
		$schema = array(
			'one_col' => DBTYPE_KEY,
			'another_col' => DBTYPE_INT,
			'third_col' => DBTYPE_TEXT,
			'fourth_col' => DBTYPE_DATETIME,
		);

		$this->hub->create('myhub', HUB_DATABASE, $schema);
		
		die('site->main');
	}

	// --------------------------------------------------------------------
}

?> 