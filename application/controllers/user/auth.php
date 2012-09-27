<?php

/**
 * User authentication controller
 *
 * Process user login, logout and registration
 * 
 * @package		Moksha
 * @category	Authentication
 * @author		Moksha Team
 */
class Auth extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for auth controller
		$this->load->model('user/auth_model');
		$this->lang->load('auth');
		$this->load->library('form_validation');
	}

	// --------------------------------------------------------------------

	/**
	* Processes user login
	*
	* @access	public
	* @param	string	URL to be opened on successful auth
	*/
	public function login($redirect)
	{
		if ($this->form_validation->run('user/auth/login'))
		{
			if ($this->auth_model->validate_user())
			{
				$this->session->set_userdata($this->bootstrap->auth_id, TRUE);
				redirect(auth_redir($redirect), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('login_failed');
			}
		}

		// Assign template data
		$data = array(
			'page_title'		=> $this->lang->line('login'),
			'page_desc'			=> $this->lang->line('login_central'),
		);

		// Load the view
		$this->template->load('user/login', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Processes user logout
	 *
	 * @access	public
	 * @param	string	URL to be opened on successful auth
	 */
	public function logout($redirect)
	{
		$this->session->unset_userdata($this->bootstrap->auth_id);
		redirect(auth_redir($redirect), 'refresh');
	}

	// --------------------------------------------------------------------
}

?> 