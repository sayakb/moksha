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
	 * Add a new widget to the DB
	 *
	 * @access	public
	 * @return	bool	true if succeeded
	 */
	public function add_widget()
	{
		$widget_name = $this->input->post('widget_name');
		$widget_data = array(
			'controls'	=> $this->populate_controls(),
			'hub'		=> array(
				'attached_hub'	=> $this->input->post('attached_hub'),
				'data_filters'	=> $this->input->post('data_filters'),
				'order_by'		=> $this->input->post('order_by'),
				'max_records'	=> $this->input->post('max_records')
			)
		);

		return $this->widget->add($widget_name, $widget_data);
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
		$widget_data = array(
			'controls'	=> $this->populate_controls(),
			'hub'		=> array(
				'attached_hub'	=> $this->input->post('attached_hub'),
				'data_filters'	=> $this->input->post('data_filters'),
				'order_by'		=> $this->input->post('order_by'),
				'max_records'	=> $this->input->post('max_records')
			)
		);

		return $this->widget->update($widget_id, $widget_name, $widget_data);
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
			$control_disp_paths		= $this->input->post('control_disp_paths');
			$control_value_paths	= $this->input->post('control_value_paths');
			$control_formats		= $this->input->post('control_formats');

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
					$controls[$idx]->disp_paths		= $control_disp_paths[$idx];
					$controls[$idx]->value_paths	= $control_value_paths[$idx];
					$controls[$idx]->formats		= $control_formats[$idx];
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