<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Authentication Helper
 *
 * @package		Moksha
 * @category	Helpers
 * @author		Moksha Team
 */

// ------------------------------------------------------------------------

/**
 * Generate a password hash
 *
 * @access	public
 * @param	string	Password for which has is to be generated
 * @return	string	Generated hash
 */
function password_hash($password)
{
	$CI =& get_instance();
	$key = $CI->config->item('encryption_key');

	return hash('SHA512', $password . $key);
}

/**
 * Gets the current context
 *
 * @access	public
 * @param	bool	Flag indicating whether we are in central
 * @return	string	Current context identifier
 */
function get_context($is_central)
{
	$CI =& get_instance();

	if ($is_central)
	{
		return '%central';
	}
	else
	{
		return $CI->bootstrap->site_slug;
	}
}

/**
 * Converts an auth URL sheme to a redirect route
 *
 * @access	public
 * @param	string	URL scheme to process
 */
function auth_redir($redirect)
{
	return base_url(str_replace('+', '/', $redirect));
}


/* End of file auth_helper.php */
/* Location: ./application/helpers/auth_helper.php */