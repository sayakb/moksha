<?php

/**
 * Administration log viewer
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Logs extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->site->admin_only();
		$this->load->model('central_admin/logs_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Log viewer screen
	 *
	 * @access	public
	 * @param	int		page number for the site list
	 */
	public function view($page = 1)
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/central/logs/view'),
				'total_rows'	=> $this->logs_model->count_entries()
			))
		);

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('admin_logs_exp'),
			'entries'		=> $this->logs_model->fetch_entries($page),
			'log_sites'		=> $this->logs_model->fetch_log_sites(),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('central_admin/logs_view', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Clear the admin log entries
	 *
	 * @access	public
	 */
	public function clear()
	{
		if ($this->template->confirm_box('lang:log_del_confirm'))
		{
			$this->logs_model->clear_entries();
			$this->session->set_flashdata('success_msg', $this->lang->line('log_cleared'));
		}

		redirect(base_url('admin/central/logs/view'));
	}

	// --------------------------------------------------------------------
}

?>