<?php

/**
 * Central administration sites management controller
 *
 * Handles site management actions for central
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Sites extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for central
		$this->load->model('central_admin/sites_model');
		$this->lang->load('central_admin');
		$this->session->enforce_admin('admin/central/login');
	}

	// --------------------------------------------------------------------

	/**
	 * Site management screen
	 *
	 * @access	public
	 * @param	int		page number for the site list
	 */
	public function manage($page = 1)
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/central/sites/manage'),
				'total_rows'	=> $this->sites_model->count_sites()
			))
		);

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_sites_exp'),
			'sites'			=> $this->sites_model->fetch_sites($page),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('central_admin/sites_manage', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new Moksha site
	 *
	 * @access	public
	 */
	public function add()
	{
		if ($this->form_validation->run('central_admin/sites'))
		{
			if ($this->sites_model->add_site())
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('site_added'));
				redirect(base_url('admin/central/sites/manage'), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('site_add_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_sites_exp'),
			'site_url'		=> set_value('site_url')
		);

		// Load the view
		$this->template->load('central_admin/sites_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Update an existing site
	 *
	 * @access	public
	 * @param	int		site identifier
	 */
	public function edit($site_id)
	{
		// Get site data
		$site = $this->sites_model->fetch_site($site_id);

		// Set exempts for email and name fields
		$this->form_validation->unique_exempts = array('site_url' => $site->site_url);

		// Process the request
		if ($this->form_validation->run('central_admin/sites'))
		{
			if ($this->sites_model->update_site($site_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('site_updated'));
				redirect(base_url('admin/central/sites/manage'), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('site_update_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_sites_exp'),
			'site_url'		=> set_value('site_url', $site->site_url)
		);

		// Load the view
		$this->template->load('central_admin/sites_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a site
	 *
	 * Deletes a specific site and redirects to site management screen
	 *
	 * @access	public
	 * @param	int		site id to delete
	 */
	public function delete($site_id)
	{
		if ($this->template->confirm_box('lang:site_del_confirm'))
		{
			if ($this->sites_model->delete_site($site_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('site_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('site_del_error'));
			}
		}

		redirect(base_url('admin/central/sites/manage'), 'refresh');
	}

	// --------------------------------------------------------------------
}

?>