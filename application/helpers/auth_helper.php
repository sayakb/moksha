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
 * @param	string	password for which has is to be generated
 * @return	string	generated hash
 */
function password_hash($password)
{
	$CI =& get_instance();
	$key = $CI->config->item('encryption_key');

	return hash('SHA512', $password.$key);
}

// --------------------------------------------------------------------

/**
 * Converts an auth URL sheme to a redirect route
 *
 * @access	public
 * @param	string	auth URL scheme to process
 * @return	string	generated URL
 */
function auth_redir($redirect)
{
	return base_url(str_replace('+', '/', $redirect));
}

// --------------------------------------------------------------------

/**
 * Checks if we are in central admin
 *
 * @access	public
 * @param	string	auth URL scheme to process
 * @return	bool	true if we are in central
 */
function in_central()
{
	$CI =& get_instance();
	return $CI->uri->segment(1) == 'admin' AND $CI->uri->segment(2) == 'central';
}

// --------------------------------------------------------------------

/* End of file auth_helper.php */
/* Location: ./application/helpers/auth_helper.php */