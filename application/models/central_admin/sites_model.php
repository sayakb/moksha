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

	/**
	 * Get existing sites
	 *
	 * @access	public
	 * @param	int		Page number for the list
	 * @return	array	List of sites
	 */
	public function get_sites($page)
	{
		$per_page = $this->config->item('per_page');
		$offset = $per_page * ($page - 1);

		$query = $this->db_c->limit($per_page, $offset)->get('sites');
		return $query->result();
	}

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

	/**
	 * Add a new site to the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function add_site()
	{
		// Get the URL and generate a slug
		$url = $this->input->post('site_url');
		$slug = url_title($url, '_', TRUE);

		// Build the insert query
		$data = array(
			'site_url'	=> $url,
			'site_slug'	=> $slug
		);

		return $this->db_c->insert('sites', $data);
	}

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