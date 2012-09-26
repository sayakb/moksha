<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Arom hub driver
 *
 * This class handles transactions for a atom feed hub
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Hub_atom extends Hub_driver {

	/**
	 * Creates a new atom feed hub for this site in the DB
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
				'hub_name'		=> $name,
				'hub_type'		=> HUB_ATOM,
				'hub_source'	=> $source
			);

			$this->CI->db_s->insert("hubs_{$this->CI->bootstrap->site_id}", $data);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a hub from the database, and related tables, if any
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	void
	 */
	public function delete($hub_id)
	{
		$this->CI->db_s->delete("hubs_{$this->CI->bootstrap->site_id}", array('hub_id' => $hub_id));
	}

	// --------------------------------------------------------------------
}
// END Hub_atom class

/* End of file Hub_atom.php */
/* Location: ./application/libraries/hub/drivers/Hub_atom.php */