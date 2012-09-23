<?php

/**
 * Central user management controller
 *
 * Handles user management actions for central
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Users extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for central
		$this->load->model('central_admin/users_model');
		$this->load->library('menu');
		$this->lang->load('central_admin');
	}

	/**
	 * Central admins management screen
	 *
	 * @access	public
	 * @param	int	Page number for the user list
	 */
	public function manage($page = 1)
	{
		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_users_exp'),
		);

		// Load the view
		$this->template->load('central_admin/users_manage', $data);
	}

	/**
	 * Add new central administrator
	 *
	 * @access	public
	 */
	public function add()
	{
		if ($this->form_validation->run('central_admin/users/add'))
		{
			if ($this->users_model->add_user())
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('user_added'));
				redirect(base_url('admin/central/users/manage'), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('user_add_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_users_exp'),
			'username'		=> set_value('username'),
			'email'			=> set_value('email'),
		);

		// Load the view
		$this->template->load('central_admin/users_editor', $data);
	}

	/**
	 * Update an existing user
	 *
	 * @access	public
	 */
	public function edit($user_id)
	{
		// Get user data
		$user = $this->users_model->get_user($user_id);

		// Set exempts for email and name fields
		$this->form_validation->unique_exempts = array(
			'user_name'		=> $user->user_name,
			'user_email'	=> $user->user_email
		);

		if ($this->form_validation->run('central_admin/users/edit'))
		{
			if ($this->users_model->update_user($user_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('user_updated'));
				redirect(base_url('admin/central/users/manage'), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('user_update_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_users_exp'),
			'username'		=> set_value('username', $user->user_name),
			'email'			=> set_value('email', $user->user_email),
		);

		// Load the view
		$this->template->load('central_admin/users_editor', $data);
	}
}

?>