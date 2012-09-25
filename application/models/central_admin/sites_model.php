<?php

/**
 * Sites management model
 *
 * Model for fetching, adding and deleting all Moksha sites
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Sites_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Get existing sites
	 *
	 * @access	public
	 * @param	int		page number for the list
	 * @return	array	list of sites
	 */
	public function get_sites($page)
	{
		$per_page = $this->config->item('per_page');
		$offset = $per_page * ($page - 1);

		$query = $this->db_c->limit($per_page, $offset)->get('sites');
		return $query->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Return the count of sites from the DB
	 *
	 * @access	public
	 * @return	int		having the site count
	 */
	public function count_sites()
	{
		return $this->db_c->count_all_results('sites');
	}

	// --------------------------------------------------------------------

	/**
	 * Get site details against a specific site ID
	 *
	 * @access	public
	 * @param	int		site identifier
	 * @return	array	containing user details
	 */
	public function get_site($site_id)
	{
		$query = $this->db_c->get_where('sites', array('site_id' => $site_id));

		return $query->row();
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new site to the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function add_site()
	{
		return $this->db_c->insert('sites', array('site_url' => $this->input->post('site_url')));
	}

	// --------------------------------------------------------------------

	/**
	 * Update a specific site's URL
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function update_site($site_id)
	{
		$data = array('site_url' => $this->input->post('site_url'));

		return $this->db_c->where('site_id', $site_id)->update('sites', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a site from the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function delete_site($site_id)
	{
		return $this->db_c->delete('sites', array('site_id' => $site_id));
	}
}