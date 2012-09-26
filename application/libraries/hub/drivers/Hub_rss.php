<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * RSS hub driver
 *
 * This class handles transactions for a RSS hub
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Hub_rss {

	var $CI;

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

			$this->CI->db_s->insert("hubs_{$this->CI->bootstrap->site_id}", $data);
		}
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
		$schema = array();
		$fields = array('title', 'body', 'pub_date', 'link', 'author');

		foreach ($fields as $field)
		{
			$elem = new stdClass();

			$elem->name			= $field;
			$elem->type			= 'text';
			$elem->default		= NULL;
			$elem->max_length	= NULL;
			$elem->primary_key	= 0;

			$schema[] = $elem;
		}

		return $schema;
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
		// Get the feed URI from DB
		$this->CI->db_s->where('hub_id', $hub_id);
		$query 		= $this->CI->db_s->get("hubs_{$this->CI->bootstrap->site_id}");
		$feed_uri	= $query->row()->hub_source;

		// Fetch the raw feed from the URL
		$raw_feed = file_get_contents($feed_uri);
		$xml = new SimpleXmlElement($raw_feed);
		$output = array();

		// Populate feed data
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

		// Generate the WHERE claus
		$relations = array('AND', 'OR');
		$first = TRUE;
		$expr = '';

		foreach ($relations as $relation)
		{
			if (isset($where[$relation]) AND is_array($where[$relation]))
			{
				foreach ($where[$relation] as $column => $value)
				{
					if (strpos($column, '[LIKE]') === FALSE)
					{
						$column = "\$item->{$column}";

						if (strpos($column, ' ') === FALSE)
						{
							$column = "{$column} ==";
						}

						$condition = "{$column} '{$value}' ";
					}
					else
					{
						$column = str_replace(' [LIKE]', '', $column);
						$condition = "strpos(\$item->{$column}, '{$value}') !== FALSE ";
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

		// Implement order by and limit logic

		return $output;
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
		$this->CI->db_s->delete("hubs_{$this->CI->bootstrap->site_id}", array('hub_id' => $hub_id));
	}

	// --------------------------------------------------------------------
}
// END Hub_rss class

/* End of file Hub_rss.php */
/* Location: ./application/libraries/hub/drivers/Hub_rss.php */