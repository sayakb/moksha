<?php

/**
 * User authentication model
 *
 * Handles database interactions for authentication
 *
 * @package		Moksha
 * @category	Authentication
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Auth_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('captcha');
		$this->config->load('captcha');
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the user credentials
	 *
	 * @access	public
	 * @return	bool	true if valid
	 */
	public function validate_user()
	{
		$username	= $this->input->post('username');
		$password	= $this->input->post('password');
		$hash		= password_hash($password);
		$in_central	= in_central();

		// Cannot log in as anonymous user
		if (trim(strtolower($username)) == 'anonymous')
		{
			return FALSE;
		}

		// Choose the database based on whether we are in central or not
		//  - Central DB uses the users table
		//  - If we are connecting to sites DB - use the users_siteId table
		if ($in_central)
		{
			$table = 'central_users';
		}
		else
		{
			$table = "site_users_{$this->site->site_id}";
		}

		$filter = array(
			'user_name'	=> $username,
			'password'	=> $hash
		);

		$query = $this->db->where($filter)->get($table);

		if ($query->num_rows() === 1)
		{
			$user = $query->row();

			// Set the admin role for central, as there is no role data in the DB
			if ($in_central)
			{
				$user->roles = ROLE_ADMIN;
			}

			// Add additional roles
			$user->roles .= '|'.ROLE_LOGGED_IN;

			$this->session->set_userdata('user', $user);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a new CAPTCHA for the session
	 *
	 * @access	public
	 * @return	string	captcha image tag
	 */
	public function create_captcha()
	{
		if (site_config('captcha') == ENABLED)
		{
			// Load the configuration
			$config = $this->config->item('captcha');
			$config['img_url'] = base_url($config['img_url']).'/';

			// Generate the captcha
			$captcha = create_captcha($config);

			// Insert the captcha info to the DB
			$data = array(
				'captcha_time'	=> $captcha['time'],
				'word'	 		=> $captcha['word'],
				'ip_address'	=> $this->input->ip_address()
			);

			$this->db->insert("site_captcha_{$this->site->site_id}", $data);

			// Return the captcha image
			return $captcha['image'];
		}
		else
		{
			return NULL;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Validates a user submitted captcha
	 *
	 * @access	public
	 * @return	bool	true if captcha value is valid
	 */
	public function validate_captcha()
	{
		if (site_config('captcha') == ENABLED)
		{
			$config = $this->config->item('captcha');

			// Delete expired captchas
			$expiration = time() - $config['expiration'];
			$this->db->delete("site_captcha_{$this->site->site_id}", array("captcha_time <" => $expiration));

			// Now we fetch the captcha for the user
			$this->db->where('word', $this->input->post('captcha'));
			$this->db->where('ip_address', $this->input->ip_address());
			$this->db->where('captcha_time >', $expiration);

			$query = $this->db->get("site_captcha_{$this->site->site_id}");

			// Return true if we got some data
			return $query->num_rows() === 1;
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Registers a new user for the site
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function register_user()
	{
		// Add user data
		$data = array(
			'user_name'		=> $this->input->post('username'),
			'password'		=> password_hash($this->input->post('password')),
			'email_address'	=> $this->input->post('email_address')
		);

		return $this->db->insert("site_users_{$this->site->site_id}", $data);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Clears a user's session data
	 *
	 * @access	public
	 * @return	void
	 */
	public function clear_session()
	{
		$this->session->unset_userdata('user');
	}

	// --------------------------------------------------------------------
}