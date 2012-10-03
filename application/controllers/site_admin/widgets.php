<?php

/**
 * Widget management operations
 *
 * Allows you to create, edit and delete widgets that can be added to Moksha pages
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Widgets extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for widgets management
		$this->lang->load('site_admin');
		$this->load->model('site_admin/widgets_model');
		$this->session->enforce_login('admin/login');
	}

	// --------------------------------------------------------------------

	/**
	 * Widget management screen
	 *
	 * @access	public
	 */
	public function manage()
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/hubs/manage'),
				'total_rows'	=> $this->widget->count(),
				'uri_segment'	=> 4,
			))
		);
		
		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_widgets_exp'),
			'widgets'		=> $this->widget->fetch_all(),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('site_admin/widgets_manage', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Create a new widget
	 *
	 * @access	public
	 */
	public function add()
	{
		if ($this->form_validation->run('site_admin/widgets/add'))
		{
			if ($this->widgets_model->add_widget())
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('widget_added'));
				redirect(base_url('admin/widgets/manage'), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('widget_add_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_widgets_exp'),
			'editor_title'	=> $this->lang->line('add_widget'),
			'widget_items'	=> $this->widgets_model->populate_controls(),
			'widget_name'	=> set_value('widget_name'),
			'toolbox_items'	=> $this->widget->fetch_controls()
		);

		// Load the view
		$this->template->load('site_admin/widgets_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Edit an existing widget
	 *
	 * @access	public
	 */
	public function edit($widget_id)
	{
		// Fetch widget data
		$widget = $this->widget->fetch($widget_id);
		
		// Exempt widget name from unique validation
		$this->form_validation->unique_exempts = array('widget_name' => $widget->widget_name);

		// Process the request
		if ($this->form_validation->run('site_admin/widgets/add'))
		{
			if ($this->widgets_model->update_widget($widget_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('widget_updated'));
				redirect(base_url('admin/widgets/manage'), 'refresh');
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('widget_updated_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_widgets_exp'),
			'editor_title'	=> $this->lang->line('edit_widget'),
			'widget_name'	=> set_value('widget_name', $widget->widget_name),
			'widget_items'	=> $this->widgets_model->populate_controls($widget->widget_data),
			'toolbox_items'	=> $this->widget->fetch_controls()
		);

		// Load the view
		$this->template->load('site_admin/widgets_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a widget from the database
	 *
	 * @access	public
	 * @param	int		widget id to delete
	 */
	public function delete($widget_id)
	{
		if ($this->template->confirm_box('lang:widget_del_confirm'))
		{
			if ($this->widget->delete($widget_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('widget_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('widget_del_error'));
			}
		}

		redirect(base_url('admin/widgets/manage'), 'refresh');
	}

	// --------------------------------------------------------------------

	/**
	 * Validates each added control by verifying the corresponding key
	 *
	 * @access	public
	 * @return	bool	true if valid
	 */
	public function check_controls()
	{
		$control_keys = $this->input->post('control_keys');

		if (is_array($control_keys))
		{
			$all_controls = $this->widget->fetch_controls();
			
			foreach ($control_keys as $key)
			{
				if ( ! isset($all_controls[$key]))
				{
					$this->form_validation->set_message('check_controls', $this->lang->line('control_invalid'));
					return FALSE;
				}
			}
			
			return TRUE;
		}

		$this->form_validation->set_message('check_controls', $this->lang->line('control_required'));
		return FALSE;
	}
}

?> 
