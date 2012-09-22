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

/* End of file auth_helper.php */
/* Location: ./application/helpers/auth_helper.php */