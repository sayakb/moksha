<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * RSS hub driver
 *
 * This class handles transactions for a RSS hub
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Hub_rss {

	var $CI;
	var $_sort_column = NULL;
	var $_sort_dir = NULL;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a new RSS hub for this site in the DB
	 *
	 * @access	public
	 * @param	string	name of the hub
	 * @param	string	feed source
	 * @return	void
	 */
	public function create($name, $source)
	{
		if ($source !== FALSE)
		{
			// First, insert into the index table
			$data = array(
				'hub_name'		=> $name,
				'hub_driver'	=> HUB_RSS,
				'hub_source'	=> $source
			);

			$this->CI->db->insert("site_hubs_{$this->CI->bootstrap->site_id}", $data);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Drops a hub from the database, and related tables, if any
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	void
	 */
	public function drop($hub_id)
	{
		$this->CI->db->delete("site_hubs_{$this->CI->bootstrap->site_id}", array('hub_id' => $hub_id));
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches the schema for a hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	array	hub schema
	 */
	public function schema($hub_id)
	{
		return array(
			'title'		=> DBTYPE_TEXT,
			'body'		=> DBTYPE_TEXT,
			'pub_date'	=> DBTYPE_TEXT,
			'link'		=> DBTYPE_TEXT,
			'author'	=> DBTYPE_TEXT
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Selects data from a hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @param	array	where claus for data selection
	 * @param	array	order by claus
	 * @param	array	limit to be applied
	 * @return	array	result of the query
	 */
	public function get($hub_id, $where, $order_by, $limit)
	{
		$xml = new SimpleXmlElement($this->fetch_feed($hub_id));
		$output = array();

		if ($xml->channel)
		{
			foreach ($xml->channel->item as $item)
			{
				$data = new stdClass();

				$data->title	= (string)$item->title;
				$data->body		= (string)$item->description;
				$data->pub_date	= (string)$item->pubDate;
				$data->link		= (string)$item->link;
				$data->author	= (string)$item->children('http://purl.org/dc/elements/1.1/')->creator;

				$output[] = $data;
			}
		}
		else
		{
			foreach ($xml->entry as $item)
			{
				$data = new stdClass();

				$data->title	= (string)$item->title;
				$data->body		= (string)$item->content;
				$data->pub_date	= (string)$item->published;
				$data->link		= (string)$item->link['href'];
				$data->author	= (string)$item->children('http://purl.org/dc/elements/1.1/')->creator;

				$output[] = $data;
			}
		}

		// Process the WHERE claus
		if (is_array($where))
		{
			$relations = array('AND', 'OR');
			$first = TRUE;
			$expr = '';

			foreach ($relations as $relation)
			{
				if (isset($where[$relation]) AND is_array($where[$relation]))
				{
					foreach ($where[$relation] as $column => $value)
					{
						// Add the equality operator, as it will not be added explicitly
						if (strpos($column, ' ') === FALSE)
						{
							$column = "{$column} ==";
						}

						// Do we have a like query?
						if (strpos($column, '[LIKE]') === FALSE)
						{
							$space = strpos($column, ' ');
							$colname = substr($column, 0, $space);
							$operator = substr($column, $space);

							$condition = "strtolower(\$item->{$colname}) {$operator} strtolower('{$value}') ";
						}
						else
						{
							$column = str_replace(' [LIKE]', '', $column);
							$condition = "stripos(\$item->{$column}, '{$value}') !== FALSE ";
						}

						if (!$first)
						{
							$expr .= "{$relation} {$condition}";
						}
						else
						{
							$expr .= $condition;
							$first = FALSE;
						}
					}
				}
			}

			// Apply the WHERE claus
			if ( ! empty($expr))
			{
				$output_where = array();

				foreach ($output as $item)
				{		
					eval("\$success = ({$expr});");

					if ($success)
					{
						$output_where[] = $item;
					}
				}

				$output = $output_where;
			}
		}

		// Process the ORDER BY claus
		if (is_array($order_by))
		{
			foreach ($order_by as $column => $dir)
			{
				$this->_sort_column	= $column;
				$this->_sort_dir	= $dir;

				uasort($output, array($this, 'rss_sort'));		
			}
		}

		// Process LIMIT claus
		if (is_array($limit) AND $limit[1] < count($output))
		{
			$output_limit = array();

			// Get the upper limit
			$upper_limit = $limit[0] + $limit[1];
			
			if ($upper_limit > count($output))
			{
				$upper_limit = count($output);
			}

			// Filter objects
			for ($idx = $limit[1]; $idx < $upper_limit; $idx++)
			{
				$output_limit[] = $output[$idx];
			}

			$output = $output_limit;
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns count of rows in a hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	int		record count
	 */
	public function count_all($hub_id)
	{
		return count($this->get($hub_id, NULL, NULL, NULL));
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches raw feed data for a specific hub
	 *
	 * @access	public
	 * @param	int		hub unique identifier
	 * @return	string	raw feed data
	 */
	public function fetch_feed($hub_id)
	{
		if ( ! $raw_feed = $this->CI->cache->get("hubfeed_{$this->CI->bootstrap->site_id}_{$hub_id}"))
		{
			$query = $this->CI->db->where('hub_id', $hub_id)->get("site_hubs_{$this->CI->bootstrap->site_id}");
			
			if ($query->num_rows() === 1)
			{
				$raw_feed = @file_get_contents($query->row()->hub_source);
				$this->CI->cache->write($raw_feed, "hubfeed_{$this->CI->bootstrap->site_id}_{$hub_id}");
			}
		}

		return $raw_feed;
	}

	// --------------------------------------------------------------------

	/**
	 * Sorts an array based on a stdClass field
	 *
	 * @access	private
	 * @param	mixed	first item to compare
	 * @param	mixed	second item to compare
	 * @return	void
	 */
	private function rss_sort($first, $second) 
	{
		$column	= $this->_sort_column;
		$dir	= $this->_sort_dir;
		
		if (isset($column) AND isset($dir))
		{
			if ($dir == 'ASC')
			{
				return strcasecmp($first->$column, $second->$column);
			}
			else
			{
				return strcasecmp($second->$column, $first->$column);
			}
		}
		else
		{
			return 0;
		}
	}

	// --------------------------------------------------------------------
}
// END Hub_feed class

/* End of file Hub_rss.php */
/* Location: ./application/libraries/hub/drivers/Hub_rss.php */