<?php

/**
 * Page management operations
 *
 * Allows you to create, edit and delete site pages
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Pages extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for widgets management
		$this->lang->load('site_admin');
		$this->load->model('site_admin/pages_model');
		$this->session->enforce_admin('admin/login');
	}

	// --------------------------------------------------------------------

	/**
	 * Page management screen
	 *
	 * @access	public
	 */
	public function manage($page = 1)
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/pages/manage'),
				'total_rows'	=> $this->pages_model->count_pages(),
				'uri_segment'	=> 4,
			))
		);
		
		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_pages_exp'),
			'pages'			=> $this->pages_model->fetch_pages($page),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('site_admin/pages_manage', $data);
	}

	// --------------------------------------------------------------------
}

?> 
