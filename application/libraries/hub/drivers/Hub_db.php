<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Database hub driver
 *
 * This class handles DB interactions for hub transactions
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Hub_db {

	var $CI;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->dbforge();
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a new hub for this site in the DB
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	array	schema for the hub table
	 * @return	bool	true on successful creation
	 */
	public function create($name, $schema)
	{
		// First, insert into the index table
		$data = array(
			'site_id'	=> $this->CI->bootstrap->site_id,
			'hub_name'	=> $name,
			'hub_type'	=> HUB_DATABASE
		);
		
		$this->CI->db_s->insert("hubs_{$this->CI->bootstrap->site_id}", $data);
		
		// We use the last inserted hub ID as the identifier for the table
		$hub_id = 
	}

	// --------------------------------------------------------------------
}
// END Hub_db class

/* End of file Hub_db.php */
/* Location: ./application/libraries/Hub_db.php */