<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Form Helper extension
 *
 * @package		Moksha
 * @category	Helpers
 * @author		Sayak Banerjee <sayakb@kde.org>
 */

// ------------------------------------------------------------------------

/**
 * Control group generator
 * 
 * @access	public
 * @param	string	control label
 * @param	string	markup for the controls
 * @return	string	control group
 */
function form_group($label, $controls)
{
	return	"<div class='control-group'>".
				"<label class='control-label'>{$label}</label>".
				"<div class='controls'>{$controls}</div>".
			"</div>";
}

// ------------------------------------------------------------------------


/* End of file Sys_form_helper.php */
/* Location: ./application/helpers/Sys_form_helper.php */
