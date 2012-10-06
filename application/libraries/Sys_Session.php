<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Session Class extension
 *
 * @package		Moksha
 * @category	Library
 * @author		Moksha Team
 */
class Sys_Session extends CI_Session {

	/**
	 * Session Constructor
	 *
	 * The constructor sets the session table based on whether we are in central or not
	 */
	public function __construct($params = array())
	{
		$this->CI		=& get_instance();
		$cookie_name	= $this->CI->config->item('sess_cookie_name');
		$table_name		= $this->CI->config->item('sess_table_name');

		if (in_central())
		{
			$params['sess_cookie_name']	= "moksha_{$cookie_name}";
			$params['sess_table_name']	= "central_{$table_name}";
		}
		else
		{
			$key = crc32("{$cookie_name}{$this->CI->bootstrap->site_id}");

			$params['sess_cookie_name']	= "moksha_{$key}";
			$params['sess_table_name']	= "site_{$table_name}_{$this->CI->bootstrap->site_id}";
		}

		parent::__construct($params);
	}

	// --------------------------------------------------------------------

	/**
	 * Enforces user login - redirects if not logged in
	 *
	 * @access	public
	 * @param	string	url to redirect to if not logged in
	 */
	public function enforce_admin($login_url)
	{
		$roles = user_data('user_roles');

		if ($roles !== FALSE)
		{
			if (in_array(ROLE_ADMIN, $roles))
			{
				return;
			}
		}

		redirect(base_url($login_url), 'refresh');
	}

	// --------------------------------------------------------------------
}
// END Sys_Session Class

/* End of file Sys_Session.php */
/* Location: ./application/libraries/Sys_Session.php */