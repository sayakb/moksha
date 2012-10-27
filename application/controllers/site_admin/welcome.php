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

		$this->site->admin_only();
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
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('site_adm_welcome'),
			'site_info'		=> $this->welcome_model->fetch_site_info(),
			'site_stats'	=> $this->welcome_model->fetch_stats(),
			'months'		=> $this->welcome_model->fetch_months(),
			'years'			=> $this->welcome_model->fetch_years()
		);

		$this->template->load('site_admin/welcome', $data);
	}

	// --------------------------------------------------------------------

	/**
	* Statistics AJAX extension
	*
	* @access	public
	* @param	int		year for which stats is to be fetches
	*/
	public function stats($year)
	{
		echo $this->welcome_model->fetch_visitors($year);
	}

	// --------------------------------------------------------------------
}

?> 
