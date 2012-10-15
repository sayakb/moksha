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
		// Get the page news
		if ( ! $pages = $this->cache->get("pageidx_{$this->bootstrap->site_id}"))
		{
			$this->db->order_by('length(page_url)', 'desc');
			$pages = $this->db->get("site_pages_{$this->bootstrap->site_id}")->result();

			$this->cache->write($pages, "pageidx_{$this->bootstrap->site_id}");
		}

		if (count($pages) > 0)
		{
			// If we have only one numeric segment, it means we are on the homepage
			if ($this->uri->total_segments() == 1 AND ctype_digit($this->uri->segment(1)))
			{
				$base = base_url();
				$curr = base_url();
			}
			else
			{
				$base = base_url();
				$curr = current_url();
			}

			foreach ($pages as $page)
			{
				$url = base_url(expr($page->page_url));

				if (($url == $base AND $url == $curr) OR ($url != $base AND strpos($curr, $url) === 0))
				{
					$page->page_widgets = unserialize($page->page_widgets);
					return $page;
				}
			}
		}
		else
		{
			show_error($this->lang->line('site_no_pages'));
		}

		return FALSE;
	}

	// --------------------------------------------------------------------
}