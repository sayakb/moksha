<?php

/**
 * Central administration homepage
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

		// Load stuff we need for central
		$this->lang->load('central_admin');
	}

	// --------------------------------------------------------------------

	/**
	* Central admin index page
	*
	* @access	public
	*/
	public function index()
	{
		// Set the template data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('welcome_central'),
		);

		// Process the template
		$this->template->load('central_admin/welcome', $data);
	}

	// --------------------------------------------------------------------
}

?>