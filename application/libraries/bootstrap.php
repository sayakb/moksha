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
	var $site_id;
	var $site_slug;
	var $site_url;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();

		$this->setup_db();
		$this->check_site();
	}

	/**
	 * Sets up moksha database paths
	 *
	 * @access	public
	 */
	public function setup_db()
	{
		$this->CI->db_c = $this->CI->load->database('central', TRUE);
		$this->CI->db_s = $this->CI->load->database('sites', TRUE);
	}

	/**
	 * Initializes the site identification and ACL
	 *
	 * @access	public
	 */
	public function check_site()
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
}
// END Site class

/* End of file bootstrap.php */
/* Location: ./application/libraries/bootstrap.php */