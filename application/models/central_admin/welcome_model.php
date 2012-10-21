<?php

/**
 * Central admin welcome page logic
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Welcome_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches basic central admin information
	 *
	 * @access	public
	 * @return	object	requested information
	 */
	public function fetch_central_info()
	{
		$info = new stdClass();

		$info->moksha_version	= $this->config->item('moksha_version');
		$info->php_version		= phpversion();

		return $info;
	}

	// --------------------------------------------------------------------
}