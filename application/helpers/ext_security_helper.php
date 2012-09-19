<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha URL Helper extension
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
 * @param	password Password for which has is to be generated
 * @return	Generated hash
 */
function password_hash($password)
{
	$CI =& get_instance();
	$key = $CI->config->item('encryption_key');

	return hash('SHA512', $password . $key);
}


/* End of file ext_security_helper.php */
/* Location: ./application/helpers/ext_security_helper.php */