<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Dynamic content generator
 *
 * This class creates widgets and controls dynamically for pages
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Dynamic {

	var $CI;

	// --------------------------------------------------------------------

	/**
	 * Page being rendered
	 * 
	 * @access public
	 * @var object
	 */	
	var $context;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI		=& get_instance();
		$this->context	= new stdClass();

		$this->context->submit_btns	= array();
		$this->context->ctrl_keys	= array();
	}

	// --------------------------------------------------------------------

	/**
	 * Generates the output for a page
	 *
	 * @access	public
	 */
	public function generate_page($page)
	{
		if (check_roles($page->page_roles))
		{
			$this->context->page = $page;

			// Process POST data for the page
			$this->process_post();

			// Determine the layout and parse each column data
			$widths	= explode('-', $page->page_layout);
			$index	= 0;
			$output	= '';

			foreach ($widths as $width)
			{
				$width	*= 4;
				$data	 = $this->generate_widgets($page->page_widgets[$index++]);

				$output	.= "<div class='span{$width}'>{$data}</div>";
			}

			// Save submit buttons to user data
			$this->CI->session->set_userdata($this->CI->bootstrap->session_key.'submit_btns', $this->context->submit_btns);

			return $output;
		}
		else
		{
			show_403();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Processes POST data for a page
	 *
	 * @access	private
	 * @return	void
	 */
	private function process_post()
	{
		// Load the control data from session
		$submit_btns = $this->CI->session->userdata($this->CI->bootstrap->session_key.'submit_btns');

		if (is_array($submit_btns))
		{
			foreach ($submit_btns as $widget_id => $btn)
			{
				// Check if data was POSTed
				if (isset($_POST[$btn]))
				{
					$this->handle_postdata($widget_id);
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Handles data entry for a widget
	 *
	 * @access	private
	 * @param	int		widget identifier
	 * @return	void
	 */
	private function handle_postdata($widget_id)
	{
		$widget = $this->CI->widget->get($widget_id);

		if ($widget !== FALSE AND $widget->widget_data->hub->attached_hub > 0)
		{
			// Fetch hub information
			$hub_name	= $this->CI->hub->fetch_name($config->attached_hub);
			$schema		= $this->CI->hub->schema($hub_name);
			$key_col	= array_search(DBTYPE_KEY, $schema);
			$widget_key	= expr($widget->widget_key);

			if ($key_col !== FALSE AND !empty($widget_key))
			{
				// Try to get the matching hub row
				$row = $this->CI->hub->get($hub_name, array($key_col => $widget_key))->row();

				if ($row !== FALSE)
				{
					$data = $this->prepare_for_save($widget_id, $widget->widget_data->controls, $row);

					if (is_array($data))
					{
						if ($count = $this->CI->hub->update($hub_name, $data))
						{
							return;
						}
					}
				}
			}

			$data = $this->prepare_for_save($widget_id, $widget->widget_data->controls);
			$this->CI->hub->insert($hub_name, $data);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Prepares a control for saving to the DB
	 *
	 * @access	private
	 * @param	int		widget identifier
	 * @param	array	controls to be parsed
	 * @param	array	hub schema
	 * @param	object	data context for the controls
	 * @return	mixed	data array if validation and 
	 */
	private function prepare_for_save($widget_id, $controls, $data = FALSE)
	{
		$index = 0;

		foreach ($controls as $control)
		{
			if ( ! empty($control->set_path))
			{
				$name	= 'control'.crc32($widget_id.$index++);
				$label	= $name;

				if ( ! empty($control->disp_src) AND substr($control->disp_src, 0, 1) != '-')
				{
					$label = expr($control->disp_src, $data);
				}

				// We always trim the data
				if (empty($control->validation))
				{
					$control->validation = 'trim';
				}
				else
				{
					$control->validation .= '|trim';
				}

				// Set form validation rules
				$this->CI->form_validation->set_rules($name, $label, $control->validation);

				// Prepare the data array
				$data[$control->set_path] = $this->input->post($name);
			}
		}

		if ($this->form_validation->run())
		{
			$this->context->success_msgs = $this->CI->lang->line('data_saved');
			return $data;
		}
		else
		{
			$this->context->success_msgs = validation_errors();
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generates widgets in a page
	 *
	 * @access	private
	 * @param	string	widget IDs to parse
	 * @return	string	rendered content
	 */
	private function generate_widgets($widget_ids)
	{
		// Load the widget configuration to context
		$this->context->config = $this->CI->config->item('widget');

		// Prepare data
		$widget_ids = explode('|', $widget_ids);
		$output = '';

		foreach ($widget_ids as $widget_id)
		{
			$key = "widgetdata_{$this->CI->bootstrap->site_id}_{$widget_id}_".user_data('user_name');

			if ( ! $widget_data = $this->CI->cache->get($key))
			{
				$widget			= $this->CI->widget->get($widget_id);
				$widget_data	= '';

				if ($widget !== FALSE)
				{
					// Check if we have a hub attached, if not - use default data
					if ($widget->widget_data->hub->attached_hub > 0)
					{
						$hub_data = $this->fetch_hub($widget->widget_data->hub);
					}
					else
					{
						$hub_data = array();
					}

					// Store the widget and returned row count in context
					$this->context->widget	= $widget;
					$this->context->rows	= count($hub_data);

					// If hard bound, repeat each control N times for N hub rows
					if (count($hub_data) > 0)
					{
						foreach($hub_data as $data)
						{
							if ($this->restrict_access($widget->widget_roles, $data))
							{
								$widget_data .= $this->generate_widget($widget, $data);
							}
						}
					}

					// For soft binding, produce at least 1 instance of the widget
					else if ($widget->widget_data->hub->binding != 'hard')
					{
						$widget_data = $this->generate_widget($widget);
					}
				}

				$this->CI->cache->write($widget_data, $key);
			}
			
			$output .= $widget_data;
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Renders a specific widget
	 *
	 * @access	private
	 * @param	object	widget to be rendered
	 * @param	array	hub row data context
	 * @param	string	rendered content
	 */
	private function generate_widget($widget, $data = FALSE)
	{
		$key		= strtolower(url_title($widget->widget_name));
		$controls	= $widget->widget_data->controls;
		$index		= 0;
		$output		= '';

		// Generate control data
		foreach ($controls as $control)
		{
			if ($this->restrict_access($control->roles, $data) AND function_exists("control_{$control->key}"))
			{
				$name	 = 'control'.crc32($widget->widget_id.$index++);
				$output	.= eval("return control_{$control->key}(\$name, \$control, \$data);");

				// Save control name to context
				if ($control->key == $this->context->config['submit'])
				{
					$this->context->submit_btns[$widget->widget_id] = $name;
				}
				else
				{
					$this->context->ctrl_keys[$widget->widget_id] = $name;
				}
			}
		}

		// Check if this is a notice widget
		// For a notice, we do not need a form or a frame
		if (count($controls) == 1 AND $controls[0] == $this->context->config['notice'])
		{
			return $output;
		}
		else
		{
			return form_open_multipart(current_url(), array('class' => "well widget-{$key}")).$output.form_close();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the output of a hub based on the widget's hub configuration
	 *
	 * @access	private
	 * @param	object	hub configruation data for the widget
	 * @return	array	hub data
	 */
	private function fetch_hub($config)
	{
		$hub_name		= $this->CI->hub->fetch_name($config->attached_hub);
		$data_filters	= $this->CI->hub->parse_filters($hub_name, $config->data_filters);
		$order_by		= $this->CI->hub->parse_orderby($hub_name, $config->order_by);
		$max_records	= $this->CI->hub->parse_limit($config->max_records);

		// Add the WHERE claus
		if ($data_filters !== FALSE)
		{
			$this->CI->hub->where($data_filters);
		}

		// Add ORDER BY claus
		if ($order_by !== FALSE)
		{
			$this->CI->hub->order_by($order_by);
		}

		// Add LIMIT
		if ($max_records !== FALSE)
		{
			$this->CI->hub->limit($max_records[0], $max_records[1]);
		}

		// Filter by where if set as such

		return $this->CI->hub->get($hub_name)->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Restricts access to a component based on the roles
	 *
	 * @access	private
	 * @param	string	component roles
	 * @param	object	associated hub data context
	 * @return	bool	if component is accessible
	 */
	private function restrict_access($roles, $data = FALSE)
	{
		$roles_ary		= explode('|', $roles);
		$author_filter	= TRUE;
		$author_key		= array_search(ROLE_AUTHOR, $roles_ary);

		// Admins have access to everything
		if (in_array(ROLE_ADMIN, user_data('user_roles')))
		{
			return TRUE;
		}

		// Extract the author role, if it's in there
		if ($author_key !== FALSE AND $data !== FALSE)
		{
			unset($roles_ary[$author_key]);
			$author_filter = user_data('user_name') == $data->_moksha_author;
		}

		return $author_filter AND check_roles($roles_ary);
	}

	// --------------------------------------------------------------------
}
// END Dynamic class

/* End of file Dynamic.php */
/* Location: ./application/libraries/dynamic/Dynamic.php */