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
		echo var_dump($this->hub->get('mydot', array(
			'AND' => array(
				'title [LIKE]' => 'Randa'
			)
		))->result());
	}

	// --------------------------------------------------------------------
}

?> 