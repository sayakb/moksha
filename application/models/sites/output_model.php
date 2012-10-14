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
		$this->db->order_by('length(page_url)', 'desc');
		$pages = $this->db->get("site_pages_{$this->bootstrap->site_id}")->result();

		$base = base_url();
		$curr = current_url();

		foreach ($pages as $page)
		{
			$url = base_url(expr($page->page_url));

			if (($url == $base AND $url == $curr) OR ($url != $base AND strpos($curr, $url) === 0))
			{
				$page->page_widgets = unserialize($page->page_widgets);
				return $page;
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------
}