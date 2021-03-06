<?php

/**
 * Widget management model
 *
 * Model for adding, editing and deleting widgets
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Widgets_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of widgets for the site
	 *
	 * @access	public
	 * @param	int		page number for the list
	 * @return	array	list of widgets
	 */
	public function fetch_widgets($page)
	{
		$config = $this->config->item('pagination');
		$offset = $config['per_page'] * ($page - 1);

		$query = $this->db->limit($config['per_page'], $offset)->get("site_widgets_{$this->site->site_id}");
		return $query->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a specific widget for a site
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @return	object	widget details
	 */
	public function fetch_widget($widget_id)
	{
		return $this->widget->get($widget_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches the name for a hub against its ID
	 *
	 * @access	public
	 * @param	int		hub identifier
	 * @return	string	hub name
	 */
	public function fetch_hub_name($hub_id)
	{
		return $this->hub->fetch_name($hub_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch different types of available controls
	 *
	 * @access	public
	 * @return	array	control list
	 */
	public function fetch_controls()
	{
		$config = $this->config->item('widgets');
		return $config['controls'];
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch different types of validations for controls
	 *
	 * @access	public
	 * @return	array	validation list
	 */
	public function fetch_validations()
	{
		$config = $this->config->item('widgets');
		return $config['validations'];
	}

	// --------------------------------------------------------------------

	/**
	 * Gets a list of role names
	 *
	 * @access	public
	 * @param	bool	indicates whether only IDs are to be returned
	 * @return	array	list of role names
	 */
	public function fetch_roles($ids_only = FALSE)
	{
		$roles = $this->db->get("site_roles_{$this->site->site_id}")->result();

		// Add author and logged-in roles
		$roles = array_merge(array(
			(object)array(
				'role_id'	=> ROLE_AUTHOR,
				'role_name'	=> $this->lang->line('author')
			),
			(object)array(
				'role_id'	=> ROLE_LOGGED_IN,
				'role_name'	=> $this->lang->line('logged_in')
			),
			(object)array(
				'role_id'	=> ROLE_ADMIN,
				'role_name'	=> $this->lang->line('administrator')
			)
		), $roles);

		if ($ids_only)
		{
			$role_ids = array();

			foreach ($roles as $role)
			{
				$role_ids[] = $role->role_id;
			}

			return $role_ids;
		}
		else
		{
			return $roles;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a count of widgets added to the site
	 *
	 * @access	public
	 * @return	int		count of widgets
	 */
	public function count_widgets()
	{
		return $this->widget->count();
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new widget to the DB
	 *
	 * @access	public
	 * @return	bool	true if succeeded
	 */
	public function add_widget()
	{
		$widget_name = $this->input->post('widget_name');

		$widget_options = array(
			'update_key'	=> $this->input->post('update_key'),
			'access_roles'	=> $this->input->post('access_roles'),
			'frameless'		=> $this->input->post('frameless') ? YES : NO,
			'password_path'	=> $this->input->post('password_path'),
			'empty_tpl'		=> $this->input->post('empty_tpl')
		);

		$widget_data = (object)array(
			'controls'	=> $this->populate_controls(),
			'hub'		=> (object)array(
				'attached_hub'	=> $this->input->post('attached_hub'),
				'data_filters'	=> $this->input->post('data_filters'),
				'order_by'		=> $this->input->post('order_by'),
				'max_records'	=> $this->input->post('max_records'),
				'binding'		=> $this->input->post('binding')
			)
		);

		$this->admin_log->add('widget_create', $widget_name);
		return $this->widget->create($widget_name, $widget_options, $widget_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Updated an existing widget
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @return	bool	true if succeeded
	 */
	public function update_widget($widget_id)
	{
		$widget_name = $this->input->post('widget_name');

		$widget_options = array(
			'update_key'	=> $this->input->post('update_key'),
			'access_roles'	=> $this->input->post('access_roles'),
			'frameless'		=> $this->input->post('frameless') ? YES : NO,
			'password_path'	=> $this->input->post('password_path'),
			'empty_tpl'		=> $this->input->post('empty_tpl')
		);

		$widget_data = (object)array(
			'controls'	=> $this->populate_controls(),
			'hub'		=> (object)array(
				'attached_hub'	=> $this->input->post('attached_hub'),
				'data_filters'	=> $this->input->post('data_filters'),
				'order_by'		=> $this->input->post('order_by'),
				'max_records'	=> $this->input->post('max_records'),
				'binding'		=> $this->input->post('binding')
			)
		);

		$this->admin_log->add('widget_modify', $widget_name);
		return $this->widget->modify($widget_id, $widget_name, $widget_options, $widget_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a specific widget
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @return	bool	true if succeeded
	 */
	public function delete_widget($widget_id)
	{
		$widget_name = $this->fetch_widget($widget_id)->widget_name;
		$this->admin_log->add('widget_delete', $widget_name);

		return $this->widget->delete($widget_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Populates POSTed data related to added controls
	 *
	 * @access	public
	 * @param	string	serialized widget data to populate as default
	 * @return	array	containing control data
	 */
	public function populate_controls($control_data = FALSE)
	{
		if (isset($_POST['submit']))
		{
			$controls = array();

			// Get POSTed controls
			$control_keys			= $this->input->post('control_keys');
			$control_classes		= $this->input->post('control_classes');
			$control_disp_srcs		= $this->input->post('control_disp_srcs');
			$control_get_paths		= $this->input->post('control_get_paths');
			$control_set_paths		= $this->input->post('control_set_paths');
			$control_groups			= $this->input->post('control_groups');
			$control_formats		= $this->input->post('control_formats');
			$control_validations	= $this->input->post('control_validations');
			$control_roles			= $this->input->post('control_roles');

			if (is_array($control_keys))
			{
				// Fetch icon and label metadata for posted controls
				$control_meta = elements($control_keys, $this->fetch_controls());

				// Populate classes, disp_paths, value_paths and formats data
				for ($idx = 0; $idx < count($control_keys); $idx++)
				{
					$control_key			= $control_keys[$idx];
					$control				= clone($control_meta[$control_key]);

					$control->key			= $control_key;
					$control->classes		= $control_classes[$idx];
					$control->disp_src		= $control_disp_srcs[$idx];
					$control->get_path		= $control_get_paths[$idx];
					$control->set_path		= $control_set_paths[$idx];
					$control->group			= $control_groups[$idx];
					$control->format		= $control_formats[$idx];
					$control->validations	= $control_validations[$idx];
					$control->roles			= $control_roles[$idx];

					$controls[] = $control;
				}

				return $controls;
			}
			else
			{
				return array();
			}
		}
		else if ($control_data !== FALSE)
		{
			return $control_data;
		}
		else
		{
			return array();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of hubs for the site
	 *
	 * @access	public
	 * @return	array	list of hubs
	 */
	public function populate_hubs()
	{
		$hubs		= $this->hub->fetch_list();
		$hubs_ary	= array('-1' => NULL);

		foreach ($hubs as $hub)
		{
			$hubs_ary[$hub->hub_id] = $hub->hub_name;
		}

		return $hubs_ary;
	}

	// --------------------------------------------------------------------
}