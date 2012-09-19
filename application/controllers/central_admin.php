<?php

/**
 * Central administration controller
 *
 * Processes all workflows related to central administration
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Central_admin extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for central
		$this->load->model('central_admin_model');
		$this->load->library('menu');
		$this->lang->load('central_admin');
	}

	/**
	 * Validates user session
	 *
	 * @access	private
	 */
	private function _validate()
	{
		if ($this->session->userdata('authed_%central') !== TRUE)
		{
			redirect(base_url('admin/central/login'), 'refresh');
		}
	}

	/**
	* Central admin index page
	*
	* @access	public
	*/
	public function index()
	{
		$this->_validate();

		// Set the template data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('welcome_central'),
			'page_menu'		=> $this->menu->generate('central', 'welcome'),
		);

		// Process the template
		$this->template->load('central_admin', 'welcome', $data);
	}

	/**
	* Site management screen
	*
	* @access	public
	*/
	public function sites($action = '', $meta = 0)
	{
		$this->load->library(array('form_validation', 'pagination'));
		$this->_validate();

		// Process based on action
		if ($action == 'manage')
		{
			if ($this->form_validation->run())
			{
				if ($this->central_admin_model->add_site())
				{
					$this->template->success_msgs = $this->lang->line('site_added');
				}
				else
				{
					$this->template->error_msgs = $this->lang->line('site_add_error');
				}
			}
		}
		else if ($action == 'delete')
		{
			if ($this->central_admin_model->delete_site($meta))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('site_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('site_del_error'));
			}

			redirect(base_url('admin/central/sites/manage'), 'refresh');
		}
		else
		{
			redirect(base_url('admin/central/sites/manage'), 'refresh');
		}

		// Initialize pagination
		$this->pagination->initialize(array(
			'base_url'			=> base_url('admin/central/sites/manage'),
			'total_rows'		=> $this->central_admin_model->count_sites(),
			'per_page'			=> $this->config->item('per_page'),
			'uri_segment'		=> 5,
			'use_page_numbers'	=> TRUE
		));

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_sites_exp'),
			'page_menu'		=> $this->menu->generate('central', 'manage_sites'),
			'sites'			=> $this->central_admin_model->get_sites($meta),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('central_admin', 'sites', $data);
	}
}

?> 