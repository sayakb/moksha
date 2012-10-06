<?php

/**
 * Page management model
 *
 * Model for adding, editing and deleting site pages
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Pages_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of site pages
	 *
	 * @access	public
	 * @return	array	list of pages
	 */
	public function fetch_pages($page)
	{
		return array();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a count of site pages
	 *
	 * @access	public
	 * @return	int		page count
	 */
	public function count_pages()
	{
		return 0;
	}

	// --------------------------------------------------------------------
}