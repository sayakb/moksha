<?php

/**
 * Site user management controller
 *
 * Handles user management actions for a site
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
		$this->load->model('site_admin/users_model');
		$this->lang->load('site_admin');
		$this->session->enforce_admin('admin/login');
	}

	// --------------------------------------------------------------------

	/**
	 * User management screen
	 *
	 * @access	public
	 * @param	int		page number for the user list
	 */
	public function manage($page = 1)
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/users/manage'),
				'total_rows'	=> $this->users_model->count_users(),
				'uri_segment'	=> 4
			))
		);

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_users_exp'),
			'users'			=> $this->users_model->fetch_users($page),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('site_admin/users_manage', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Add new user to a site
	 *
	 * @access	public
	 */
	public function add()
	{
		if ($this->form_validation->run('site_admin/users/add'))
		{
			if ($this->users_model->add_user())
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('user_added'));
				redirect(base_url('admin/users/manage'), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('user_add_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_users_exp'),
			'roles'			=> $this->users_model->fetch_roles(),
			'disable_adm'	=> NULL,
			'username'		=> set_value('username'),
			'email'			=> set_value('email'),
			'user_roles'	=> set_value('user_roles')
		);

		// Load the view
		$this->template->load('site_admin/users_editor', $data);
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
		// Get user and role data
		$user	= $this->users_model->fetch_user($user_id);

		// Set exempts for email and name fields
		$this->form_validation->unique_exempts = array(
			'user_name'		=> $user->user_name,
			'user_email'	=> $user->user_email
		);

		// Process the request
		if ($this->form_validation->run('site_admin/users/edit'))
		{
			if ($this->users_model->update_user($user_id, $user->user_founder == 1))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('user_updated'));
				redirect(base_url('admin/users/manage'), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('user_update_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_users_exp'),
			'roles'			=> $this->users_model->fetch_roles(),
			'disable_adm'	=> $user->user_founder == 1 ? 'disabled="disabled"' : '',
			'username'		=> set_value('username', $user->user_name),
			'email'			=> set_value('email', $user->user_email),
			'user_roles'	=> set_value('user_roles', $user->user_roles)
		);

		// Load the view
		$this->template->load('site_admin/users_editor', $data);
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

		redirect(base_url('admin/users/manage'), 'refresh');
	}

	// --------------------------------------------------------------------

	/**
	 * Validates submitted user roles
	 *
	 * @access	public
	 * @param	string	roles to validate
	 * @return	bool	true if valid
	 */
	public function check_roles($roles)
	{
		$roles = trim($roles);

		if (empty($role))
		{
			return TRUE;
		}

		$roles_ary = explode('|', $roles);

		if (is_array($roles_ary))
		{
			$valid_roles = $this->users_model->fetch_roles(TRUE);

			foreach ($roles_ary as $role)
			{
				if ( ! in_array($role, $valid_roles))
				{
					$this->form_validation->set_message('check_roles', $this->lang->line('invalid_role'));
					return FALSE;
				}
			}

			return TRUE;
		}

		$this->form_validation->set_message('check_roles', $this->lang->line('invalid_role'));
		return FALSE;
	}

	// --------------------------------------------------------------------
}

?>