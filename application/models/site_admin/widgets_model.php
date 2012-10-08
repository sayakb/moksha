<?php

/**
 * Widget management model
 *
 * Model for adding, editing and deleting widgets
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
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

		$query = $this->db->limit($config['per_page'], $offset)->get("site_widgets_{$this->bootstrap->site_id}");
		return $query->result();
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
		$roles = $this->db->get("site_roles_{$this->bootstrap->site_id}")->result();

		// Add author and logged-in roles
		$roles = array_merge(array(
			(object)array(
				'role_id'	=> ROLE_AUTHOR,
				'role_name'	=> $this->lang->line('author')
			),
			(object)array(
				'role_id'	=> ROLE_LOGGED_IN,
				'role_name'	=> $this->lang->line('logged_in')
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
	 * Add a new widget to the DB
	 *
	 * @access	public
	 * @return	bool	true if succeeded
	 */
	public function add_widget()
	{
		$widget_name	= $this->input->post('widget_name');
		$widget_width	= $this->input->post('widget_width');
		$widget_roles	= $this->input->post('widget_roles');

		$widget_data	= (object)array(
			'controls'	=> $this->populate_controls(),
			'hub'		=> (object)array(
				'attached_hub'	=> $this->input->post('attached_hub'),
				'data_filters'	=> $this->input->post('data_filters'),
				'order_by'		=> $this->input->post('order_by'),
				'max_records'	=> $this->input->post('max_records')
			)
		);

		return $this->widget->add($widget_name, $widget_width, $widget_data);
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
		$widget_name	= $this->input->post('widget_name');
		$widget_width	= $this->input->post('widget_width');
		$widget_roles	= $this->input->post('widget_roles');

		$widget_data	= (object)array(
			'controls'	=> $this->populate_controls(),
			'hub'		=> (object)array(
				'attached_hub'	=> $this->input->post('attached_hub'),
				'data_filters'	=> $this->input->post('data_filters'),
				'order_by'		=> $this->input->post('order_by'),
				'max_records'	=> $this->input->post('max_records')
			)
		);

		return $this->widget->update($widget_id, $widget_name, $widget_width, $widget_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Populates POSTed data related to added controls
	 *
	 * @access	public
	 * @param	string	serialized widget data to populate as default
	 * @return	array	containing control data
	 */
	public function populate_controls($widget_data = FALSE)
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
			$control_formats		= $this->input->post('control_formats');
			$control_validations	= $this->input->post('control_validations');
			$control_roles			= $this->input->post('control_roles');

			if (is_array($control_keys))
			{
				// Fetch icon and label metadata for posted controls
				$control_meta = elements($control_keys, $this->widget->fetch_controls());
			
				// Populate classes, disp_paths, value_paths and formats data
				for($idx = 0; $idx < count($control_keys); $idx++)
				{
					$key		= $control_keys[$idx];
					$controls[] = $control_meta[$key];

					$controls[$idx]->key			= $key;
					$controls[$idx]->classes		= $control_classes[$idx];
					$controls[$idx]->disp_src		= $control_disp_srcs[$idx];
					$controls[$idx]->get_path		= $control_get_paths[$idx];
					$controls[$idx]->set_path		= $control_set_paths[$idx];
					$controls[$idx]->format			= $control_formats[$idx];
					$controls[$idx]->validations	= $control_validations[$idx];
					$controls[$idx]->roles			= $control_roles[$idx];
				}

				return $controls;
			}
			else
			{
				return array();
			}
		}
		else if ($widget_data !== FALSE)
		{
			return $widget_data;
		}
		else
		{
			return array();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of widget widths
	 *
	 * @access	public
	 * @return	array	list of widths
	 */
	public function populate_widths()
	{
		return array(
			'1' => '1',
			'2' => '2',
			'3' => '3'
		);
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
			$hubs_ary[$hub->hub_name] = $hub->hub_name;
		}

		return $hubs_ary;
	}

	// --------------------------------------------------------------------
}