<?php

/**
 * Control management operations
 *
 * Allows you to create, edit and delete controls that can be added to Moksha pages
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Controls extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for controls management
		$this->lang->load('site_admin');
		$this->load->model('site_admin/controls_model');
		$this->session->enforce_login('admin/login');
	}

	// --------------------------------------------------------------------

	/**
	 * Control management screen
	 *
	 * @access	public
	 */
	public function manage()
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/hubs/manage'),
				'total_rows'	=> $this->control->count(),
				'uri_segment'	=> 4,
			))
		);
		
		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_controls_exp'),
			'controls'		=> $this->control->fetch_all(),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('site_admin/controls_manage', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Create a new control
	 *
	 * @access	public
	 */
	public function add()
	{
		if ($this->form_validation->run('site_admin/controls/add'))
		{
			if ($this->controls_model->add_control())
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('control_added'));
				redirect(base_url('admin/controls/manage'), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('control_add_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_controls_exp'),
			'editor_title'	=> $this->lang->line('add_control'),
			'toolbox_items'	=> $this->control->fetch_types(),
			'control_name'	=> set_value('control_name'),
			'controls'		=> set_value('controls')
		);

		// Load the view
		$this->template->load('site_admin/controls_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Edit an existing control
	 *
	 * @access	public
	 */
	public function edit($control_id)
	{
		// Fetch control data
		$control = $this->control->fetch($control_id);

		if ($this->form_validation->run('site_admin/controls/add'))
		{
			if ($this->controls_model->update_control($control_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('control_updated'));
				redirect(base_url('admin/controls/manage'), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('control_updated_error');
			}
		}

		// Prepare the control array
		$controls_ary = unserialize($control->control_elements);

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_controls_exp'),
			'editor_title'	=> $this->lang->line('edit_control'),
			'toolbox_items'	=> $this->control->fetch_types(),
			'control_name'	=> set_value('control_name', $control->control_name),
			'controls'		=> set_value('controls', implode('|', $controls_ary))
		);

		// Load the view
		$this->template->load('site_admin/controls_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a control from the database
	 *
	 * @access	public
	 * @param	int		control id to delete
	 */
	public function delete($control_id)
	{
		if ($this->template->confirm_box('lang:control_del_confirm'))
		{
			if ($this->control->delete($control_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('control_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('control_del_error'));
			}
		}

		redirect(base_url('admin/controls/manage'), 'refresh');
	}

	// --------------------------------------------------------------------

	/**
	 * Validate added controls
	 *
	 * @access	public
	 * @return	bool	true if valid
	 */
	public function check_controls()
	{
		$controls = $this->input->post('controls');

		if ( ! empty($controls))
		{
			$controls_ary	= explode('|', $controls);
			$all_controls	= $this->control->fetch_types();

			$submit_count	= count($controls_ary);
			$valid_count	= count(elements($controls_ary, $all_controls));

			if ($submit_count == $valid_count)
			{
				return TRUE;
			}
			else
			{
				$this->form_validation->set_message('check_controls', $this->lang->line('control_invalid'));
				return FALSE;
			}
		}

		$this->form_validation->set_message('check_controls', $this->lang->line('control_required'));
		return FALSE;
	}
}

?> 
