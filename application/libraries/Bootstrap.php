<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Bootstrap Library for Moksha
 *
 * This class handles Moksha site startup
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Bootstrap {

	var $CI;

	/**
	 * Current context
	 *
	 * @access public
	 * @var string
	 */
	var $context;

	/**
	 * Current site ID
	 *
	 * @access public
	 * @var int
	 */	
	var $site_id;

	/**
	 * Current site slug identifier
	 *
	 * @access public
	 * @var string
	 */
	var $site_slug;

	/**
	 * Base url for the current site
	 *
	 * @access public
	 * @var string
	 */
	var $site_url;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();

		$this->setup_db();
		$this->check_site();
		$this->check_admin();
	}

	/**
	 * Sets up moksha database paths
	 *
	 * @access	private
	 */
	private function setup_db()
	{
		$this->CI->db_c = $this->CI->load->database('central', TRUE);
		$this->CI->db_s = $this->CI->load->database('sites', TRUE);
	}

	/**
	 * Initializes the site identification and ACL
	 *
	 * @access	private
	 */
	private function check_site()
	{
		// Determine current protocol
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

		// Now we fetch the site slug and ID
		$query = $this->CI->db_c->get_where('sites', array('site_url' => $this->site_url));

		// We expect exactly one entry for this site
		if ($query->num_rows() == 1)
		{
			$row = $query->row();

			$this->site_id = $row->site_id;
			$this->site_slug = $row->site_slug;
		}

		// Site wasn't found. We kill the session if we are not in central
		else if ($this->CI->router->fetch_directory() != 'central_admin/')
		{
			show_error($this->CI->lang->line('invalid_site'));
		}
	}

	/**
	 * Validates administration sessions
	 *
	 * @access	private
	 */
	private function check_admin()
	{
		$subdir = $this->CI->router->fetch_directory();

		if ($subdir == 'central_admin/' || $subdir == 'site_admin/')
		{
			// Determine the context of the current request
			$is_central = ($subdir == 'central_admin/');

			// Set the login page based on whether we are in central
			if ($is_central)
			{
				$this->context = '%central';
				$url = 'admin/central/login';
			}
			else
			{
				$this->context = $this->site_slug;
				$url = 'admin/login';
			}

			// Make sure the user is authed, else serve the login page
			if ($this->CI->session->userdata("authed_{$this->context}") !== TRUE)
			{
				redirect(base_url($url), 'refresh');
			}
		}
	}
}
// END Site class

/* End of file bootstrap.php */
/* Location: ./application/libraries/bootstrap.php */