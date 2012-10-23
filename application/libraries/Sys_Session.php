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
				$key = crc32("{$cookie_name}{$this->CI->bootstrap->site_id}");

				$params['sess_cookie_name']	= "moksha_{$key}";
				$params['sess_table_name']	= "site_{$table_name}_{$this->CI->bootstrap->site_id}";
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
			$stats_data = array(
				'session_id'		=> $this->userdata['session_id'],
				'ip_address'		=> $this->CI->input->ip_address(),
				'sess_create_time'	=> $this->now
			);

			$this->CI->db->insert("site_stats_{$this->CI->bootstrap->site_id}", $stats_data);
		}

		// Write the cookie
		$this->_set_cookie();
	}

	// --------------------------------------------------------------------

	/**
	 * Update an existing session
	 *
	 * @access	public
	 * @return	void
	 */
	function sess_update()
	{
		// We only update the session every five minutes by default
		if (($this->userdata['last_activity'] + $this->sess_time_to_update) >= $this->now)
		{
			return;
		}

		// Save the old session id so we know which record to
		// update in the database if we need it
		$old_sessid = $this->userdata['session_id'];
		$new_sessid = '';

		while (strlen($new_sessid) < 32)
		{
			$new_sessid .= mt_rand(0, mt_getrandmax());
		}

		// To make the session ID even more secure we'll combine it with the user's IP
		$new_sessid .= $this->CI->input->ip_address();

		// Turn it into a hash
		$new_sessid = md5(uniqid($new_sessid, TRUE));

		// Update the session data in the session data array
		$this->userdata['session_id'] = $new_sessid;
		$this->userdata['last_activity'] = $this->now;

		// _set_cookie() will handle this for us if we aren't using database sessions
		// by pushing all userdata to the cookie.
		$cookie_data = NULL;

		// Update the session ID and last_activity field in the DB if needed
		if ($this->sess_use_database === TRUE)
		{
			// set cookie explicitly to only have our session data
			$cookie_data = array();

			foreach (array('session_id','ip_address','user_agent','last_activity') as $val)
			{
				$cookie_data[$val] = $this->userdata[$val];
			}

			$this->CI->db->update($this->sess_table_name, array('last_activity' => $this->now, 'session_id' => $new_sessid), array('session_id' => $old_sessid));
		}

		// Update the stats table with the new session ID
		if (site_config('stats') == ENABLED)
		{
			$this->CI->db->update("site_stats_{$this->CI->bootstrap->site_id}", array('session_id' => $new_sessid), array('session_id' => $old_sessid));
		}

		// Write the cookie
		$this->_set_cookie($cookie_data);
	}

	// --------------------------------------------------------------------
}
// END Sys_Session Class

/* End of file Sys_Session.php */
/* Location: ./application/libraries/Sys_Session.php */