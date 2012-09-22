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

	/**
	* Processes user login
	*
	* @access	public
	* @param	string	URL to be opened on successful auth
	* @param	bool	Flag indicating whether we are logging in to central
	*/
	public function login($redirect, $is_central = FALSE)
	{
		// Check if we have a context and redirect set
		if ($this->form_validation->run('user/auth/login'))
		{
			$context = $this->_context($is_central);

			if ($this->auth_model->validate_user($context))
			{
				$this->session->set_userdata("authed_{$context}", TRUE);
				redirect($this->_redir($redirect), 'refresh');
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

	/**
	 * Processes user logout
	 *
	 * @access	public
	* @param	string	URL to be opened on successful auth
	* @param	bool	Flag indicating whether we are logging in to central
	 */
	public function logout($redirect, $is_central = FALSE)
	{
		$context = $this->_context($is_central);

		$this->session->unset_userdata("authed_{$context}");
		redirect($this->_redir($redirect), 'refresh');
	}

	/**
	 * Gets current authentication context
	 *
	 * @access	private
	 */
	private function _context($is_central)
	{
		if ($is_central)
		{
			return '%central';
		}
		else
		{
			// Implement slug logic here
		}
	}

	/**
	 * Converts a URL sheme to an actual URL
	 *
	 * @access	private
	 */
	private function _redir($redirect)
	{
		return base_url(str_replace('+', '/', $redirect));
	}
}

?> 