<?php

/**
 * Site configuration model
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Config_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches the site configuration
	 *
	 * @access	public
	 * @return	object	site configuration
	 */
	public function fetch_config()
	{
		$config = new stdClass();

		$config->status			= site_config('status');
		$config->login			= site_config('login');
		$config->registration	= site_config('registration');
		$config->captcha		= site_config('captcha');
		$config->stats			= site_config('stats');

		return $config;
	}

	// --------------------------------------------------------------------

	/**
	 * Saves site configuration
	 *
	 * @access	public
	 * @return	void
	 */
	public function save_config()
	{
		site_config('status',		$this->input->post('status'));
		site_config('login',		$this->input->post('login'));
		site_config('registration',	$this->input->post('registration'));
		site_config('captcha',		$this->input->post('captcha'));
		site_config('stats',		$this->input->post('stats'));

		// Clear the stats table if it was disabled
		if (site_config('stats') == DISABLED)
		{
			$this->db->empty_table("site_stats_{$this->site->site_id}");
		}

		$this->admin_log->add('site_conf_update');
	}

	// --------------------------------------------------------------------
}