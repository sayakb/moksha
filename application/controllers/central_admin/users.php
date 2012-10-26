<?php

/**
 * Central user management controller
 *
 * Handles user management actions for central
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Users extends CI_Controller {

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

		$this->load->model('central_admin/users_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Central admins management screen
	 *
	 * @access	public
	 * @param	int		page number for the user list
	 */
	public function manage($page = 1)
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/central/users/manage'),
				'total_rows'	=> $this->users_model->count_users()
			))
		);

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_users_exp'),
			'users'			=> $this->users_model->fetch_users($page),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('central_admin/users_manage', $data);
	}

	// --------------------------------------------------------------------

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
				redirect(base_url('admin/central/users/manage'));
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
			'email_address'	=> set_value('email_address'),
		);

		// Load the view
		$this->template->load('central_admin/users_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Update an existing user
	 *
	 * @access	public
	 * @param	int		user identifier
	 */
	public function edit($user_id)
	{
		// Get user data
		$user = $this->users_model->fetch_user($user_id);

		// Set exempts for email and name fields
		$this->form_validation->unique_exempts = array(
			'user_name'		=> $user->user_name,
			'email_address'	=> $user->email_address
		);

		// Process the request
		if ($this->form_validation->run('central_admin/users/edit'))
		{
			if ($this->users_model->update_user($user_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('user_updated'));
				redirect(base_url('admin/central/users/manage'));
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
			'email_address'	=> set_value('email_address', $user->email_address),
		);

		// Load the view
		$this->template->load('central_admin/users_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a central admin user
	 *
	 * @access	public
	 * @param	int		user id to delete
	 */
	public function delete($user_id)
	{
		// Founder cannot be deleted
		if ($this->users_model->check_founder($user_id))
		{
			$this->session->set_flashdata('error_msg', $this->lang->line('cannot_del_founder'));
		}

		// Process the request
		else if ($this->template->confirm_box('lang:user_del_confirm'))
		{
			if ($this->users_model->delete_user($user_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('user_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('user_del_error'));
			}
		}

		redirect(base_url('admin/central/users/manage'));
	}

	// --------------------------------------------------------------------
}

?>