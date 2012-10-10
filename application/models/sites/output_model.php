<?php

/**
 * Site output model
 *
 * Model for performing operations when displaying a site page
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Output_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches current page details
	 *
	 * @access	public
	 * @return	mixed	page details if found, otherwise false
	 */
	public function fetch_page()
	{
		if (current_url() == base_url())
		{
			$this->db->where('page_url', '{sys:homepage}');
			$this->db->or_where('page_url', '{sys:base_url}');
		}
		else
		{
			$url = str_replace(base_url(), '', current_url());
			$this->db->where('page_url', $url);
		}

		$query = $this->db->limit(1)->get("site_pages_{$this->bootstrap->site_id}");

		if ($query->num_rows() == 1)
		{
			$page = $query->row();
			$page->page_widgets = unserialize($page->page_widgets);

			return $page;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------
}