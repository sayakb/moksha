<?php

/**
 * Central administration model
 *
 * Handles database interactions for central
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Central_admin_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->load->database('admin');
	}

	/**
	 * Get existing sites
	 *
	 * @access	public
	 * @returns Array of rows
	 */
	public function get_sites($start)
	{
		$per_page = $this->config->item('per_page');

		// Apply the limit and get the data
		$this->db->limit($per_page, $start);
		$query = $this->db->get('sites');

		return $query->result_array();
	}

	/**
	 * Return the count of sites from the DB
	 *
	 * @access	public
	 * @returns Integer having the site count
	 */
	public function count_sites()
	{
		return $this->db->count_all_results('sites');
	}

	/**
	 * Add a new site to the DB
	 *
	 * @access	public
	 * @returns Boolean: true if successful
	 */
	public function add_site()
	{
		// Get the URL and generate a slug
		$url = $this->input->post('site_url');
		$slug = url_title($url, 'dash', TRUE);

		// Build the insert query
		$data = array(
			'site_url'	=> $url,
			'site_slug'	=> $slug
		);

		return $this->db->insert('sites', $data);
	}

	/**
	 * Delete a site from the DB
	 *
	 * @access	public
	 * @returns Boolean: true if successful
	 */
	public function delete_site($site_id)
	{
		$this->db->where('site_id', $site_id);

		return $this->db->delete('sites');
	}
} 
