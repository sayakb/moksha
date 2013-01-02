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

		$info->site_id		= $this->site->site_id;
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
		$prefix	= $this->db->dbprefix('site_');
		$prefix	= str_replace('_', '\_', $prefix);
		$suffix	= '\_'.$this->site->site_id;
		$query	= $this->db->query('SHOW TABLE STATUS');

		if ($query->num_rows() > 0)
		{
			// Calculate the total size
			$size = 0;

			foreach ($query->result() as $row)
			{
				if (preg_match("/{$prefix}(.*){$suffix}/", $row->Name))
				{
					$size += $row->Data_length + $row->Index_length;
				}
			}

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

		return 'N/A';
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
		$table = "site_{$item}_{$this->site->site_id}";

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
		$this->db->order_by('access_count', 'desc');
		$this->db->limit(10);

		return $this->db->get("site_pages_{$this->site->site_id}")->result();
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

		// Get the stats for the current year
		$stats = $this->db->get_where("site_stats_{$this->site->site_id}", array('year' => $year))->result();

		// Populate data for each month
		foreach ($stats as $stat)
		{
			$visitors[$stat->month] = $stat->visitors;
		}

		// Fill up the missing values
		for ($month = 1; $month <= 12; $month++)
		{
			if ( ! isset($visitors[$month]))
			{
				$visitors[$month] = 0;
			}
		}

		// Return the formatted visitor count
		ksort($visitors);
		return implode('|', $visitors);
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
}