<?php

/**
 * Role management operations
 *
 * Allows you to create, edit and delete user roles
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Roles extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->site->admin_only();
		$this->load->model('site_admin/roles_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Role management screen
	 *
	 * @access	public
	 */
	public function manage($page = 1)
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/roles/manage'),
				'total_rows'	=> $this->roles_model->count_roles(),
				'uri_segment'	=> 4
			))
		);
		
		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_roles_exp'),
			'roles'			=> $this->roles_model->fetch_roles($page),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('site_admin/roles_manage', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new user role to the site
	 *
	 * @access	public
	 */
	public function add()
	{
		if ($this->form_validation->run('site_admin/roles'))
		{
			if ($this->roles_model->add_role())
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('role_added'));
				redirect(base_url('admin/roles/manage'));
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('role_add_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_roles_exp'),
			'role_name'		=> set_value('role_name')
		);

		// Load the view
		$this->template->load('site_admin/roles_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Edit a user role
	 *
	 * @access	public
	 * @param	int		role id to edit
	 */
	public function edit($role_id)
	{
		// Get role data
		$role = $this->roles_model->fetch_role($role_id);

		// Set exempts for email and name fields
		$this->form_validation->unique_exempts = array('role_name' => $role->role_name);

		// Process the request
		if ($this->form_validation->run('site_admin/roles'))
		{
			if ($this->roles_model->update_role($role_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('role_updated'));
				redirect(base_url('admin/roles/manage'));
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('role_update_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_roles_exp'),
			'role_name'		=> set_value('role_name', $role->role_name)
		);

		// Load the view
		$this->template->load('site_admin/roles_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a role from the database
	 *
	 * @access	public
	 * @param	int		role id to delete
	 */
	public function delete($role_id)
	{
		if ($this->template->confirm_box('lang:role_del_confirm'))
		{
			if ($this->roles_model->delete_role($role_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('role_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('role_del_error'));
			}
		}

		redirect(base_url('admin/roles/manage'));
	}

	// --------------------------------------------------------------------
}

?> 
