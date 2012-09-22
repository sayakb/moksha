<?php

/**
 * Central user management controller
 *
 * Handles user management actions for central
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Users extends Moksha_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for central
		$this->load->model('central_admin/users_model');
		$this->load->library('menu');
		$this->lang->load('central_admin');
	}

	/**
	 * Central admins management screen
	 *
	 * @access	public
	 * @param	int	Page number for the user list
	 */
	public function manage($page = 1)
	{
		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('manage_users_exp'),
		);

		// Load the view
		$this->template->load('central_admin/users_manage', $data);
	}
}

?>