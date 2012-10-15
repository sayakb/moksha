<?php

/**
 * User authentication controller
 *
 * Process user login, logout and registration
 * 
 * @package		Moksha
 * @category	Authentication
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Auth extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->lang->load('auth');
		$this->load->model('user/auth_model');
		$this->load->library('form_validation');
	}

	// --------------------------------------------------------------------

	/**
	* Processes user login
	*
	* @access	public
	* @param	string	URL to be opened on successful auth
	*/
	public function login($redirect = '')
	{
		if ($this->form_validation->run('user/auth/login'))
		{
			if ($this->auth_model->validate_user())
			{
				redirect(auth_redir($redirect));
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('login_failed');
			}
		}

		// Assign template data
		$data = array(
			'page_title'		=> $this->lang->line('moksha'),
			'page_desc'			=> $this->lang->line('login_desc'),
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
	public function logout($redirect = '')
	{
		$this->auth_model->clear_session();
		redirect(auth_redir($redirect));
	}

	// --------------------------------------------------------------------

	/**
	 * Processes user registration
	 *
	 * @access	public
	 */
	public function register($action = 'index')
	{
		if ($action == 'index')
		{
			if ($this->form_validation->run('user/auth/register'))
			{
				if ($this->auth_model->register_user())
				{
					$success_msg = sprintf($this->lang->line('register_success'), base_url('login'));
					$this->template->success_msgs = $success_msg;
				}
				else
				{
					$this->template->error_msgs = $this->lang->line('register_fail');
				}
			}

			// Assign template data
			$data = array(
				'page_title'	=> $this->lang->line('moksha'),
				'page_desc'		=> $this->lang->line('register_desc'),
				'username'		=> set_value('username'),
				'email'			=> set_value('email'),
				'captcha'		=> $this->auth_model->create_captcha()
			);

			// Load the view
			$this->template->load('user/register', $data);
		}
		else if ($action == 'captcha')
		{
			echo $this->auth_model->create_captcha();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the submitted captcha string
	 *
	 * @access	public
	 */
	public function check_captcha($captcha)
	{
		if (empty($captcha))
		{
			$this->form_validation->set_message('check_captcha', $this->lang->line('captcha_required'));
			return FALSE;
		}

		if ( ! $this->auth_model->validate_captcha())
		{
			$this->form_validation->set_message('check_captcha', $this->lang->line('captcha_wrong'));
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------
}

?> 