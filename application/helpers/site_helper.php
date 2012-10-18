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
	$CI =& get_instance();

	// Get the current site ID, if not passed
	if ($site_id === FALSE)
	{
		$site_id = $CI->bootstrap->site_id;
	}

	// Get the site configuration data
	if ( ! $config = $CI->cache->get("siteconfig_{$site_id}"))
	{
		$config = $CI->db->get("site_config_{$site_id}")->result();
		$CI->cache->write($config, "siteconfig_{$site_id}");
	}

	// If no value is passed, we are fetching the config data
	if ($value === FALSE)
	{
		foreach ($config as $item)
		{
			if ($item->config_key == $key)
			{
				return $item->config_value;
			}
		}
	}

	// We are setting a config value
	else
	{
		// Delete the cached site configuration data
		$CI->cache->delete("siteconfig_{$site_id}");

		// Prepare the date to save
		$config_data = array(
			'config_key'	=> $key,
			'config_value'	=> $value
		);

		// Update the configuration item value
		$CI->db->delete("site_config_{$site_id}", array('config_key' => $key));
		$CI->db->insert("site_config_{$site_id}", $config_data);

		return TRUE;
	}

	return FALSE;
}

// --------------------------------------------------------------------


/* End of file site_helper.php */
/* Location: ./application/helpers/site_helper.php */