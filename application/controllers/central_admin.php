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
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for central
		$this->load->library('menu');
		$this->lang->load('central_admin');
	}

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
			'page_menu'		=> $this->menu->generate('central', 'welcome'),
		);

		// Process the template
		$this->template->admin('central/welcome', $data);
	}

	/**
	* Site management screen
	*
	* @access	public
	*/
	public function sites()
	{
		// Set the template data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_sites_exp'),
			'page_menu'		=> $this->menu->generate('central', 'manage_sites'),
		);

		// Process the template
		$this->template->admin('central/sites', $data);
	}
}

?> 