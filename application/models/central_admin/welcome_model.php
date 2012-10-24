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
		$this->db->select_sum('data_length + index_length', 'size');
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
			else
			{
				return FALSE;
			}
		}
		else
		{
			if (class_exists('COM'))
			{
				$wmi	= new COM('WinMgmts:\\\\.');
				$cpus	= $wmi->InstancesOf('Win32_Processor');

				$cpuload = 0;
				$i = 0;

				while ($cpu = $cpus->Next())
				{
					$cpuload += $cpu->LoadPercentage;
					$i++;
				}

				$cpuload = round($cpuload / $i, 2);

				return "{$cpuload}%";
			}
			else
			{
				return FALSE;
			}
		}
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
			else
			{
				return FALSE;
			}
		}
		else
		{
			if (function_exists('exec'))
			{
				// 'systeminfo' can take a while
				set_time_limit(150);

				$uptime	= exec('systeminfo | find "System Up"');

				$parts	= explode(':', $uptime);
				$parts	= array_pop($parts);
				$parts	= explode(',', trim($parts));

				foreach (array('days', 'hours', 'mins', 'secs') as $key => $val)
				{
					$parts[$key] = explode(' ', trim($parts[$key]));
					$$val = array_shift($parts[$key]);
				}

				return "{$days}{$d} {$hours}{$h} {$mins}{$m} {$secs}{$s}";
			}
			else
			{
				return FALSE;
			}
		}
	}

	// --------------------------------------------------------------------
}