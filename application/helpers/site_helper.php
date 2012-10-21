<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Site Configuration Helper
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
		if ($CI->bootstrap->site_id > 0)
		{
			$site_id = $CI->bootstrap->site_id;
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


/* End of file site_helper.php */
/* Location: ./application/helpers/site_helper.php */