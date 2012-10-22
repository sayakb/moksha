<?php

/**
 * Site admin welcome page logic
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Welcome_model extends CI_Model {

	/**
	 * Start and end times for stats filter
	 *
	 * @access	public
	 * @var		int
	 */
	var $start_ts;
	var $end_ts;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->config->load('schema');
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches basic site information
	 *
	 * @access	public
	 * @return	object	site information
	 */
	public function fetch_site_info()
	{
		$info = new stdClass();

		$info->site_id		= $this->bootstrap->site_id;
		$info->tables		= count($this->config->item('schema'));
		$info->db_size		= $this->fetch_size();
		$info->user_count	= $this->fetch_count('users') - 1;
		$info->widget_count	= $this->fetch_count('widgets');
		$info->pages_count	= $this->fetch_count('pages');
		$info->stylesheets	= $this->fetch_count('files', array('file_type' => 'css'));
		$info->scripts		= $this->fetch_count('files', array('file_type' => 'js'));

		return $info;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches site statistics
	 *
	 * @access	public
	 * @return	object	site statistics
	 */
	public function fetch_stats()
	{
		if (site_config('stats') == ENABLED)
		{
			$stats = new stdClass();

			$stats->top_pages	= $this->fetch_top_pages();
			$stats->visitors	= $this->fetch_visitors();

			return $stats;
		}
		else
		{
			return NULL;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches the site's DB footprint
	 *
	 * @access	public
	 * @return	string	formatted size for the site tables
	 */
	public function fetch_size()
	{
		// Get the table size from information schema
		$this->db->select_sum('data_length + index_length', 'size');
		$this->db->like('table_name', "site_", 'after');
		$this->db->like('table_name', "_{$this->bootstrap->site_id}", 'before');

		$query = $this->db->get('information_schema.TABLES');

		if ($query->num_rows() == 1)
		{
			$size = $query->row()->size;

			// Format the size
			$suffix = array('bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
			$offset = 0;

			while ($size > 1024)
			{
				$size /= 1024;
				$offset++;
			}

			$size = round($size, 2);
			return "{$size} {$suffix[$offset]}";
		}

		return 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches count for specific item
	 *
	 * @access	public
	 * @param	string	item to count
	 * @param	array	filters to be applied when counting
	 * @return	int		count of item
	 */
	public function fetch_count($item, $filters = FALSE)
	{
		$table = "site_{$item}_{$this->bootstrap->site_id}";

		if ($this->db->table_exists($table))
		{
			if (is_array($filters))
			{
				foreach ($filters as $column => $value)
				{
					$this->db->where($column, $value);
				}
			}

			return $this->db->count_all_results($table);
		}
		else
		{
			return 0;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of top 10 pages
	 *
	 * @access	public
	 * @return	string	list of pages
	 */
	public function fetch_top_pages()
	{
		$all_visits	= array();
		$list		= array();

		// Fetch the page visits from the stats table
		$result = $this->db->select('page_visits')->get("site_stats_{$this->bootstrap->site_id}")->result();

		foreach ($result as $row)
		{
			$visits		= explode('|', $row->page_visits);
			$all_visits	= array_merge($all_visits, $visits);
		}

		// Get the page visit counts
		$visit_counts = array_count_values($all_visits);
		arsort($visit_counts);

		// Determine the top 10 pages from the counts
		$top_pages = array_keys($visit_counts);
		$top_pages = array_slice($top_pages, 0, 10);

		// Generate the page list and inject the hits data
		$this->db->where_in($top_pages);
		$pages = $this->db->get("site_pages_{$this->bootstrap->site_id}")->result();

		foreach ($pages as $page)
		{
			$item = new stdClass();

			$item->url		= $page->page_url;
			$item->title	= $page->page_title;
			$item->hits		= isset($visit_counts[$page->page_id]) ? $visit_counts[$page->page_id] : 0;

			$list[] = $item;
		}

		return $list;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches visitors for the current site
	 *
	 * @access	public
	 * @param	int		year for which visitors are to be fetched
	 * @return	string	visitor counts
	 */
	public function fetch_visitors($year = FALSE)
	{
		$current_year = date('Y');

		// It's unrealistic to support a wider range
		if ($year === FALSE OR $year < 2000 OR $year > 2100)
		{
			$year = $current_year;
		}

		// Fetch user statistics
		$this->db->select('sess_create_time');
		$this->db->where('page_visits !=', '');

		$stats = $this->db->get("site_stats_{$this->bootstrap->site_id}")->result();

		// Build count for each month
		$date		= date_create("01/01/{$year}");
		$one_month	= date_interval_create_from_date_string('1 month');

		for ($month = 1; $month <= 12; $month++)
		{
			// Get the start and end times
			$this->start_ts	= date_timestamp_get($date);
			$this->end_ts	= date_timestamp_get(date_add($date, $one_month)) - 1;

			// Get the count for the current month
			$month_stats[$month] = count(array_filter($stats, array($this, 'filter_current_month')));
		}

		return implode('|', $month_stats);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of months for the visitor stats
	 *
	 * @access	public
	 * @return	array	list of months
	 */
	public function fetch_months()
	{
		for ($month = 1; $month <= 12; $month++)
		{
			$date		= date_create("{$month}/01/2000");
			$names[]	= strtolower(date('M', date_timestamp_get($date)));
		}

		return $names;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of years for the visitor stats
	 *
	 * @access	public
	 * @return	array	list of years
	 */
	public function fetch_years()
	{
		for ($year = 2000; $year <= 2100; $year++)
		{
			$years[$year] = $year;
		}

		return $years;
	}

	// --------------------------------------------------------------------

	/**
	 * Filters the stats array to values corresponding to the current month
	 *
	 * @access	public
	 * @return	bool	indicating whether the item should be removed
	 */
	public function filter_current_month($item)
	{
		return $item->sess_create_time >= $this->start_ts AND $item->sess_create_time <= $this->end_ts;
	}

	// --------------------------------------------------------------------
}