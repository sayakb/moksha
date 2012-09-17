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
 * Server protocol
 *
 * Returns theprotocol (http/https) for the current request
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('server_protocol'))
{
	function server_protocol($suffix = '')
	{
		if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')
		{
			return "https{$suffix}";
		}
		else
		{
			return "http{$suffix}";
		}
	}
}

/**
 * Current site
 *
 * Returns the current site being accessed
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('current_site'))
{
	function current_site()
	{
		// 1. We strip off the protocol from the base URL
		// 2. We remove :80, if set explicitly
		// 3. We remove the trailing slash
		$current_site = str_replace(server_protocol('://'), '', base_url());
		$current_site = str_replace(':80/', '/', $current_site);
		$current_site = rtrim($current_site, '/');

		return $current_site;
	}
}