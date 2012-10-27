<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Session Class extension
 *
 * @package		Moksha
 * @category	Library
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Sys_Session extends CI_Session {

	/**
	 * Session Constructor
	 *
	 * The constructor sets the session table based on whether we are in central or not
	 */
	public function __construct($params = array())
	{
		if ( ! in_install())
		{
			$this->CI =& get_instance();

			$cookie_name	= $this->CI->config->item('sess_cookie_name');
			$table_name		= $this->CI->config->item('sess_table_name');

			if (in_central())
			{
				$params['sess_cookie_name']	= "moksha_{$cookie_name}";
				$params['sess_table_name']	= "central_{$table_name}";
			}
			else
			{
				$key = crc32("{$cookie_name}{$this->CI->site->site_id}");

				$params['sess_cookie_name']	= "moksha_{$key}";
				$params['sess_table_name']	= "site_{$table_name}_{$this->CI->site->site_id}";
			}

			parent::__construct($params);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Create a new session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_create()
	{
		$sessid = '';

		while (strlen($sessid) < 32)
		{
			$sessid .= mt_rand(0, mt_getrandmax());
		}

		// To make the session ID even more secure we'll combine it with the user's IP
		$sessid .= $this->CI->input->ip_address();

		$this->userdata = array(
			'session_id'	=> md5(uniqid($sessid, TRUE)),
			'ip_address'	=> $this->CI->input->ip_address(),
			'user_agent'	=> substr($this->CI->input->user_agent(), 0, 120),
			'last_activity'	=> $this->now,
			'user_data'		=> ''
		);

		// Save the data to the DB if needed
		if ($this->sess_use_database === TRUE)
		{
			$this->CI->db->insert($this->sess_table_name, $this->userdata);
		}

		// Perform reporting activities
		if (site_config('stats') == ENABLED)
		{
			// Get visitor count
			$this->CI->db->where('year', date('Y'));
			$this->CI->db->where('month', date('m'));
			$query = $this->CI->db->get("site_stats_{$this->CI->site->site_id}");

			if ($query->num_rows() == 0)
			{
				$data = array(
					'year'		=> date('Y'),
					'month'		=> date('m'),
					'visitors'	=> 1
				);

				$this->CI->db->insert("site_stats_{$this->CI->site->site_id}", $data);
			}
			else
			{
				$visitors = $query->row()->visitors;

				$this->CI->db->where('year', date('Y'));
				$this->CI->db->where('month', date('m'));
				$this->CI->db->update("site_stats_{$this->CI->site->site_id}", array('visitors' => $visitors + 1));
			}
		}

		// Write the cookie
		$this->_set_cookie();
	}

	// --------------------------------------------------------------------

	/**
	 * Search user data and return the corresponsing session ID
	 *
	 * @access	public
	 * @param	string	user_data key to look for
	 * @param	array	value for the key to match
	 * @return	string	session id against the user data
	 */
	public function search_userdata($key, $value)
	{
		$result = $this->CI->db->get($this->sess_table_name)->result();

		foreach ($result as $row)
		{
			$user_data = $this->_unserialize($row->user_data);

			if (isset($user_data[$key]))
			{
				$found = FALSE;

				// Convert objects to arrays
				if (is_object($value))
				{
					$value = (array)$value;
				}

				if (is_object($user_data[$key]))
				{
					$user_data[$key] = (array)$user_data[$key];
				}

				// Compare strings straight away
				if (is_string($value) AND is_string($user_data[$key]) AND $user_data[$key] === $value)
				{
					$found = TRUE;
				}

				// Compare each array items
				else if (is_array($value) AND is_array($user_data[$key]))
				{
					foreach ($value as $val_key => $val_data)
					{
						if (isset($user_data[$key][$val_key]) AND $user_data[$key][$val_key] === $val_data)
						{
							$found = TRUE;
						}
					}
				}

				// Item found?
				if ($found)
				{
					return $row->session_id;
				}
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Create a new session
	 *
	 * @access	public
	 * @param	string	session id to update
	 * @param	mixed	new data key
	 * @param	mixed	new data value
	 * @return	void
	 */
	public function update_userdata($session_id, $key, $value)
	{
		$this->CI->db->where('session_id', $session_id);
		$session = $this->CI->db->get($this->sess_table_name)->row();

		$user_data = $this->_unserialize($session->user_data);
		$user_data[$key] = $value;

		$new_data = array('user_data' => $this->_serialize($user_data));
		return $this->CI->db->update($this->sess_table_name, $new_data, array('session_id' => $session_id));
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a user session
	 *
	 * @access	public
	 * @param	string	session id to flush
	 * @return	void
	 */
	public function flush_session($session_id)
	{
		return $this->CI->db->delete($this->sess_table_name, array('session_id' => $session_id));
	}

	// --------------------------------------------------------------------
}
// END Sys_Session Class

/* End of file Sys_Session.php */
/* Location: ./application/libraries/Sys_Session.php */