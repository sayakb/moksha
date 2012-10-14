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

			$this->context->submit_btns = array();
			$this->context->delete_btns = array();
			$this->context->delete_data = array();

			// Process POST data for the page
			$this->process_post();

			// Determine the layout and parse each column data
			$widths	= explode('-', $page->page_layout);
			$index	= 0;
			$output	= '';
			$empty	= TRUE;

			foreach ($widths as $width)
			{
				$data	 = $this->generate_widgets($page->page_widgets[$index++]);
				$empty	 = ($empty AND empty($data));
				$width	*= 4;

				$output	.= "<div class='span{$width}'>{$data}</div>";
			}

			// Save submit buttons to user data
			$buttons = array(
				'submit_btns' => $this->context->submit_btns,
				'delete_btns' => $this->context->delete_btns
			);

			$this->CI->session->set_userdata($this->CI->bootstrap->session_key.'btn_data', $buttons);

			if ($empty)
			{
				show_404();
			}
			else
			{
				return $output;
			}
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
		// Load the button data from session
		$btn_data		= $this->CI->session->userdata($this->CI->bootstrap->session_key.'btn_data');
		$submit_btns	= $btn_data['submit_btns'];
		$delete_btns	= $btn_data['delete_btns'];

		// Probe for form submission
		if (is_array($submit_btns))
		{
			foreach ($submit_btns as $key => $btn)
			{
				// Check if data was POSTed
				if (isset($_POST[$btn[0]]))
				{
					$key = explode('|', $key);
					$this->handle_postdata($btn[1], $key[0], $key[1]);

					break;
				}
			}
		}

		// Prove for item deletion
		if (is_array($delete_btns))
		{
			foreach ($delete_btns as $key => $btn)
			{
				// Check if data was POSTed
				if (isset($_POST[$btn]))
				{
					$key = explode('|', $key);
					$this->handle_deletion($key[0], $key[1]);

					break;
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Handles data entry for a widget
	 *
	 * @access	private
	 * @param	array	control names
	 * @param	int		widget identifier
	 * @param	int		unique identifier
	 * @return	void
	 */
	private function handle_postdata($control_names, $widget_id, $unique_id)
	{
		$widget = $this->CI->widget->get($widget_id);

		if ($widget !== FALSE AND $widget->widget_data->hub->attached_hub > 0)
		{
			// Fetch hub information
			$hub_name	= $this->CI->hub->fetch_name($widget->widget_data->hub->attached_hub);
			$schema		= $this->CI->hub->schema($hub_name);
			$key_col	= array_search(DBTYPE_KEY, $schema);
			$widget_key	= expr($widget->widget_key);

			// Update operation
			if ($key_col !== FALSE AND ! empty($widget_key))
			{
				// Try to get the matching hub row
				$row = $this->CI->hub->where($key_col, $widget_key)->get($hub_name)->row();

				if ($row !== FALSE)
				{
					$data = $this->prepare_for_save($widget, $control_names, $row);

					if (is_array($data))
					{
						if ($this->CI->hub->where($key_col, $widget_key)->update($hub_name, $data))
						{
							$this->finish_submit('success', $this->CI->lang->line('item_saved'));
						}
					}
				}
			}

			// Insert operation
			$data = $this->prepare_for_save($widget, $control_names);

			if (is_array($data))
			{
				$this->CI->hub->insert($hub_name, $data);
			}

			$this->finish_submit('success', $this->CI->lang->line('item_saved'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Handles deletion for a widget
	 *
	 * @access	private
	 * @param	int		widget identifier
	 * @param	object	data context
	 * @return	void
	 */
	private function handle_deletion($widget_id, $unique_id)
	{
		$widget = $this->CI->widget->get($widget_id);

		if ($widget !== FALSE)
		{
			if ($widget->widget_data->hub->attached_hub > 0)
			{
				// Fetch hub information
				$hub_name	= $this->CI->hub->fetch_name($widget->widget_data->hub->attached_hub);
				$schema		= $this->CI->hub->schema($hub_name);
				$key_col	= array_search(DBTYPE_KEY, $schema);
				$hub_row	= $this->CI->hub->where($key_col, $unique_id)->get($hub_name)->row();

				if ($hub_row !== FALSE AND $this->restrict_access($widget->widget_roles, $hub_row))
				{
					if ($this->CI->hub->where($key_col, $unique_id)->delete($hub_name))
					{
						$this->finish_submit('success', $this->CI->lang->line('item_deleted'));
					}
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Prepares a control for saving to the DB
	 *
	 * @access	private
	 * @param	object	widget containing the controls
	 * @param	array	controls names to be parsed
	 * @param	object	data context for the controls
	 * @return	mixed	data array if validation and 
	 */
	private function prepare_for_save($widget, $control_names, $data = FALSE)
	{
		if ($this->restrict_access($widget->widget_roles, $data))
		{
			// Process POST data for controls
			foreach ($widget->widget_data->controls as $key => $control)
			{
				if ( ! empty($control->set_path) AND isset($control_names[$key]))
				{
					$name	= $control_names[$key];
					$label	= $name;

					// Determine a readable name for the control
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

					if ($this->restrict_access($control->roles, $data))
					{
						// Set form validation rules
						$this->CI->form_validation->set_rules($name, $label, $control->validation);

						// Prepare the data array
						$ctrl_data[$control->set_path] = $this->CI->input->post($name);
					}
				}
			}

			// Validate the form
			if ($this->CI->form_validation->run())
			{
				return $ctrl_data;
			}
			else
			{
				$this->finish_submit('error', validation_errors());
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Completes submission activities
	 *
	 * @access	private
	 * @param	string	status of the submission
	 * @return	void
	 */
	private function finish_submit($status, $message)
	{
		// Set the notice message
		$this->CI->session->set_flashdata("{$this->CI->bootstrap->session_key}notice_{$status}", $message);

		// Redirect if set
		$url = "page_{$status}_url";

		if ( ! empty($this->context->page->$url))
		{
			redirect(expr($this->context->page->$url));
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
		$this->context->config = $this->CI->config->item('widgets');

		// Prepare data
		$widget_ids = explode('|', $widget_ids);
		$output = '';

		foreach ($widget_ids as $widget_id)
		{
			$widget			= $this->CI->widget->get($widget_id);
			$widget_key		= expr($widget->widget_key);
			$widget_data	= '';

			if ($widget !== FALSE)
			{
				// Check if we have a hub attached, if not - use default data
				if ($widget->widget_data->hub->attached_hub > 0)
				{
					$hub_name	= $this->CI->hub->fetch_name($widget->widget_data->hub->attached_hub);
					$schema		= $this->CI->hub->schema($hub_name);
					$key_col	= array_search(DBTYPE_KEY, $schema);

					$hub_data	= $this->fetch_hub($widget->widget_data->hub);
				}
				else
				{
					$hub_data	= array();
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
							$unique_id		 = isset($data->$key_col) ? $data->$key_col : FALSE;
							$widget_data	.= $this->generate_widget($widget, $data, $unique_id);
						}
					}
				}

				// For soft binding, produce at least 1 instance of the widget
				else if ($widget->widget_data->hub->binding != 'hard')
				{
					$widget_data = $this->generate_widget($widget);
				}
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
	 * @param	array	hub schema
	 * @param	int		row unique identifier
	 * @param	string	rendered content
	 */
	private function generate_widget($widget, $data = FALSE, $unique_id = FALSE)
	{
		$controls	= $widget->widget_data->controls;
		$names		= array();
		$index		= 0;
		$output		= '';

		// Generate control data
		foreach ($controls as $key => $control)
		{
			if ($this->restrict_access($control->roles, $data) AND function_exists("control_{$control->key}"))
			{
				$name = 'control'.crc32($widget->widget_id.$unique_id.$index++);
				$names[$key] = $name;

				if ($control->key == $this->context->config['submit'])
				{
					$this->context->submit_btns["{$widget->widget_id}|{$unique_id}"][] = $name;
				}

				if ($control->key == $this->context->config['delete'])
				{
					$this->context->delete_btns["{$widget->widget_id}|{$unique_id}"] = $name;
				}

				$output	.= eval("return control_{$control->key}(\$name, \$control, \$data);");
			}
		}

		// Save control names to the submit button metadata
		if (isset($this->context->submit_btns["{$widget->widget_id}|{$unique_id}"]))
		{
			$this->context->submit_btns["{$widget->widget_id}|{$unique_id}"][] = $names;
		}

		// Check if this is a notice widget
		// For a notice, we do not need a form or a frame
		if (count($controls) == 1 AND $controls[0] == $this->context->config['notice'])
		{
			return $output;
		}
		else
		{
			$name = strtolower(url_title($widget->widget_name));
			return form_open_multipart(current_url(), array('class' => "well widget-{$name}")).$output.form_close();
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

		return $this->CI->hub->get($hub_name, $data_filters, $order_by, $max_records)->result();
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