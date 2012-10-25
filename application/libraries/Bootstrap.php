<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bootstrap Library for Moksha
 *
 * This class handles Moksha site startup
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Bootstrap {

	var $CI;

	// --------------------------------------------------------------------

	/**
	 * Current site ID and URL. This is 0 for central
	 *
	 * @access public
	 * @var int
	 */	
	var $site_id;
	var $site_url;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();

		$this->init();
		$this->check_installation();
		$this->check_site();
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize environment variables we need later
	 *
	 * @access	private
	 */
	private function init()
	{
		// Set the default timezone to GMT
		date_default_timezone_set('GMT');
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the Moksha installation
	 *
	 * @access	private
	 */
	private function check_installation()
	{
		$file_path = APPPATH.'config/database.php';

		if (file_exists($file_path = APPPATH.'config/database.php'))
		{
			include($file_path);

			if (isset($db) AND ! empty($db['default']['hostname']))
			{
				$this->CI->load->database();
				return;
			}
		}

		if ( ! in_install())
		{
			redirect(base_url('install'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Initializes the site identification and ACL
	 *
	 * @access	private
	 */
	private function check_site()
	{
		if ( ! in_install())
		{
			if (isset($_SERVER['HTTPS']) AND $_SERVER['HTTPS'] == 'on')
			{
				$protocol = "https://";
			}
			else
			{
				$protocol = "http://";
			}

			// 1. We strip off the protocol from the base URL
			// 2. We remove :80, if set explicitly
			// 3. We remove the trailing slash
			$this->site_url = str_replace($protocol, '', base_url());
			$this->site_url = str_replace(':80/', '/', $this->site_url);
			$this->site_url = rtrim($this->site_url, '/');

			// Obtain the site ID
			$query = $this->CI->db->get_where('central_sites', array('site_url' => $this->site_url));

			if ($query->num_rows() == 1)
			{
				$this->site_id = $query->row()->site_id;
			}
			else if ( ! in_central())
			{
				// Show error if site isn't found
				show_error($this->CI->lang->line('invalid_site'));
			}
		}
	}

	// --------------------------------------------------------------------
}
// END Bootstrap class

/* End of file Bootstrap.php */
/* Location: ./application/libraries/Bootstrap.php */