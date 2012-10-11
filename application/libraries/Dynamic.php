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
	var $page;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
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
			$this->page = $page;

			// Process POST data for the page
			$this->process_post();

			// Determine the layout
			$widths	= explode('-', $page->page_layout);
			$index	= 0;
			$output	= '';

			foreach ($widths as $width)
			{
				$width	*= 4;
				$data	 = $this->generate_widgets($page->page_widgets[$index++]);

				$output	.= "<div class='span{$width}'>{$data}</div>";
				
			}

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
		// Implement this
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
		$widget_ids = explode('|', $widget_ids);
		$output = '';

		foreach ($widget_ids as $widget_id)
		{
			$key = "widgetdata_{$this->CI->bootstrap->site_id}_{$this->page->page_id}_{$widget_id}_".user_data('user_name');

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

					// Based on hard/soft binding, repeat each control N times for N hub rows
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
		$key	= strtolower(url_title($widget->widget_name));
		$output	= '';

		foreach ($widget->widget_data->controls as $control)
		{
			if ($this->restrict_access($control->roles, $data) AND function_exists("control_{$control->key}"))
			{
				$output .= eval("return control_{$control->key}(\$control, \$data);");
			}
		}

		return "<div class='well widget-{$key}'>{$output}</div>";
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
	private function restrict_access($roles, $data)
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
		if ($author_key !== FALSE)
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