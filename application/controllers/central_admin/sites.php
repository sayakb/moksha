<?php

/**
 * Central administration sites management controller
 *
 * Handles site management actions for central
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
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
		$this->load->library('menu');
		$this->lang->load('central_admin');
	}

	/**
	 * Site management screen
	 *
	 * @access	public
	 * @param	int	Page number for the site list
	 */
	public function manage($page = 1)
	{
		$this->load->library('pagination');

		if ($this->form_validation->run('central_admin/sites/manage'))
		{
			if ($this->sites_model->add_site())
			{
				$this->template->success_msgs = $this->lang->line('site_added');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('site_add_error');
			}
		}

		// Initialize pagination
		$this->pagination->initialize(array(
			'base_url'			=> base_url('admin/central/sites/manage'),
			'total_rows'		=> $this->sites_model->count_sites(),
			'per_page'			=> $this->config->item('per_page'),
			'uri_segment'		=> 5,
			'use_page_numbers'	=> TRUE
		));

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_sites_exp'),
			'sites'			=> $this->sites_model->get_sites($page),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('central_admin/sites', $data);
	}

	/**
	 * Delete a site
	 *
	 * Deletes a specific site and redirects to site management screen
	 *
	 * @access	public
	 * @param	int	Site ID to delete
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
}

?>