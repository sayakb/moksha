<?php

/**
 * Moksha installer
 *
 * Auto-install Moksha on your server
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Install extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->lang->load('install');
		$this->load->model('system/install_model');		
	}

	// --------------------------------------------------------------------

	/**
	 * Moksha installer
	 *
	 * @access	public
	 */
	public function index()
	{
		// Check if Moksha is already installed
		$this->install_model->check_already_installed();

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('install'),
			'page_desc'		=> $this->lang->line('install_exp')
		);

		// Process the form
		if ($this->form_validation->run('system/install'))
		{
			// Install the Moksha system
			$this->install_model->install_moksha();

			// Create a central admin user
			$user_data = $this->install_model->create_central_admin();

			// Inject the user data to the template array
			$data = array_merge($data, $user_data);

			$this->template->load('system/install_end', $data);
		}
		else
		{
			$this->template->load('system/install_start', $data);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Checks if the database configuration is valid
	 *
	 * @access	public
	 * @param	string	form key
	 * @return	bool	true if valid
	 */
	public function check_config($key)
	{
		$file_path = APPPATH.'config/database.php';

		if ( ! is_really_writable($file_path))
		{
			$error = sprintf($this->lang->line('conf_not_writable'), $file_path);
			$this->form_validation->set_message('check_config', $error);

			return FALSE;
		}

		if ( ! function_exists('mysqli_connect'))
		{
			$this->form_validation->set_message('check_config', $this->lang->line('mysqli_unavailable'));
			return FALSE;
		}

		if ($this->install_model->check_connection() === FALSE)
		{
			$this->form_validation->set_message('check_config', $this->lang->line('invalid_db_config'));
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------
}

?> 
