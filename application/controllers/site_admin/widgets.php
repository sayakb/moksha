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
		$this->session->enforce_admin('admin/login');
	}

	// --------------------------------------------------------------------

	/**
	 * Widget management screen
	 *
	 * @access	public
	 */
	public function manage($page = 1)
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/widgets/manage'),
				'total_rows'	=> $this->widget->count(),
				'uri_segment'	=> 4,
			))
		);
		
		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_widgets_exp'),
			'widgets'		=> $this->widgets_model->fetch_widgets($page),
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
			'toolbox_items'	=> $this->widget->fetch_controls(),
			'validations'	=> $this->widget->fetch_validations(),
			'widget_items'	=> $this->widgets_model->populate_controls(),
			'widget_widths'	=> $this->widgets_model->populate_widths(),
			'hubs_list'		=> $this->widgets_model->populate_hubs(),
			'widget_name'	=> set_value('widget_name'),
			'widget_width'	=> set_value('widget_width'),
			'attached_hub'	=> set_value('attached_hub'),
			'data_filters'	=> set_value('data_filters'),
			'order_by'		=> set_value('order_by'),
			'max_records'	=> set_value('max_records')
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
		$widget		= $this->widget->fetch($widget_id);
		$hub_data	= $widget->widget_data['hub'];
		
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
			'widget_items'	=> $this->widgets_model->populate_controls($widget->widget_data['controls']),
			'widget_widths'	=> $this->widgets_model->populate_widths(),
			'hubs_list'		=> $this->widgets_model->populate_hubs(),
			'toolbox_items'	=> $this->widget->fetch_controls(),
			'validations'	=> $this->widget->fetch_validations(),
			'widget_name'	=> set_value('widget_name', $widget->widget_name),
			'widget_width'	=> set_value('widget_width', $widget->widget_width),
			'attached_hub'	=> set_value('attached_hub', $hub_data['attached_hub']),
			'data_filters'	=> set_value('data_filters', $hub_data['data_filters']),
			'order_by'		=> set_value('order_by', $hub_data['order_by']),
			'max_records'	=> set_value('max_records', $hub_data['max_records'])
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
	 * Validates set value paths of the controls
	 *
	 * @access	public
	 * @param	array	value paths
	 * @return	bool	true if valid
	 */
	public function check_set_paths($path)
	{
		$path		= trim($path);
		$hub_name	= $this->input->post('attached_hub');

		if (empty($path))
		{
			return TRUE;
		}

		if ($hub_name != '-1')
		{
			$columns = $this->hub->column_list($hub_name);

			if (in_array($path, $columns))
			{
				return TRUE;
			}
		}

		$this->form_validation->set_message('check_set_paths', $this->lang->line('invalid_set_path'));
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates attached hub name
	 *
	 * @access	public
	 * @param	string	hub name
	 * @return	bool	true if valid
	 */
	public function check_hub($hub_name)
	{
		if ($hub_name != '-1')
		{
			$hub_list = $this->hub->fetch_list();

			if (is_array($hub_list))
			{
				foreach ($hub_list as $hub)
				{
					if ($hub->hub_name == $hub_name)
					{
						return TRUE;
					}
				}
			}
		}

		$this->form_validation->set_message('check_hub', $this->lang->line('invalid_hub'));
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the data filter
	 *
	 * @access	public
	 * @param	string	filter value
	 * @return	bool	true if valid
	 */
	public function check_filters($filters)
	{
		$filters	= trim($filters);
		$hub_name	= $this->input->post('attached_hub');

		if (empty($filters))
		{
			return TRUE;
		}

		if ($hub_name != '-1')
		{
			if ($this->widget->parse_filters($hub_name, $filters) !== FALSE)
			{
				return TRUE;
			}
		}
		
		$this->form_validation->set_message('check_filters', $this->lang->line('invalid_filter'));
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the order-by value
	 *
	 * @access	public
	 * @param	string	filter value
	 * @return	bool	true if valid
	 */
	public function check_orderby($order_by)
	{
		$order_by	= trim($order_by);
		$hub_name	= $this->input->post('attached_hub');

		if (empty($order_by))
		{
			return TRUE;
		}

		if ($hub_name != '-1')
		{
			if ($this->widget->parse_orderby($hub_name, $order_by) !== FALSE)
			{
				return TRUE;
			}
		}

		$this->form_validation->set_message('check_orderby', $this->lang->line('invalid_orderby'));
		return FALSE;
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

	// --------------------------------------------------------------------
}

?> 
