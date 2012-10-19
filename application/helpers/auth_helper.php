<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Authentication Helper
 *
 * @package		Moksha
 * @category	Helpers
 * @author		Sayak Banerjee <sayakb@kde.org>
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
	if (empty($redirect))
	{
		return base_url();
	}
	else
	{
		return base_url(str_replace('+', '/', $redirect));
	}
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

/**
 * Fetches user data from session
 *
 * @access	public
 * @param	string	session key
 * @return	mixed	user data
 */
function user_data($key)
{
	global $user_data;
	
	if ( ! isset($user_data))
	{
		$CI		=& get_instance();
		$user_data	= $CI->session->userdata($CI->bootstrap->session_key.'user');

		if ($user_data === FALSE)
		{
			$user_data = new stdClass();

			$user_data->user_id			= 0;
			$user_data->user_name		= 'anonymous';
			$user_data->password			= 'anonymous';
			$user_data->email_address	= 'anonymous';
			$user_data->roles			= array();
			$user_data->founder			= 0;
		}
		else
		{
			// Convert the roles into array so that it is readily usable
			if ( ! is_array($user_data->roles))
			{
				$user_data->roles = explode('|', $user_data->roles);
			}
		}
	}

	if (isset($user_data->$key))
	{
		return $user_data->$key;
	}
}

// --------------------------------------------------------------------

/**
 * Checks if the user has the specified roles
 *
 * @access	private
 * @param	mixed	array of roles or roles separated by |
 * @return	bool	true if user has all the roles
 */
function check_roles($roles)
{
	$roles = user_data('roles');

	if ( ! is_array($roles))
	{
		$roles = explode('|', $roles);
	}

	// Admins have access to everything
	if (in_array(ROLE_ADMIN, $roles))
	{
		return TRUE;
	}

	// Check for the specific role
	foreach ($roles as $role)
	{
		if ($role !== '' AND ! in_array($role, $roles))
		{
			return FALSE;
		}
	}

	return TRUE;
}

// --------------------------------------------------------------------


/* End of file auth_helper.php */
/* Location: ./application/helpers/auth_helper.php */