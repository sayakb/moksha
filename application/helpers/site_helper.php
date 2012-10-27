<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Site Operation Helper
 *
 * @package		Moksha
 * @category	Helpers
 * @author		Sayak Banerjee <sayakb@kde.org>
 */

// ------------------------------------------------------------------------

/**
 * Gets or sets site configuration
 *
 * @access	public
 * @param	string	configuration key to fetch
 * @param	mixed	configuration value to set
 * @param	int		optional site identifier
 * @return	mixed	config value if being queried, boolean if being set
 */
function site_config($key, $value = FALSE, $site_id = FALSE)
{
	global $site_config;

	$CI =& get_instance();

	// Get the current site ID, if not passed
	if ($site_id === FALSE)
	{
		if ($CI->site->site_id > 0)
		{
			$site_id = $CI->site->site_id;
		}
		else
		{
			return FALSE;
		}
	}

	// Get the site configuration data
	if ( ! isset($site_config) AND ! $site_config = $CI->cache->get("siteconfig_{$site_id}"))
	{
		$site_config = $CI->db->get("site_config_{$site_id}")->result();
		$CI->cache->write($site_config, "siteconfig_{$site_id}");
	}

	// If no value is passed, we are fetching the config data
	if ($value === FALSE)
	{
		foreach ($site_config as $item)
		{
			if ($item->key == $key)
			{
				return $item->value;
			}
		}
	}

	// We are setting a config value
	else
	{
		// Delete the local and cached site configuration data
		$CI->cache->delete("siteconfig_{$site_id}");
		$site_config = NULL;

		// Prepare the date to save
		$site_config_data = array(
			'key'	=> $key,
			'value'	=> $value
		);

		// Update the configuration item value
		$CI->db->delete("site_config_{$site_id}", array('key' => $key));
		$CI->db->insert("site_config_{$site_id}", $site_config_data);

		return TRUE;
	}

	return FALSE;
}

// --------------------------------------------------------------------

/**
 * Checks if we are in an admin panel
 *
 * @access	public
 * @return	bool	true if we are in admin
 */
function in_admin()
{
	$CI =& get_instance();

	$subdir	= $CI->router->fetch_directory();
	$sgmt	= $CI->uri->segment(1);

	return $subdir == 'central_admin/' OR $subdir == 'site_admin/' OR $sgmt == 'admin';
}

// --------------------------------------------------------------------

/**
 * Checks if we are in central admin
 *
 * @access	public
 * @return	bool	true if we are in central
 */
function in_central()
{
	$CI =& get_instance();

	$subdir	= $CI->router->fetch_directory();
	$sgmt1	= $CI->uri->segment(1);
	$sgmt2	= $CI->uri->segment(2);

	return $subdir == 'central_admin/' OR ($sgmt1 == 'admin' AND $sgmt2 == 'central');
}

// --------------------------------------------------------------------

/**
 * Checks if we are in install mode
 *
 * @access	public
 * @return	bool	true if we are in install mode
 */
function in_install()
{
	$CI =& get_instance();
	return $CI->uri->segment(1) == 'install';
}

// --------------------------------------------------------------------


/* End of file site_helper.php */
/* Location: ./application/helpers/site_helper.php */