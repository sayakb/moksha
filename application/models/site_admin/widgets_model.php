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

		$query = $this->db->limit($config['per_page'], $offset)->get("site_widgets_{$this->bootstrap->site_id}");
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
		$this->db->where('widget_id', $widget_id);

		$widget = $this->db->get("site_widgets_{$this->bootstrap->site_id}")->row();
		$widget->widget_data = unserialize($widget->widget_data);

		return $widget;
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
		$hub = $this->db->get_where("site_hubs_{$this->bootstrap->site_id}", array('hub_id' => $hub_id))->row();
		return $hub->hub_name;
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
	 * Fetches a count of widgets added to the site
	 *
	 * @access	public
	 * @return	int		count of widgets
	 */
	public function count_widgets()
	{
		return $this->db->count_all("site_widgets_{$this->bootstrap->site_id}");
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
		// Gather widget meta data
		$widget_data = (object)array(
			'controls'	=> $this->populate_controls(),
			'hub'		=> (object)array(
				'attached_hub'	=> $this->input->post('attached_hub'),
				'data_filters'	=> $this->input->post('data_filters'),
				'order_by'		=> $this->input->post('order_by'),
				'max_records'	=> $this->input->post('max_records')
			)
		);

		// Build the insert data
		$data = array(
			'widget_name'	=> $this->input->post('widget_name'),
			'widget_roles'	=> $this->input->post('widget_roles'),
			'widget_key'	=> $this->input->post('widget_key'),
			'widget_data'	=> serialize($widget_data)
		);

		return $this->db->insert("site_widgets_{$this->bootstrap->site_id}", $data);
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
		// Gather widget meta data
		$widget_data = (object)array(
			'controls'	=> $this->populate_controls(),
			'hub'		=> (object)array(
				'attached_hub'	=> $this->input->post('attached_hub'),
				'data_filters'	=> $this->input->post('data_filters'),
				'order_by'		=> $this->input->post('order_by'),
				'max_records'	=> $this->input->post('max_records')
			)
		);

		// Build the update data
		$data = array(
			'widget_name'	=> $this->input->post('widget_name'),
			'widget_roles'	=> $this->input->post('widget_roles'),
			'widget_key'	=> $this->input->post('widget_key'),
			'widget_data'	=> serialize($widget_data)
		);

		return $this->db->update("site_widgets_{$this->bootstrap->site_id}", $data, array('widget_id' => $widget_id));
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
		return $this->db->delete("site_widgets_{$this->bootstrap->site_id}", array('widget_id' => $widget_id));
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
			$control_formats		= $this->input->post('control_formats');
			$control_validations	= $this->input->post('control_validations');
			$control_roles			= $this->input->post('control_roles');

			if (is_array($control_keys))
			{
				// Fetch icon and label metadata for posted controls
				$control_meta = elements($control_keys, $this->fetch_controls());
			
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
		$hubs_ary	= array(HUB_NONE => NULL);

		foreach ($hubs as $hub)
		{
			$hubs_ary[$hub->hub_id] = $hub->hub_name;
		}

		return $hubs_ary;
	}

	// --------------------------------------------------------------------
}