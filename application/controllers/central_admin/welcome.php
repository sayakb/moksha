<?php

/**
 * Central administration homepage
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Welcome extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		if ( ! check_roles(ROLE_ADMIN))
		{
			redirect('admin/central/login');
		}

		$this->lang->load('central_admin');
		$this->load->model('central_admin/welcome_model');
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
			'page_desc'		=> $this->lang->line('central_adm_exp'),
			'conf_writable'	=> is_really_writable(APPPATH.'config/database.php'),
			'central_info'	=> $this->welcome_model->fetch_central_info()
		);

		// Process the template
		$this->template->load('central_admin/welcome', $data);
	}

	// --------------------------------------------------------------------
}

?>