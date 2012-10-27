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
 * Checks if a user is logged in
 *
 * @access	public
 * @return	bool	true if logged in
 */
function is_logged_in()
{
	return user_data('user_name') != ANONYMOUS;
}

// --------------------------------------------------------------------

/**
 * Fetches user data from session
 *
 * @access	public
 * @param	string	session key
 * @return	mixed	user data, false if not found
 */
function user_data($key)
{
	global $user_data;
	
	if ( ! isset($user_data))
	{
		$CI =& get_instance();

		// Fetch the user data from session
		$user_data = $CI->session->userdata('user');

		// No data was found
		if ($user_data === FALSE)
		{
			$user_data = new stdClass();

			$user_data->user_id			= 0;
			$user_data->user_name		= ANONYMOUS;
			$user_data->password		= ANONYMOUS;
			$user_data->email_address	= ANONYMOUS;
			$user_data->roles			= array();
			$user_data->active			= ACTIVE;
			$user_data->founder			= NO;
		}
		else
		{
			if (in_central() AND ! isset($user_data->roles))
			{
				$user_data->roles = array(ROLE_ADMIN);
			}

			// Convert the roles into array so that it is readily usable
			if ( ! is_array($user_data->roles))
			{
				$user_data->roles = explode('|', $user_data->roles);
			}
		}
	}

	// Return the session ID directly, if asked for
	if ($key == 'session_id')
	{
		return $CI->session->userdata('session_id');
	}
	else if (isset($user_data->$key))
	{
		return $user_data->$key;
	}

	return FALSE;
}

// --------------------------------------------------------------------

/**
 * Updates session data for any user
 *
 * @access	public
 * @param	mixed	user data
 * @return	void
 */
function update_user_data($data)
{
	$CI =& get_instance();

	$search = new stdClass();
	$search->user_name = $data->user_name;

	// Find the session ID for this user
	$session_id = $CI->session->search_userdata('user', $search);

	if ($session_id !== FALSE)
	{
		$CI->session->update_userdata($session_id, 'user', $data);
	}
}

// --------------------------------------------------------------------

/**
 * Kills a user session
 *
 * @access	public
 * @param	string	user name
 * @return	void
 */
function kill_session($user_name)
{
	$CI =& get_instance();

	$search = new stdClass();
	$search->user_name = $user_name;

	// Find the session ID for this user
	$session_id = $CI->session->search_userdata('user', $search);

	if ($session_id !== FALSE)
	{
		$CI->session->flush_session($session_id);
	}
}

// --------------------------------------------------------------------

/**
 * Checks if the user has the specified roles
 *
 * @access	public
 * @param	mixed	array of roles or roles separated by |
 * @return	bool	true if user has all the roles
 */
function check_roles($roles)
{
	$user_roles = user_data('roles');

	if ( ! is_array($roles))
	{
		$roles = explode('|', $roles);
	}

	// Admins have access to everything
	if (in_array(ROLE_ADMIN, $user_roles))
	{
		return TRUE;
	}

	// Check for the specific role
	foreach ($roles as $role)
	{
		if ($role !== '' AND ! in_array($role, $user_roles))
		{
			return FALSE;
		}
	}

	return TRUE;
}

// --------------------------------------------------------------------


/* End of file auth_helper.php */
/* Location: ./application/helpers/auth_helper.php */