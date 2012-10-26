<?php

/**
 * Log viewer model
 *
 * Logic for viewing and filtering administration logs
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Logs_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
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
		return $this->admin_log->count_entries();
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
		return $this->admin_log->fetch_log_sites();
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
	public function fetch_entries($page)
	{
		$filters	= array();
		$site_id	= $this->input->post('site');
		$from_time	= $this->input->post('from_date');
		$to_time	= $this->input->post('to_date');

		if ( ! empty($site_id) AND $site_id != SITE_NONE)
		{
			$filters['site_id'] = $site_id;
		}

		if ( ! empty($from_time) AND strtotime($from_time) !== FALSE)
		{
			$filters['from_date'] = strtotime($from_time);
		}

		if ( ! empty($to_time) AND strtotime($to_time) !== FALSE)
		{
			$filters['to_date'] = strtotime($to_time);
		}

		return $this->admin_log->fetch_entries($page, $filters);
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
		return $this->admin_log->clear_entries();
	}

	// --------------------------------------------------------------------
}