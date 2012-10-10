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
 * @param	object	hub row
 * @return	string	parsed string
 */
function expr($text, $row = FALSE)
{
	$CI				=& get_instance();
	$config			= $CI->config->item('parser');
	$expressions	= $config['expr'];

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
				$value = expr($value, $row);
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


/* End of file expression_helper.php */
/* Location: ./application/libraries/expression_helper.php */
