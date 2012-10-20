<?php

/**
 * Site administration homepage
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
			redirect('admin/login');
		}

		$this->lang->load('site_admin');
		$this->load->model('site_admin/welcome_model');
	}

	// --------------------------------------------------------------------

	/**
	* Site admin index page
	*
	* @access	public
	*/
	public function index()
	{
		// Assign view data
		$data = array(
			'page_title'			=> $this->lang->line('site_adm'),
			'page_desc'				=> $this->lang->line('site_adm_welcome'),
			'site_info'				=> $this->welcome_model->fetch_site_info()
		);

		$this->template->load('site_admin/welcome', $data);
	}

	// --------------------------------------------------------------------
}

?> 
