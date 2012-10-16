<?php

/**
 * Main site controller
 *
 * Processes and generates the site output
 * 
 * @package		Moksha
 * @category	Site
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Output extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('sites/output_model');
	}

	// --------------------------------------------------------------------

	/**
	* Entry point for the site parser
	*
	* @access	public
	*/
	public function index()
	{
		// Fetch the current page
		$page = $this->output_model->fetch_page();

		// Page was not found
		if ($page === FALSE)
		{
			show_404();
		}

		// Assign view data
		$data = array(
			'page_title'	=> $page->page_title,
			'page_content'	=> $this->dynamic->generate_page($page),
			'page_header'	=> $this->output_model->fetch_header(),
			'page_class'	=> 'page-'.strtolower(url_title($page->page_title))
		);
		
		// Load the view. We do not use the template library as we don't need the
		// typical header and footer
		$this->load->view('sites/output', $data);
	}

	// --------------------------------------------------------------------
}

?> 