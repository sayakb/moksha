<?php

/**
 * Central admin welcome page logic
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
	 * Fetches basic central admin information
	 *
	 * @access	public
	 * @return	object	requested information
	 */
	public function fetch_central_info()
	{
		$info = new stdClass();

		$info->php_version		= phpversion();
		$info->mysql_version	= mysqli_get_server_info($this->db->conn_id);
		$info->moksha_version	= $this->config->item('moksha_version');
		$info->db_size			= $this->fetch_size();
		$info->server_load		= $this->fetch_load();
		$info->server_uptime	= $this->fetch_uptime();

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
		$query = $this->db->query('SHOW TABLE STATUS');

		if ($query->num_rows() > 0)
		{
			// Calculate the total size
			$size = 0;

			foreach ($query->result() as $row)
			{
				$size += $row->Data_length + $row->Index_length;
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
	 * Fetches the server load
	 *
	 * @access	public
	 * @return	string	server load
	 */
	function fetch_load()
	{
		$os = strtolower(PHP_OS);

		if (strpos($os, 'win') === FALSE)
		{
			if (file_exists('/proc/loadavg'))
			{
				$load = file_get_contents('/proc/loadavg');
				$load = explode(' ', $load);
				return $load[0];
			}
			else if (function_exists('shell_exec'))
			{
				$load = explode(' ', `uptime`);
				return $load[count($load) - 1];
			}
		}
		else
		{
			if (function_exists('exec'))
			{
				$load = array();
				exec('wmic cpu get loadpercentage', $load);

				if ( ! empty($load[1]))
				{
					return "{$load[1]}%";
				}
			}
		}

		return 'N/A';
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches the server uptime
	 *
	 * @access	public
	 * @return	string	server uptime
	 */
	function fetch_uptime()
	{
		$os = strtolower(PHP_OS);

		$d = $this->lang->line('days_abbr');
		$h = $this->lang->line('hours_abbr');
		$m = $this->lang->line('mins_abbr');
		$s = $this->lang->line('secs_abbr');

		if (strpos($os, 'win') === FALSE)
		{
			if (function_exists('shell_exec'))
			{
				$uptime	= shell_exec("cut -d. -f1 /proc/uptime");

				$days	= floor($uptime / 60 / 60 / 24);
				$hours	= $uptime / 60 / 60 % 24;
				$mins	= $uptime / 60 % 60;
				$secs	= $uptime % 60;

				return "{$days}{$d} {$hours}{$h} {$mins}{$m} {$secs}{$s}";
			}
		}
		else
		{
			if (function_exists('exec'))
			{
				if ( ! $info = $this->cache->get('systeminfo'))
				{
					$info = array();

					set_time_limit(150);
					exec('systeminfo', $info);

					$this->cache->write($info, 'systeminfo');
				}

				foreach ($info as $line)
				{
					if (strpos($line, 'Boot Time:') !== FALSE)
					{
						$boot = trim(substr($line, strpos($line, ':') + 1));
					}
					else if (strpos($line, 'Time Zone:') !== FALSE)
					{
						$pos = strpos($line, 'UTC') + 3;

						$operation	= substr($line, $pos, 1);
						$offset_h	= substr($line, $pos + 1, 2);
						$offset_m	= substr($line, $pos + 4, 2);
					}
				}

				if (isset($boot) AND isset($operation))
				{
					$interval			= date_interval_create_from_date_string("{$offset_h} hours {$offset_m} minutes");
					$interval->invert	= $operation == '-' ? 1 : 0;

					$current	= date_add(date_create(), $interval);
					$diff		= date_diff($current, date_create($boot));

					return "{$diff->d}{$d} {$diff->h}{$h} {$diff->i}{$m} {$diff->s}{$s}";
				}
			}
		}

		return 'N/A';
	}

	// --------------------------------------------------------------------
}