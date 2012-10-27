<?php

/**
 * Sites management model
 *
 * Model for fetching, adding and deleting all Moksha sites
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
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
	 * Get site details against a specific site ID
	 *
	 * @access	public
	 * @param	int		site identifier
	 * @return	array	containing site details
	 */
	public function fetch_site($site_id)
	{
		return $this->site->fetch($site_id);
	}

	// --------------------------------------------------------------------

	/**
	 * Get existing sites
	 *
	 * @access	public
	 * @param	int		page number for the list
	 * @return	array	list of sites
	 */
	public function fetch_sites($page)
	{
		$config = $this->config->item('pagination');
		$offset = $config['per_page'] * ($page - 1);

		$query = $this->db->limit($config['per_page'], $offset)->get('central_sites');
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
		return $this->db->count_all_results('central_sites');
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
		$site_url = $this->input->post('site_url');
		$this->admin_log->add('site_create', $site_url);

		return $this->site->add($site_url);
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
		$site_url = $this->input->post('site_url');
		$this->admin_log->add('site_modify', $site_url);

		return $this->site->update($site_id, $site_url);
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
		$site_url = $this->fetch_site($site_id)->site_url;
		$this->admin_log->add('site_delete', $site_url);

		return $this->site->delete($site_id);
	}

	// --------------------------------------------------------------------
}