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
	* Central admin index page
	*
	* @access	public
	*/
	public function index()
	{
		// Set the template data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('welcome_central'),
			'page_menu'		=> $this->menu->generate('central', 'welcome'),
		);

		// Process the template
		$this->template->admin('central', 'welcome', $data);
	}

	/**
	* Site management screen
	*
	* @access	public
	*/
	public function sites($page = 1, $delete_id = FALSE)
	{
		$this->load->library(array('form_validation', 'admin_pages'));

		// Set the template data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_sites_exp'),
			'page_menu'		=> $this->menu->generate('central', 'manage_sites'),
		);

		if ($delete_id === FALSE)
		{
			// Validation rules:
			//  - site_url is mandatory
			//  - site_url should not exceed 255 chars
			//  - site_url should be unique
			$this->form_validation->set_rules(
				'site_url',
				$this->lang->line('site_url'),
				'required|max_length[255]|is_unique[sites.site_url]'
			);

			// Check if validation failed
			if ($this->form_validation->run() !== FALSE)
			{
				if ($this->central_admin_model->add_site() !== FALSE)
				{
					$this->template->success_msgs = $this->lang->line('site_added');
				}
				else
				{
					$this->template->error_msgs = $this->lang->line('site_add_error');
				}
			}
		}
		else
		{
			if ($this->central_admin_model->delete_site($delete_id) !== FALSE)
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('site_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('site_del_error'));
			}

			redirect(base_url('admin/central/sites'), 'refresh');
		}

		// Generate pagination
		$data['pagination'] = $this->admin_pages->generate(
			base_url('admin/central/sites'),
			$page,
			$this->central_admin_model->count_sites()
		);

		// Load site list
		$data['sites'] = $this->central_admin_model->get_sites($page);

		// Load the view
		$this->template->admin('central', 'sites', $data);
	}
}

?> 