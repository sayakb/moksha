<?php

/**
 * Site administration hub management controller
 *
 * Handles hub management actions for each site
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Hubs extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		if ( ! check_roles(ROLE_ADMIN))
		{
			redirect('admin/login');
		}

		$this->lang->load('site_admin');
		$this->load->model('site_admin/hubs_model');
	}

	// --------------------------------------------------------------------

	/**
	 * View data stored within a hub
	 *
	 * @access	public
	 * @param	int		page number for the site list
	 */
	public function view($hub_id, $page = 1)
	{
		// Get hub data
		$hub = $this->hubs_model->fetch_hub($hub_id);

		// Determing hub title
		if (strlen($hub->hub_name) > 20)
		{
			$hub_title = substr($hub->hub_name, 0, 20).'...';
		}
		else
		{
			$hub_title = $hub->hub_name;
		}

		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/hubs/view'),
				'total_rows'	=> $this->hubs_model->count_rows($hub->hub_name)
			))
		);

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('view_hubs_exp'),
			'hub_title'		=> sprintf($this->lang->line('viewing_hub'), $hub_title),
			'hub_columns'	=> $this->hubs_model->fetch_columns($hub->hub_name),
			'hub_data'		=> $this->hubs_model->fetch_hub_data($hub->hub_name, $page),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('site_admin/hubs_view', $data);
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
				'total_rows'	=> $this->hubs_model->count_hubs(),
				'uri_segment'	=> 4
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
	 * @param	string	current request category
	 */
	public function add($category = 'index')
	{
		if ($category == 'index')
		{
			if ($this->form_validation->run('site_admin/hubs/add/index'))
			{
				$hub_type = $this->input->post('hub_type');

				if ($this->hubs_model->add_hub($hub_type))
				{
					$this->session->set_flashdata('success_msg', $this->lang->line('hub_added'));
					redirect(base_url('admin/hubs/manage'));
				}
				else
				{
					$this->template->error_msgs = $this->lang->line('hub_add_error');
				}
			}

			// We don't need this data anymore
			$this->session->unset_userdata('hub_name');

			// Assign view data
			$data = array(
				'page_title'		=> $this->lang->line('site_adm'),
				'page_desc'			=> $this->lang->line('manage_hubs_exp'),
				'hub_types'			=> $this->hubs_model->fetch_drivers()
			);

			// Load the view
			$this->template->load('site_admin/hubs_create', $data);
		}
		else if ($category == 'columns')
		{
			$hub_name = $this->session->userdata('hub_name');

			if ($hub_name !== FALSE)
			{
				if ($this->form_validation->run('site_admin/hubs/add/columns'))
				{
					if ($this->hubs_model->add_hub(HUB_DATABASE))
					{
						$this->session->set_flashdata('success_msg', $this->lang->line('hub_added'));
						redirect(base_url('admin/hubs/manage'));
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
				redirect(base_url('admin/hubs/add'));
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Edit a hub and its columns
	 *
	 * @access	public
	 * @param	int		hub id to edit
	 */
	public function edit($hub_id)
	{
		// Get hub data
		$hub = $this->hubs_model->fetch_hub($hub_id);

		// DB hub operations
		if ($hub->driver == HUB_DATABASE)
		{
			// Rename hub operation
			if (isset($_POST['rename_hub']))
			{
				$this->form_validation->unique_exempts = array('hub_name' => $hub->hub_name);

				if ($this->form_validation->run('site_admin/hubs/edit/rename_hub'))
				{
					if ($this->hubs_model->update_hub())
					{
						$this->session->set_flashdata('success_msg', $this->lang->line('hub_renamed'));
						redirect(base_url('admin/hubs/manage'));
					}
					else
					{
						$this->template->error_msgs = $this->lang->line('hub_rename_error');
					}
				}
			}

			// Add column to hub
			if (isset($_POST['add_column']))
			{
				if ($this->form_validation->run('site_admin/hubs/edit/add_column'))
				{
					if ($this->hubs_model->add_column())
					{
						$this->session->set_flashdata('success_msg', $this->lang->line('column_added'));
						redirect(base_url('admin/hubs/manage'));
					}
					else
					{
						$this->template->error_msgs = $this->lang->line('column_add_error');
					}
				}
			}

			// Rename hub column
			if (isset($_POST['rename_column']))
			{
				$this->form_validation->unique_exempts = array('column_name' => $this->input->post('column_name_existing'));

				if ($this->form_validation->run('site_admin/hubs/edit/rename_column'))
				{
					if ($this->hubs_model->rename_column())
					{
						$this->session->set_flashdata('success_msg', $this->lang->line('column_renamed'));
						redirect(base_url('admin/hubs/manage'));
					}
					else
					{
						$this->template->error_msgs = $this->lang->line('column_rename_error');
					}
				}
			}

			// Delete hub column
			if (isset($_POST['delete_column']))
			{
				if ($this->form_validation->run('site_admin/hubs/edit/delete_column'))
				{
					if ($this->hubs_model->delete_column())
					{
						$this->session->set_flashdata('success_msg', $this->lang->line('column_deleted'));
						redirect(base_url('admin/hubs/manage'));
					}
					else
					{
						$this->template->error_msgs = $this->lang->line('column_del_error');
					}
				}
			}
		}

		// RSS hub operations
		if ($hub->driver == HUB_RSS)
		{
			$this->form_validation->unique_exempts = array('hub_name' => $hub->hub_name);

			if ($this->form_validation->run('site_admin/hubs/edit/modify_hub'))
			{
				if ($this->hubs_model->modify_hub())
				{
					$this->session->set_flashdata('success_msg', $this->lang->line('hub_modified'));
					redirect(base_url('admin/hubs/manage'));
				}
				else
				{
					$this->template->error_msgs = $this->lang->line('hub_modify_error');
				}
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_hubs_exp'),
			'data_types'	=> $this->hubs_model->fetch_datatypes(TRUE),
			'hub_columns'	=> $this->hubs_model->fetch_columns($hub->hub_name),
			'hub'			=> $hub
		);

		// Load the view
		$this->template->load('site_admin/hubs_edit', $data);
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

		redirect(base_url('admin/hubs/manage'));
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
	 * Validates hub column data when adding columns to a new hub
	 *
	 * @access	public
	 * @return	bool	true if column data is valid
	 */
	public function check_column_add($key)
	{
		$this->load->helper('array');

		$column_names		= $this->input->post('column_names');
		$column_datatypes	= $this->input->post('column_datatypes');

		// At least one unique key is required
		if ( ! in_array(DBTYPE_KEY, $column_datatypes))
		{
			$this->form_validation->set_message('check_column_add', $this->lang->line('unique_key_reqd'));
			return FALSE;
		}

		// Only one unique key column is allowed
		if (array_has_duplicates($column_datatypes, DBTYPE_KEY))
		{
			$this->form_validation->set_message('check_column_add', $this->lang->line('one_unique_key'));
			return FALSE;
		}

		// Column names must be unique
		if (array_has_duplicates($column_names))
		{
			$this->form_validation->set_message('check_column_add', $this->lang->line('duplicate_colname'));
			return FALSE;
		}

		// Disallow reserved column names
		foreach ($column_names as $name)
		{
			if (in_array($name, array('_moksha_author', '_moksha_timestamp')))
			{
				$this->form_validation->set_message('check_column_add', $this->lang->line('column_reserved'));
				return FALSE;
			}
		}

		// Both column name and data types should be set
		for ($idx = 0; $idx < 100; $idx++)
		{
			if (($column_names[$idx] != '' AND $column_datatypes[$idx] == 'none') OR ($column_names[$idx] == '' AND $column_datatypes[$idx] != 'none'))
			{
				$this->form_validation->set_message('check_column_add', $this->lang->line('enter_col_both'));
				return FALSE;
			}
		}
		
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates hub column name when adding columns to an existing hub
	 *
	 * @access	public
	 * @return	bool	true if column data is valid
	 */
	public function check_column_edit($column_name)
	{
		$hub_name		= $this->input->post('hub_name');
		$hub_columns	= $this->hubs_model->fetch_columns($hub_name);

		// Disallow reserved column names
		foreach ($column_names as $name)
		{
			if (in_array($name, array('_moksha_author', '_moksha_timestamp')))
			{
				$this->form_validation->set_message('check_column_edit', $this->lang->line('column_reserved'));
				return FALSE;
			}
		}

		// Check for duplicate columns
		if (in_array($column_name, $hub_columns))
		{
			$this->form_validation->set_message('check_column_edit', $this->lang->line('column_exists'));
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates column when deleting it. Disallows deleting if it is the
	 * last column in the hub
	 *
	 * @access	public
	 * @return	bool	true if valid
	 */
	public function check_column_delete($column_name)
	{
		$hub_name		= $this->input->post('hub_name');
		$hub_columns	= $this->hubs_model->fetch_columns($hub_name);

		// Disallow deleting reserved columns
		foreach ($column_names as $name)
		{
			if (in_array($name, array('_moksha_author', '_moksha_timestamp')))
			{
				$this->form_validation->set_message('check_column_delete', $this->lang->line('reserved_no_del'));
				return FALSE;
			}
		}

		// Disallow deleting the last column
		if (count($hub_columns) == 1)
		{
			$this->form_validation->set_message('check_column_delete', $this->lang->line('column_last'));
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates hub column data when adding columns
	 *
	 * @access	public
	 * @return	bool	true if column data is valid
	 */
	public function check_column_dropdown($column_name)
	{
		$hub_name		= $this->input->post('hub_name');
		$hub_columns	= $this->hubs_model->fetch_columns($hub_name);

		if ( ! in_array($column_name, $hub_columns))
		{
			$this->form_validation->set_message('check_column_dropdown', $this->lang->line('invalid_column'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Validates column data type selecton
	 *
	 * @access	public
	 * @return	bool	true if column data type is valid
	 */
	public function check_column_datatype($data_type)
	{
		$data_types = $this->hubs_model->fetch_datatypes();

		if ( ! isset($data_types[$data_type]))
		{
			$this->form_validation->set_message('check_column_datatype', $this->lang->line('select_datatype'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Disallows adding a column of datatype unique key
	 *
	 * @access	public
	 * @return	bool	true if column data type is valid
	 */
	public function check_disallow_unique($datatype)
	{
		if ($datatype == DBTYPE_KEY)
		{
			$this->form_validation->set_message('check_disallow_unique', $this->lang->line('one_unique_key'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------
}

?>