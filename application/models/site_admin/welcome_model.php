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
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
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
}