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

		$query = $this->db->limit($config['per_page'], $offset)->get("site_pages_{$this->bootstrap->site_id}");
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

		$page = $this->db->get("site_pages_{$this->bootstrap->site_id}")->row();
		$page->page_widgets = unserialize($page->page_widgets);

		return $page;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of widgets for the site
	 *
	 * @access	public
	 * @param	int		page number for the list
	 * @return	array	list of widgets
	 */
	public function fetch_widgets($ids_only = FALSE)
	{
		$widgets = $this->db->get("site_widgets_{$this->bootstrap->site_id}")->result();

		if ($ids_only)
		{
			$widget_ids = array();

			foreach ($widgets as $widget)
			{
				$widget_ids[] = $widget->widget_id;
			}

			return $widget_ids;
		}
		else
		{
			return $widgets;
		}
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
		$roles = $this->db->get("site_roles_{$this->bootstrap->site_id}")->result();

		// Add author and logged-in roles
		$roles = array_merge(array(
			(object)array(
				'role_id'	=> ROLE_AUTHOR,
				'role_name'	=> $this->lang->line('author')
			),
			(object)array(
				'role_id'	=> ROLE_LOGGED_IN,
				'role_name'	=> $this->lang->line('logged_in')
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
		return $this->db->count_all("site_pages_{$this->bootstrap->site_id}");
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
			'page_roles'	=> $this->input->post('pg_roles'),
			'page_widgets'	=> serialize($this->populate_widgets())
		);

		return $this->db->insert("site_pages_{$this->bootstrap->site_id}", $data);
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
			'page_roles'	=> $this->input->post('pg_roles'),
			'page_widgets'	=> serialize($this->populate_widgets())
		);

		return $this->db->update("site_pages_{$this->bootstrap->site_id}", $data, array('page_id' => $page_id));
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
		return $this->db->delete("site_pages_{$this->bootstrap->site_id}", array('page_id' => $page_id));
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