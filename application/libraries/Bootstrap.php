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

	// --------------------------------------------------------------------

	/**
	 * Current site ID
	 *
	 * @access public
	 * @var int
	 */	
	var $site_id;

	// --------------------------------------------------------------------

	/**
	 * Base url for the current site
	 *
	 * @access public
	 * @var string
	 */
	var $site_url;

	// --------------------------------------------------------------------

	/**
	 * Flag indicating whether we are in admin interface
	 *
	 * @access public
	 * @var bool
	 */
	var $in_admin;

	// --------------------------------------------------------------------

	/**
	 * Flag indicating whether we are in central
	 *
	 * @access public
	 * @var bool
	 */
	var $in_central;

	// --------------------------------------------------------------------

	/**
	 * Identifier required for authentication
	 *
	 * @access public
	 * @var string
	 */
	var $auth_id;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();

		$this->init();
		$this->setup_db();
		$this->check_site();
		$this->check_admin();
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
	 * Sets up moksha database paths
	 *
	 * @access	private
	 */
	private function setup_db()
	{
		$this->CI->db_c = $this->CI->load->database('central', TRUE);
		$this->CI->db_s = $this->CI->load->database('sites', TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Initializes the site identification and ACL
	 *
	 * @access	private
	 */
	private function check_site()
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

		$this->in_central = $this->CI->uri->segment(1) == 'admin' AND $this->CI->uri->segment(2) == 'central';
		$query = $this->CI->db_c->get_where('sites', array('site_url' => $this->site_url));

		if ($query->num_rows() == 1)
		{
			$this->site_id = $query->row()->site_id;
		}
		else if ( ! $this->in_central)
		{
			// Show error if site isn't found
			show_error($this->CI->lang->line('invalid_site'));
		}

		$this->auth_id = 'authed_'.($this->in_central ? '0' : $this->site_id);
		$this->CI->db = $this->in_central ? $this->CI->db_c : $this->CI->db_s;
	}

	// --------------------------------------------------------------------

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
			$this->in_admin = true;

			// Make sure the user is authed, else serve the login page
			if ($this->CI->session->userdata($this->auth_id) !== TRUE)
			{
				$login_url = $this->in_central ? 'admin/central/login' : 'admin/login';

				redirect(base_url($login_url), 'refresh');
			}
		}
	}

	// --------------------------------------------------------------------
}
// END Bootstrap class

/* End of file Bootstrap.php */
/* Location: ./application/libraries/Bootstrap.php */