<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * RSS hub driver
 *
 * This class handles transactions for a RSS hub
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Hub_rss {

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
	 * Creates a new RSS hub for this site in the DB
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	string	feed source
	 * @return	void
	 */
	public function create($name, $source)
	{
		if ($source !== FALSE)
		{
			// First, insert into the index table
			$data = array(
				'site_id'		=> $this->CI->bootstrap->site_id,
				'hub_name'		=> $name,
				'hub_type'		=> HUB_RSS,
				'hub_source'	=> $source
			);

			$this->CI->db_s->insert("hubs_{$this->CI->bootstrap->site_id}", $data);
		}
	}

	// --------------------------------------------------------------------
}
// END Hub_rss class

/* End of file Hub_rss.php */
/* Location: ./application/libraries/Hub_rss.php */