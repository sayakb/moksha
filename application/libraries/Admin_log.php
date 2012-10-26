<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Admin Log Library
 *
 * This class exposes a custom template load method that auto-includes the
 * page header and footer
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Admin_log {

	var $CI;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Adds an entry to the admin log
	 *
	 * @access	public
	 * @param	string	message to log
	 * @return	void
	 */
	public function add($message)
	{
		$lang = $this->CI->lang->line($message);

		$entry = array(
			'site_id'	=> $this->CI->site->site_id,
			'message'	=> $lang ? $lang : $message,
			'log_time'	=> time()
		);

		return $this->CI->db->insert('central_logs', $entry);
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the count of log entries
	 *
	 * @access	public
	 * @return	int		log entry count
	 */
	public function count_entries()
	{
		return $this->CI->db->count_all('central_logs');
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches different log sites
	 *
	 * @access	public
	 * @return	array	log site list
	 */
	public function fetch_log_sites()
	{
		// Fetch the site list
		$sites = $this->CI->db->get('central_sites')->result();

		// Build the site array
		$sites_ary = array(
			SITE_NONE		=> NULL,
			SITE_CENTRAL	=> $this->CI->lang->line('central')
		);

		foreach ($sites as $site)
		{
			$sites_ary[$site->site_id] = $site->site_url;
		}

		return $sites_ary;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches admin log entries from the DB
	 *
	 * @access	public
	 * @param	int		page number
	 * @param	array	filters
	 * @return	array	log entries
	 */
	public function fetch_entries($page = 1, $filters = FALSE)
	{
		// Apply the filters
		if (is_array($filters))
		{
			if (isset($filters['site_id']))
			{
				$this->CI->db->where('central_logs.site_id', $filters['site_id']);
			}

			if (isset($filters['from_date']))
			{
				$this->CI->db->where('central_logs.log_time >=', $filters['from_date']);
			}

			if (isset($filters['to_date']))
			{
				$this->CI->db->where('central_logs.log_time <=', $filters['to_date']);
			}
		}

		// Return the log entries
		$config = $this->CI->config->item('pagination');
		$offset = $config['per_page'] * ($page - 1);

		$this->CI->db->join('central_sites', 'central_logs.site_id = central_sites.site_id', 'left');
		$this->CI->db->limit($config['per_page'], $offset);

		return $this->CI->db->get('central_logs')->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Clears all admin log entries
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear_entries()
	{
		return $this->CI->db->empty_table('central_logs');
	}

	// --------------------------------------------------------------------
}
// END Admin_log class

/* End of file Admin_log.php */
/* Location: ./application/libraries/Admin_log.php */