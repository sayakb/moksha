<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha URL Helper extension
 *
 * @package		Moksha
 * @category	Helpers
 * @author		Sayak Banerjee <sayakb@kde.org>
 */

// ------------------------------------------------------------------------

/**
 * Base URL
 * 
 * This is an extension of the core base_url() that checks whether we
 * already have a fully formed URL or not
 *
 * @access	public
 * @param	string	url to be appended
 * @return	string	base URL
 */
function base_url($uri = '')
{
	$CI =& get_instance();

	// Strip off admin/central if we are in central but the URL
	// does not contain any those segments. This is needed as we
	// support a dedicated URL for central
	if (in_central() AND $CI->uri->segment(1) != 'admin' AND $CI->uri->segment(2) != 'central')
	{
		$uri = substr($uri, 13);
	}

	// Do not format links if its already formatted
	if (substr($uri, 0, 4) != 'http')
	{
		$CI =& get_instance();
		return $CI->config->base_url($uri);
	}
	else
	{
		return $uri;
	}
}

// ------------------------------------------------------------------------

/**
 * Home page
 * 
 * This is a utility function to be used with expressions
 *
 * @access	public
 * @return	string	homepage URL
 */
function homepage()
{
	return base_url();
}

// ------------------------------------------------------------------------

/* End of file url_helper.php */
/* Location: ./system/helpers/url_helper.php */