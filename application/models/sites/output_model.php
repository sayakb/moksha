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
		// Get the page list
		if ( ! $pages = $this->cache->get("pageidx_{$this->site->site_id}"))
		{
			$this->db->order_by('length(page_url)', 'desc');
			$pages = $this->db->get("site_pages_{$this->site->site_id}")->result();

			$this->cache->write($pages, "pageidx_{$this->site->site_id}");
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
					// Update the hit counter
					if (site_config('stats') == ENABLED)
					{
						$page_visits = $this->session->userdata('page_visits');

						if ( ! is_array($page_visits))
						{
							$page_visits = array();
						}

						if ( ! in_array($page->page_id, $page_visits))
						{
							$this->db->where('page_id', $page->page_id);
							$this->db->update("site_pages_{$this->site->site_id}", array('access_count' => $page->access_count + 1));

							$page_visits[] = $page->page_id;
							$this->session->set_userdata('page_visits', $page_visits);
						}
					}

					// Return the page data
					$page->widgets = unserialize($page->widgets);
					return $page;
				}
			}
		}
		else
		{
			$no_pages = sprintf($this->lang->line('site_no_pages_exp'), base_url('admin'));
			show_error($no_pages, 500, $this->lang->line('site_no_pages'));
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches stylesheets and scripts for the page
	 *
	 * @access	public
	 * @return	string	markup for the page css/js
	 */
	public function fetch_header()
	{
		$header = '';

		// Get the page files
		if ( ! $files = $this->cache->get("pagefiles_{$this->site->site_id}"))
		{
			$files = $this->db->get("site_files_{$this->site->site_id}")->result();
			$this->cache->write($files, "pagefiles_{$this->site->site_id}");
		}

		// Generate the header
		foreach ($files as $file)
		{
			if ($file->file_type == 'css')
			{
				$header .= '<link href="'.base_url($file->relative_path).'" rel="stylesheet" />';
			}
			else if ($file->file_type == 'js')
			{
				$header .= '<script type="text/javascript" src="'.base_url($file->relative_path).'"></script>';
			}
		}

		return $header;
	}

	// --------------------------------------------------------------------
}