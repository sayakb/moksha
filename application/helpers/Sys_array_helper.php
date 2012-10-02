<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Array Helper extension
 *
 * @package		Moksha
 * @category	Helpers
 * @author		Moksha Team
 */

// ------------------------------------------------------------------------

/**
 * Check if an array has duplicate items
 *
 * @access	public
 * @param	array	array to validate
 * @return	mixed	check for this specific duplicate entry
 */
function array_has_duplicates($array, $value = FALSE)
{
	$dupe_array = array();

	foreach($array as $item)
	{
		if ( ! empty($item) AND isset($dupe_array[$item]))
		{
			if ($value === FALSE OR ($value !== FALSE AND $item == $value))
			{
				return TRUE;
			}
		}
		else
		{
			$dupe_array[$item] = TRUE;
		}
	}

	return FALSE;
}

// --------------------------------------------------------------------

/* End of file auth_helper.php */
/* Location: ./application/helpers/auth_helper.php */