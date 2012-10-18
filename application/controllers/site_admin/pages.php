<?php

/**
 * Page management operations
 *
 * Allows you to create, edit and delete site pages
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Pages extends CI_Controller {

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
		$this->load->model('site_admin/pages_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Page management screen
	 *
	 * @access	public
	 */
	public function manage($page = 1)
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/pages/manage'),
				'total_rows'	=> $this->pages_model->count_pages(),
				'uri_segment'	=> 4,
			))
		);
		
		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('manage_pages_exp'),
			'pages'			=> $this->pages_model->fetch_pages($page),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('site_admin/pages_manage', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Add new page to the site
	 *
	 * @access	public
	 */
	public function add()
	{
		if ($this->form_validation->run('site_admin/pages'))
		{
			if ($this->pages_model->add_page())
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('page_added'));
				redirect(base_url('admin/pages/manage'));
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('page_add_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'		=> $this->lang->line('site_adm'),
			'page_desc'			=> $this->lang->line('manage_pages_exp'),
			'roles'				=> $this->pages_model->fetch_roles(),
			'widgets'			=> $this->pages_model->fetch_widgets(),
			'pg_column1'		=> $this->pages_model->populate_widgets(1),
			'pg_column2'		=> $this->pages_model->populate_widgets(2),
			'pg_column3'		=> $this->pages_model->populate_widgets(3),
			'pg_title'			=> set_value('pg_title'),
			'pg_url'			=> set_value('pg_url'),
			'pg_success_url'	=> set_value('pg_success_url'),
			'pg_error_url'		=> set_value('pg_error_url'),
			'pg_layout'			=> set_value('pg_layout', '1-1-1'),
			'pg_roles'			=> set_value('pg_roles'),
		);

		// Load the view
		$this->template->load('site_admin/pages_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Edits a site page
	 *
	 * @access	public
	 */
	public function edit($page_id)
	{
		// Fetch page data
		$page = $this->pages_model->fetch_page($page_id);

		// Set exempts for email and name fields
		$this->form_validation->unique_exempts = array(
			'page_title'	=> $page->page_title,
			'page_url'		=> $page->page_url
		);

		if ($this->form_validation->run('site_admin/pages'))
		{
			if ($this->pages_model->update_page($page_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('page_updated'));
				redirect(base_url('admin/pages/manage'));
			}
			else
			{
				$this->template->error_msgs = $this->lang->line('page_update_error');
			}
		}

		// Assign view data
		$data = array(
			'page_title'		=> $this->lang->line('site_adm'),
			'page_desc'			=> $this->lang->line('manage_pages_exp'),
			'roles'				=> $this->pages_model->fetch_roles(),
			'widgets'			=> $this->pages_model->fetch_widgets(),
			'pg_column1'		=> $this->pages_model->populate_widgets(1, $page->page_widgets),
			'pg_column2'		=> $this->pages_model->populate_widgets(2, $page->page_widgets),
			'pg_column3'		=> $this->pages_model->populate_widgets(3, $page->page_widgets),
			'pg_title'			=> set_value('pg_title', $page->page_title),
			'pg_url'			=> set_value('pg_url', $page->page_url),
			'pg_success_url'	=> set_value('pg_success_url', $page->page_success_url),
			'pg_error_url'		=> set_value('pg_error_url', $page->page_error_url),
			'pg_layout'			=> set_value('pg_layout', $page->page_layout),
			'pg_roles'			=> set_value('pg_roles', $page->page_roles),
		);

		// Load the view
		$this->template->load('site_admin/pages_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a page from the database
	 *
	 * @access	public
	 * @param	int		page id to delete
	 */
	public function delete($page_id)
	{
		if ($this->template->confirm_box('lang:page_del_confirm'))
		{
			if ($this->pages_model->delete_page($page_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('page_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('page_del_error'));
			}
		}

		redirect(base_url('admin/pages/manage'));
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the page layout
	 *
	 * @access	public
	 * @param	string	layout to validate
	 * @return	bool	true if valid
	 */
	public function check_layout($layout)
	{
		$valid_layouts = $this->pages_model->fetch_layouts();

		if ( ! in_array($layout, $valid_layouts))
		{
			$this->form_validation->set_message('check_layout', $this->lang->line('invalid_layout'));
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates widgets added to the page
	 *
	 * @access	public
	 * @return	bool	true if valid
	 */
	public function check_widgets()
	{
		$column1 = $this->input->post('pg_column1');
		$column2 = $this->input->post('pg_column2');
		$column3 = $this->input->post('pg_column3');

		if (empty($column1) AND empty($column2) AND empty($column3))
		{
			$this->form_validation->set_message('check_widgets', $this->lang->line('widget_required'));
			return FALSE;
		}

		$column1_ary = explode('|', $column1);
		$column2_ary = explode('|', $column2);
		$column3_ary = explode('|', $column3);
		$widgets_ary = array_merge($column1_ary, $column2_ary, $column3_ary);

		if (is_array($widgets_ary))
		{
			$valid_widgets = $this->pages_model->fetch_widgets(TRUE);

			foreach ($widgets_ary as $widget)
			{
				if ( ! empty($widget) AND ! in_array($widget, $valid_widgets))
				{
					$this->form_validation->set_message('check_widgets', $this->lang->line('invalid_widget'));
					return FALSE;
				}
			}

			return TRUE;
		}

		$this->form_validation->set_message('check_widgets', $this->lang->line('invalid_widget'));
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the site URL to check for restricted paths
	 *
	 * @access	public
	 * @param	string	url to validate
	 * @return	bool	true if valid
	 */
	public function check_url($url)
	{
		$config		= $this->config->item('pages');
		$disallowed	= $config['disallowed_urls'];
		$url		= str_replace(base_url(), '', $url);

		foreach ($disallowed as $item)
		{
			$regex	= '/^\/?'.$item.'(\/(.*?)|$)/';

			if (preg_match($regex, $url))
			{
				$this->form_validation->set_message('check_url', $this->lang->line('disallowed_url'));
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validates submitted user roles
	 *
	 * @access	public
	 * @param	string	roles to validate
	 * @return	bool	true if valid
	 */
	public function check_roles($roles)
	{
		$roles = trim($roles);

		if (empty($role))
		{
			return TRUE;
		}

		$roles_ary = explode('|', $roles);

		if (is_array($roles_ary))
		{
			$valid_roles = $this->pages_model->fetch_roles(TRUE);

			foreach ($roles_ary as $role)
			{
				if ( ! in_array($role, $valid_roles))
				{
					$this->form_validation->set_message('check_roles', $this->lang->line('invalid_role'));
					return FALSE;
				}
			}

			return TRUE;
		}

		$this->form_validation->set_message('check_roles', $this->lang->line('invalid_role'));
		return FALSE;
	}

	// --------------------------------------------------------------------
}

?> 
