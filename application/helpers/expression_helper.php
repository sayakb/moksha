<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha expression helper
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
 */

// --------------------------------------------------------------------
	
/**
 * Parses placeholders within a string
 *
 * @access	public
 * @param	string	text to be parsed
 * @param	object	current data context
 * @param	string	format expression for the string
 * @return	string	parsed string
 */
function expr($text, $data = FALSE, $format = FALSE)
{
	$CI				=& get_instance();
	$config			= $CI->config->item('expressions');
	$expressions	= $config['functions'];

	if (preg_match_all('/\{(.*?):(.*?)\}/i', $text, $matches) !== FALSE)
	{
		$originals	= $matches[0];
		$types		= $matches[1];
		$values		= $matches[2];

		for ($idx = 0; $idx < count($originals); $idx++)
		{
			$type	= $types[$idx];
			$value	= $values[$idx];

			// Check if value contains an expression. If so, parse it first
			if (preg_match('/\{(.*?):(.*?})\}/i', $value))
			{
				$value = expr($value, $data);
			}

			// Parse the expression
			if (isset($expressions[$type]))
			{
				$old = $originals[$idx];
				$new = eval("return {$expressions[$type]};");

				if ($format !== FALSE AND ! empty($format))
				{
					$new = @expr_format($new, $format);
				}

				$text = str_replace($old, $new, $text);
			}
		}
	}

	return $text;
}

// --------------------------------------------------------------------

/**
 * Format hub data before displaying
 *
 * @access	public
 * @param	string	text to be parsed
 * @param	object	current data context
 * @param	string	format expression for the string
 * @return	string	parsed string
 */
function expr_format($text, $format)
{
	$format = explode(':', $format);

	if (count($format) == 2)
	{
		switch($format[0])
		{
			case 'datetime':
			case 'date':
			case 'time':
				$text = date($format[1], strtotime($text));
				break;

			case 'string':
				$text = sprintf($format[1], $text);
				break;
		}
	}

	return $text;
}

// --------------------------------------------------------------------


/* End of file expression_helper.php */
/* Location: ./application/libraries/expression_helper.php */
