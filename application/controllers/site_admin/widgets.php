<?php

/**
 * Widget management operations
 *
 * Allows you to create, edit and delete widgets that can be added to Moksha pages
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Widgets extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		if ( ! check_roles(ROLE_ADMIN))
		{
			redirect('admin/login');
		}

		$this->lang->load('site_admin');
		$this->load->model('site_admin/widgets_model');
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
				'total_rows'	=> $this->widgets_model->count_widgets(),
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
				redirect(base_url('admin/widgets/manage'));
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('widget_add_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'		=> $this->lang->line('site_adm'),
			'page_desc'			=> $this->lang->line('manage_widgets_exp'),
			'editor_title'		=> $this->lang->line('add_widget'),
			'toolbox_items'		=> $this->widgets_model->fetch_controls(),
			'validations'		=> $this->widgets_model->fetch_validations(),
			'roles'				=> $this->widgets_model->fetch_roles(),
			'widget_items'		=> $this->widgets_model->populate_controls(),
			'hubs_list'			=> $this->widgets_model->populate_hubs(),
			'widget_name'		=> set_value('widget_name'),
			'widget_roles'		=> set_value('widget_roles'),
			'widget_key'		=> set_value('widget_key'),
			'widget_empty'		=> set_value('widget_empty'),
			'frame_box'			=> set_radio('widget_frameless', 0),
			'frame_none'		=> set_radio('widget_frameless', 1),
			'attached_hub'		=> set_value('attached_hub'),
			'data_filters'		=> set_value('data_filters'),
			'order_by'			=> set_value('order_by'),
			'max_records'		=> set_value('max_records'),
			'binding'			=> set_value('binding')
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
		$widget		= $this->widgets_model->fetch_widget($widget_id);
		$hub_data	= $widget->widget_data->hub;

		// Exempt widget name from unique validation
		$this->form_validation->unique_exempts = array('widget_name' => $widget->widget_name);

		// Process the request
		if ($this->form_validation->run('site_admin/widgets/add'))
		{
			if ($this->widgets_model->update_widget($widget_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('widget_updated'));
				redirect(base_url('admin/widgets/manage'));
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('widget_updated_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'		=> $this->lang->line('site_adm'),
			'page_desc'			=> $this->lang->line('manage_widgets_exp'),
			'editor_title'		=> $this->lang->line('edit_widget'),
			'toolbox_items'		=> $this->widgets_model->fetch_controls(),
			'validations'		=> $this->widgets_model->fetch_validations(),
			'roles'				=> $this->widgets_model->fetch_roles(),
			'widget_items'		=> $this->widgets_model->populate_controls($widget->widget_data->controls),
			'hubs_list'			=> $this->widgets_model->populate_hubs(),
			'widget_name'		=> set_value('widget_name', $widget->widget_name),
			'widget_roles'		=> set_value('widget_roles', $widget->widget_roles),
			'widget_key'		=> set_value('widget_key', $widget->widget_key),
			'widget_empty'		=> set_value('widget_empty', $widget->widget_empty),
			'frame_box'			=> set_radio('widget_frameless', 0, $widget->widget_frameless == 0),
			'frame_none'		=> set_radio('widget_frameless', 1, $widget->widget_frameless == 1),
			'attached_hub'		=> set_value('attached_hub', $hub_data->attached_hub),
			'data_filters'		=> set_value('data_filters', $hub_data->data_filters),
			'order_by'			=> set_value('order_by', $hub_data->order_by),
			'max_records'		=> set_value('max_records', $hub_data->max_records),
			'binding'			=> set_value('binding', $hub_data->binding)
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
			if ($this->widgets_model->delete_widget($widget_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('widget_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('widget_del_error'));
			}
		}

		redirect(base_url('admin/widgets/manage'));
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
		$hub_id		= $this->input->post('attached_hub');
		$hub_name	= $this->widgets_model->fetch_hub_name($hub_id);

		if (empty($path))
		{
			return TRUE;
		}

		if ($hub_name != HUB_NONE)
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
	public function check_hub($hub_id)
	{
		$hub_id		= $this->input->post('attached_hub');
		$hub_name	= $this->widgets_model->fetch_hub_name($hub_id);

		if ($hub_name != HUB_NONE)
		{
			$hub_list = $this->hub->fetch_list();

			if (is_array($hub_list))
			{
				foreach ($hub_list as $hub)
				{
					if ($hub->hub_id == $hub_id)
					{
						return TRUE;
					}
				}
			}

			$this->form_validation->set_message('check_hub', $this->lang->line('invalid_hub'));
			return FALSE;
		}

		return TRUE;
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
		$hub_id		= $this->input->post('attached_hub');
		$hub_name	= $this->widgets_model->fetch_hub_name($hub_id);

		if (empty($filters))
		{
			return TRUE;
		}

		if ($hub_name != HUB_NONE)
		{
			if ($this->hub->parse_filters($hub_name, $filters, TRUE) !== FALSE)
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
		$hub_id		= $this->input->post('attached_hub');
		$hub_name	= $this->widgets_model->fetch_hub_name($hub_id);

		if (empty($order_by))
		{
			return TRUE;
		}

		if ($hub_name != HUB_NONE)
		{
			if ($this->hub->parse_orderby($hub_name, $order_by) !== FALSE)
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
		$widget_config		= $this->config->item('widgets');
		$submit_button		= $widget_config['submit'];

		$control_keys		= $this->input->post('control_keys');
		$control_set_paths	= $this->input->post('control_set_paths');

		$hub_id				= $this->input->post('attached_hub');
		$hub_name			= $this->widgets_model->fetch_hub_name($hub_id);

		// Validate each control
		if (is_array($control_keys))
		{
			$all_controls = $this->widgets_model->fetch_controls();
			
			foreach ($control_keys as $key)
			{
				// Check if control is valid
				if ( ! isset($all_controls[$key]))
				{
					$this->form_validation->set_message('check_controls', $this->lang->line('control_invalid'));
					return FALSE;
				}

				// Submit button cannot be added to a read only hub
				if ($hub_name != HUB_NONE AND ! $this->hub->is_writable($hub_name) AND in_array($submit_button, $control_keys))
				{
					$this->form_validation->set_message('check_controls', $this->lang->line('submit_read_only'));
					return FALSE;
				}
			}
		}
		else
		{
			$this->form_validation->set_message('check_controls', $this->lang->line('control_required'));
			return FALSE;
		}

		// Validate the control set paths
		if ($hub_name != HUB_NONE AND is_array($control_set_paths))
		{
			$schema = $this->hub->schema($hub_name);
			$compat = $widget_config['compatibility'];

			// Unique identifier cannot be used as a set path
			foreach ($control_set_paths as $path)
			{
				if (isset($schema[$path]) AND $schema[$path] == DBTYPE_KEY)
				{
					$this->form_validation->set_message('check_controls', $this->lang->line('key_set_path'));
					return FALSE;
				}
			}

			// Check set path compatibility
			for ($idx = 0; $idx < count($control_keys); $idx++)
			{
				$key	= $control_keys[$idx];
				$path	= $control_set_paths[$idx];
				$type	= isset($schema[$path]) ? $schema[$path] : NULL;

				if (isset($compat[$key]) AND $type != NULL)
				{
					$compat_types = $compat[$key];

					if ( ! in_array($type, $compat_types))
					{
						$ctrl_name	= $this->lang->line("field_{$key}");
						$data_type	= $this->lang->line("dbtype_{$type}");
						$message	= sprintf($this->lang->line('column_incompatible'), $ctrl_name, $data_type);

						$this->form_validation->set_message('check_controls', $message);
						return FALSE;
					}
				}
			}
		}
		
		return TRUE;
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
			$valid_roles = $this->widgets_model->fetch_roles(TRUE);

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
