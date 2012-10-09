<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Widget Library for Moksha
 *
 * This class handles actions related to site widgets
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
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
		$operators		= $this->operators();
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
	 * @param	object	hub row
	 * @return	string	parsed string
	 */
	public function parse_expr($text, $row = FALSE)
	{
		$config			= $this->CI->config->item('widgets');
		$expressions	= $config['expressions'];

		if ($row === FALSE OR gettype($row) == 'object')
		{
			$row = new stdClass();
		}

		if (preg_match_all('/\{(.*?):(.*)\}/i', $text, $matches) !== FALSE)
		{
			$originals	= $matches[0];
			$types		= $matches[1];
			$values		= $matches[2];

			for ($idx = 0; $idx < count($originals); $idx++)
			{
				$type	= $types[$idx];
				$value	= $values[$idx];
				
				// Check if value contains an expression. If so, parse it first
				if (preg_match('/\{(.*?):(.*)\}/i', $value))
				{
					$value = $this->parse_expr($value, $row);
				}

				// Parse the expression
				if (isset($expressions[$type]))
				{
					$old = $originals[$idx];
					$new = @eval("return {$expressions[$type]};");

					$text = str_replace($old, $new, $text);
				}
			}
		}

		return $text;
	}

	// --------------------------------------------------------------------

	/**
	 * Gets a list of equality operators in a filter
	 *
	 * @access	private
	 * @return	array	list of operators
	 */
	private function operators()
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
}
// END Widget class

/* End of file Widget.php */
/* Location: ./application/libraries/Widget.php */
