<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Control Generator
 *
 * @package		Moksha
 * @category	Helpers
 * @author		Sayak Banerjee <sayakb@kde.org>
 */

// ------------------------------------------------------------------------

/**
 * Creates a parahraph control
 *
 * @access	public
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_paragraph($options, $data = FALSE)
{
	$content = expr($options->disp_src, $data, $options->format);
	return "<p class='{$options->classes}'>{$content}</p>";
}

// ------------------------------------------------------------------------

/**
 * Creates a heading (big) control
 *
 * @access	public
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_heading_big($options, $data = FALSE)
{
	$content = expr($options->disp_src, $data, $options->format);
	return "<h2 class='{$options->classes}'>{$content}</h2>";
}

// ------------------------------------------------------------------------

/**
 * Creates a heading (normal) control
 *
 * @access	public
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_heading_normal($options, $data = FALSE)
{
	$content = expr($options->disp_src, $data, $options->format);
	return "<h3 class='{$options->classes}'>{$content}</h3>";
}

// ------------------------------------------------------------------------

/**
 * Creates a heading (small) control
 *
 * @access	public
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_heading_small($options, $data = FALSE)
{
	$content = expr($options->disp_src, $data, $options->format);
	return "<h4 class='{$options->classes}'>{$content}</h4>";
}

// ------------------------------------------------------------------------


/* End of file control_helper.php */
/* Location: ./application/helpers/control_helper.php */