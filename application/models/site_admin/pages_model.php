<?php

/**
 * Page management model
 *
 * Model for adding, editing and deleting site pages
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Pages_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of site pages
	 *
	 * @access	public
	 * @return	array	list of pages
	 */
	public function fetch_pages($page)
	{
		$config = $this->config->item('pagination');
		$offset = $config['per_page'] * ($page - 1);

		$query = $this->db->limit($config['per_page'], $offset)->get("site_pages_{$this->site->site_id}");
		return $query->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a specific page
	 *
	 * @access	public
	 * @param	int		page identifier
	 * @return	array	list of pages
	 */
	public function fetch_page($page_id)
	{
		$this->db->where('page_id', $page_id);
		$query = $this->db->get("site_pages_{$this->site->site_id}");

		if ($query->num_rows() == 1)
		{
			$page = $query->row();
			$page->widgets = unserialize($page->widgets);

			return $page;
		}
		else
		{
			show_error($this->lang->line('resource_404'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of widgets for the site
	 *
	 * @access	public
	 * @param	bool	fetch widget ids only
	 * @param	bool	fetch protected widgets only
	 * @return	array	list of widgets
	 */
	public function fetch_widgets($ids_only = FALSE, $protected_only = FALSE)
	{
		$widget_ids	= array();
		$widgets	= $this->db->get("site_widgets_{$this->site->site_id}")->result();

		// Populate IDs only
		if ($ids_only)
		{
			foreach ($widgets as $widget)
			{
				$widget_ids[] = $widget->widget_id;
			}

			return $widget_ids;
		}

		// Populate protected widget IDs
		else if ($protected_only)
		{
			foreach ($widgets as $widget)
			{
				if ( ! empty($widget->password_path))
				{
					$widget_ids[] = $widget->widget_id;
				}
			}

			return $widget_ids;
		}

		return $widgets;
	}

	// --------------------------------------------------------------------

	/**
	 * Gets a list of role names
	 *
	 * @access	public
	 * @param	bool	indicates whether only IDs are to be returned
	 * @return	array	list of role names
	 */
	public function fetch_roles($ids_only = FALSE)
	{
		$roles = $this->db->get("site_roles_{$this->site->site_id}")->result();

		// Add admin and logged-in roles
		$roles = array_merge(array(
			(object)array(
				'role_id'	=> ROLE_LOGGED_IN,
				'role_name'	=> $this->lang->line('logged_in')
			),
			(object)array(
				'role_id'	=> ROLE_ADMIN,
				'role_name'	=> $this->lang->line('administrator')
			)
		), $roles);

		if ($ids_only)
		{
			$role_ids = array();

			foreach ($roles as $role)
			{
				$role_ids[] = $role->role_id;
			}

			return $role_ids;
		}
		else
		{
			return $roles;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of page layouts
	 *
	 * @access	public
	 * @return	array	list of layouts
	 */
	public function fetch_layouts()
	{
		return array(
			'1-1-1',
			'2-1',
			'1-2',
			'3'
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a count of site pages
	 *
	 * @access	public
	 * @return	int		page count
	 */
	public function count_pages()
	{
		return $this->db->count_all("site_pages_{$this->site->site_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Adds a new site page
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function add_page()
	{
		$data = array(
			'page_title'	=> $this->input->post('pg_title'),
			'page_url'		=> $this->input->post('pg_url'),
			'page_layout'	=> $this->input->post('pg_layout'),
			'access_roles'	=> $this->input->post('access_roles'),
			'success_url'	=> $this->input->post('success_url'),
			'error_url'		=> $this->input->post('error_url'),
			'page_url'		=> str_replace(base_url(), '', $this->input->post('pg_url')),
			'widgets'		=> serialize($this->populate_widgets())
		);

		$this->cache->delete_group("pageidx_{$this->site->site_id}");
		$this->admin_log->add('page_create', $data['page_url']);

		return $this->db->insert("site_pages_{$this->site->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Adds a new site page
	 *
	 * @access	public
	 * @param	int		page identifier
	 * @return	bool	true if successful
	 */
	public function update_page($page_id)
	{
		$data = array(
			'page_title'	=> $this->input->post('pg_title'),
			'page_url'		=> $this->input->post('pg_url'),
			'page_layout'	=> $this->input->post('pg_layout'),
			'access_roles'	=> $this->input->post('access_roles'),
			'success_url'	=> $this->input->post('success_url'),
			'error_url'		=> $this->input->post('error_url'),
			'page_url'		=> str_replace(base_url(), '', $this->input->post('pg_url')),
			'widgets'		=> serialize($this->populate_widgets())
		);

		$this->cache->delete_group("pageidx_{$this->site->site_id}");
		$this->admin_log->add('page_modify', $data['page_url']);

		return $this->db->update("site_pages_{$this->site->site_id}", $data, array('page_id' => $page_id));
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a site page
	 *
	 * @access	public
	 * @param	int		page identifier
	 * @return	bool	true if successful
	 */
	public function delete_page($page_id)
	{
		$page_url = $this->fetch_page($page_id)->page_url;

		$this->cache->delete_group("pageidx_{$this->site->site_id}");
		$this->admin_log->add('page_delete', $page_url);

		return $this->db->delete("site_pages_{$this->site->site_id}", array('page_id' => $page_id));
	}

	// --------------------------------------------------------------------

	/**
	 * Populates added widgets
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @param	int		page column
	 * @return	bool	true if successful
	 */
	public function populate_widgets($column = FALSE, $widget_data = FALSE)
	{
		if (isset($_POST['submit']))
		{
			if ($column !== FALSE)
			{
				return $this->input->post("pg_column{$column}");
			}
			else
			{
				$pg_column1	= $this->input->post('pg_column1');
				$pg_column2	= $this->input->post('pg_column2');
				$pg_column3	= $this->input->post('pg_column3');

				return array($pg_column1, $pg_column2, $pg_column3);
			}
		}
		else if ($widget_data !== FALSE)
		{
			if ($column !== FALSE AND isset($widget_data[$column - 1]))
			{
				return $widget_data[$column - 1];
			}
			else
			{
				return $widget_data;
			}
		}
		else
		{
			return '';
		}
	}

	// --------------------------------------------------------------------
}