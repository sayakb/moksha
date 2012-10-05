<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Widget Library for Moksha
 *
 * This class handles actions related to site widgets
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Widget {

	var $CI;

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
	 * Fetch different types of available controls
	 *
	 * @access	public
	 * @return	array	control list
	 */
	public function fetch_controls()
	{
		return array(
			'paragraph' => (object)array(
				'icon'	=> 'paragraph',
				'label'	=> 'field_paragraph',
			),
			'heading_big' => (object)array(
				'icon'	=> 'heading',
				'label'	=> 'field_heading_big',
			),
			'heading_normal' => (object)array(
				'icon'	=> 'heading',
				'label'	=> 'field_heading_normal',
			),
			'heading_small' => (object)array(
				'icon'	=> 'heading',
				'label'	=> 'field_heading_small',
			),
			'hyperlink' => (object)array(
				'icon'	=> 'hyperlink',
				'label'	=> 'field_hyperlink',
			),
			'textbox' => (object)array(
				'icon'	=> 'textbox',
				'label'	=> 'field_textbox',
			),
			'password' => (object)array(
				'icon'	=> 'password',
				'label'	=> 'field_password',
			),
			'textarea' => (object)array(
				'icon'	=> 'textarea',
				'label'	=> 'field_textarea',
			),
			'wysiwyg' => (object)array(
				'icon'	=> 'wysiwyg',
				'label'	=> 'field_wysiwyg',
			),
			'codebox' => (object)array(
				'icon'	=> 'codebox',
				'label'	=> 'field_codebox',
			),
			'checkbox' => (object)array(
				'icon'	=> 'checkbox',
				'label'	=> 'field_checkbox',
			),
			'radio' => (object)array(
				'icon'	=> 'radio',
				'label'	=> 'field_radio',
			),
			'submit_button' => (object)array(
				'icon'	=> 'button',
				'label'	=> 'field_submit_button',
			),
			'reset_button' => (object)array(
				'icon'	=> 'button',
				'label'	=> 'field_reset_button',
			),
			'file' => (object)array(
				'icon'	=> 'file',
				'label'	=> 'field_file',
			),
			'hidden' => (object)array(
				'icon'	=> 'hidden',
				'label'	=> 'field_hidden',
			),
			'image' => (object)array(
				'icon'	=> 'image',
				'label'	=> 'field_image',
			),
			'select' => (object)array(
				'icon'	=> 'select',
				'label'	=> 'field_select',
			),
			'multiselect' => (object)array(
				'icon'	=> 'multiselect',
				'label'	=> 'field_multiselect',
			)
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Gets a list of equality operators in a filter
	 *
	 * @access	public
	 * @return	array	list of operators
	 */
	public function fetch_operators()
	{
		return array(
			'[EQ]'		=> '',
			'[NEQ]'		=> '!=',
			'[GRTR]'	=> '>',
			'[LESS]'	=> '<',
			'[GRTREQ]'	=> '>=',
			'[LESSEQ]'	=> '<=',
			'[LIKE]'	=> '[LIKE]'
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of widgets for the site
	 *
	 * @access	public
	 * @return	array	list of widgets
	 */
	public function fetch_all()
	{
		return $this->CI->db->get("site_widgets_{$this->CI->bootstrap->site_id}")->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a specific widget for a site
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @return	object	widget details
	 */
	public function fetch($widget_id)
	{
		$this->CI->db->where('widget_id', $widget_id);

		$widget = $this->CI->db->get("site_widgets_{$this->CI->bootstrap->site_id}")->row();
		$widget->widget_data = unserialize($widget->widget_data);

		return $widget;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a count of widgets added to the site
	 *
	 * @access	public
	 * @return	int		count of widgets
	 */
	public function count()
	{
		return $this->CI->db->count_all("site_widgets_{$this->CI->bootstrap->site_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new widget to the DB
	 *
	 * @access	public
	 * @param	string	widget name
	 * @param	array	widget metadata
	 * @return	bool	true if succeeded
	 */
	public function add($widget_name, $widget_data)
	{
		$data = array(
			'widget_name'	=> $widget_name,
			'widget_data'	=> serialize($widget_data)
		);

		return $this->CI->db->insert("site_widgets_{$this->CI->bootstrap->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Updated an existing widget
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @param	string	widget name
	 * @param	array	widget metadata
	 * @return	bool	true if succeeded
	 */
	public function update($widget_id, $widget_name, $widget_data)
	{
		$data = array(
			'widget_name'	=> $widget_name,
			'widget_data'	=> serialize($widget_data)
		);

		$this->CI->db->where('widget_id', $widget_id);
		return $this->CI->db->update("site_widgets_{$this->CI->bootstrap->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a specific widget
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @return	bool	true if succeeded
	 */
	public function delete($widget_id)
	{
		$this->CI->db->where('widget_id', $widget_id);
		return $this->CI->db->delete("site_widgets_{$this->CI->bootstrap->site_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Parses data filter for a widget
	 *
	 * @access	public
	 * @param	string	hub name that is being linked
	 * @param	string	data filter to be parsed
	 * @return	mixed	array if filter is parsed, false on error
	 */
	public function parse_filters($hub_name, $filters)
	{
		$hub_columns	= $this->CI->hub->column_list($hub_name);
		$operators		= $this->fetch_operators();
		$filters		= explode("\n", $filters);
		$first_filter	= TRUE;

		$parsed = array(
			'AND'	=> array(),
			'OR'	=> array()
		);

		if (is_array($filters))
		{
			foreach ($filters as $filter)
			{
				$filter = trim($filter);
				$condition = substr($filter, 0, 1);

				// First filter needs to start with AND
				if ($first_filter AND $condition == '&')
				{
					$first_filter = FALSE;
				}
				else
				{
					return FALSE;
				}

				// Determine the condition for this filter
				$condition = $condition == '&' ? 'AND' : 'OR';

				// Determine the operator
				foreach ($operators as $opkey => $opval)
				{
					$pos = strpos($filter, $opkey);

					if ($pos !== FALSE)
					{
						$offset = strlen($opkey);
						$operator = $opval;

						break;
					}
					else
					{
						return FALSE;
					}
				}

				// Determine the key and value for the parsed array
				$column = trim(substr($filter, 1, $pos - 1));

				if (in_array($column, $hub_columns))
				{
					$key = trim("{$column} {$operator}");
					$value = substr($filter, $offset);

					$parsed[$condition][$key] = $value;
				}
				else
				{
					return FALSE;
				}
			}
		}

		// Return the parsed data
		if (count($parsed['AND']) > 0)
		{
			return $parsed;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Parses the order-by value for a widget
	 *
	 * @access	public
	 * @param	string	hub name that is being linked
	 * @param	string	order-by value
	 * @return	mixed	array if filter is parsed, false on error
	 */
	public function parse_orderby($hub_name, $order_by)
	{
		$hub_columns	= $this->CI->hub->column_list($hub_name);
		$order_by		= explode("\n", $order_by);
		$parsed			= array();

		if (is_array($order_by))
		{
			foreach ($order_by as $column)
			{
				$column = trim($column);

				// Check if column name is valid
				if (in_array($column, $hub_columns))
				{
					$parsed[] = $column;
				}
				else
				{
					return FALSE;
				}
			}
		}

		// Return parsed data
		if (count($parsed) > 0)
		{
			return $parsed;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Parses placeholders within a string
	 *
	 * @access	public
	 * @param	string	text to be parsed
	 * @return	string	parsed string
	 */
	public function parse_expr($text)
	{
		// Implement this later
		return $text;
	}

	// --------------------------------------------------------------------
}
// END Widget class

/* End of file Widget.php */
/* Location: ./application/libraries/Widget.php */
