<?php

/**
 * Site configuration controller
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Config extends CI_Controller {

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

		$this->load->model('site_admin/config_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Site configuration management screen
	 *
	 * @access	public
	 */
	public function index()
	{
		$config = $this->config_model->fetch_config();

		// Save the configuration
		if ($this->form_validation->run('site_admin/config'))
		{
			$this->config_model->save_config();
			
			$this->session->set_flashdata('success_msg', $this->lang->line('config_updated'));
			redirect(base_url('admin/config'));
		}

		// Assign view data
		$data = array(
			'page_title'			=> $this->lang->line('site_adm'),
			'page_desc'				=> $this->lang->line('site_config_exp'),

			'status_online'			=> set_radio('status',			ONLINE, $config->status == ONLINE),
			'login_enabled'			=> set_radio('login',			ENABLED, $config->login == ENABLED),
			'registration_enabled'	=> set_radio('registration',	ENABLED, $config->registration == ENABLED),
			'captcha_enabled'		=> set_radio('captcha',			ENABLED, $config->captcha == ENABLED),
			'stats_enabled'			=> set_radio('stats',			ENABLED, $config->stats == ENABLED),

			'status_offline'		=> set_radio('status',			OFFLINE, $config->status == OFFLINE),
			'login_disabled'		=> set_radio('login',			DISABLED, $config->login == DISABLED),
			'registration_disabled'	=> set_radio('registration',	DISABLED, $config->registration == DISABLED),
			'captcha_disabled'		=> set_radio('captcha',			DISABLED, $config->captcha == DISABLED),
			'stats_disabled'		=> set_radio('stats',			DISABLED, $config->stats == DISABLED)
		);

		// Load the view
		$this->template->load('site_admin/config', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Validates a flag value
	 *
	 * @access	public
	 * @param	string	value to validate
	 * @return	bool	true if valid
	 */
	public function check_flag($flag)
	{
		if ($flag == NULL OR ! in_array(intval($flag), array(ONLINE, OFFLINE, ENABLED, DISABLED)))
		{
			$this->form_validation->set_message('check_flag', $this->lang->line('invalid_flag'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------
}

?> 
