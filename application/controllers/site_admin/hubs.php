<?php

/**
 * Site administration hub management controller
 *
 * Handles hub management actions for each site
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Hubs extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Load stuff we need for site admin
		$this->load->model('site_admin/hubs_model');
		$this->lang->load('site_admin');
		$this->session->enforce_login('admin/login');
	}

	// --------------------------------------------------------------------

	/**
	 * Hub management screen
	 *
	 * @access	public
	 * @param	int		page number for the site list
	 */
	public function manage($page = 1)
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/hubs/manage'),
				'total_rows'	=> $this->hub->count_list(),
				'per_page'		=> $this->config->item('per_page'),
				'uri_segment'	=> 4,
			))
		);

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_hubs_exp'),
			'hubs'			=> $this->hubs_model->fetch_hubs($page),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('site_admin/hubs_manage', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new hub to the site
	 *
	 * @access	public
	 */
	public function add($action = 'index')
	{
		if ($action == 'index')
		{
			if ($this->form_validation->run('site_admin/hubs/add/index'))
			{
				$hub_type = $this->input->post('hub_type');

				if ($this->hubs_model->add_hub($hub_type))
				{
					$this->session->set_flashdata('success_msg', $this->lang->line('hub_added'));
					redirect(base_url('admin/hubs/manage'), 'refresh');
				}
				else
				{
					$this->template->error_msgs = $this->lang->line('hub_add_error');
				}
			}

			// Assign view data
			$data = array(
				'page_title'		=> $this->lang->line('site_adm'),
				'page_desc'			=> $this->lang->line('manage_hubs_exp'),
				'hub_types'			=> $this->hubs_model->fetch_drivers()
			);

			// Load the view
			$this->template->load('site_admin/hubs_create', $data);
		}
		else if ($action == 'columns')
		{
			$hub_name = $this->session->userdata('hub_name');

			if ($hub_name !== FALSE)
			{
				if ($this->form_validation->run('site_admin/hubs/add/columns'))
				{
					if ($this->hubs_model->add_hub(HUB_DATABASE))
					{
						$this->session->set_flashdata('success_msg', $this->lang->line('hub_added'));
						redirect(base_url('admin/hubs/manage'), 'refresh');
					}
					else
					{
						$this->template->error_msgs = $this->lang->line('hub_add_error');
					}
				}

				// Assign view data
				$data = array(
					'page_title'	=> $this->lang->line('site_adm'),
					'page_desc'		=> $this->lang->line('manage_hubs_exp'),
					'data_types'	=> $this->hubs_model->fetch_datatypes()
				);

				// Load the view
				$this->template->load('site_admin/hubs_cols_add', $data);
			}
			else
			{
				redirect(base_url('admin/hubs/add'), 'refresh');
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a hub from the database
	 *
	 * @access	public
	 * @param	int		hub id to delete
	 */
	public function delete($hub_id)
	{
		if ($this->template->confirm_box('lang:hub_del_confirm'))
		{
			if ($this->hubs_model->delete_hub($hub_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('hub_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('hub_del_error'));
			}
		}

		redirect(base_url('admin/hubs/manage'), 'refresh');
	}

	// --------------------------------------------------------------------

	/**
	 * Validates hub source URL
	 *
	 * @access	public
	 * @param	string	url to validate
	 * @return	bool	true if url is valid
	 */
	public function check_source($url)
	{
		if ($this->input->post('hub_type') == HUB_RSS)
		{
			$data = @file_get_contents($url);

			if (empty($data))
			{
				$this->form_validation->set_message('check_source', $this->lang->line('invalid_source'));
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates hub column data
	 *
	 * @access	public
	 * @return	bool	true if column data is valid
	 */
	public function check_columns($key)
	{
		$this->load->helper('array');

		$col_names		= $this->input->post('col_names');
		$col_datatypes	= $this->input->post('col_datatypes');

		// Only one unique key column is allowed
		if (array_has_duplicates($col_datatypes, DBTYPE_KEY))
		{
			$this->form_validation->set_message('check_columns', $this->lang->line('one_unique_key'));
			return FALSE;
		}

		// Column names must be unique
		if (array_has_duplicates($col_names))
		{
			$this->form_validation->set_message('check_columns', $this->lang->line('duplicate_colname'));
			return FALSE;
		}

		// Both column name and data types should be set
		for ($idx = 0; $idx < 100; $idx++)
		{
			if (($col_names[$idx] != '' AND $col_datatypes[$idx] == '') OR ($col_names[$idx] == '' AND $col_datatypes[$idx] != ''))
			{
				$this->form_validation->set_message('check_columns', $this->lang->line('enter_col_both'));
				return FALSE;
			}
		}
		
		return TRUE;
	}

	// --------------------------------------------------------------------
}

?>