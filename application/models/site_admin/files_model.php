<?php

/**
 * File management model
 *
 * Model for adding and deleting stylesheets/scripts
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Files_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of static files
	 *
	 * @access	public
	 * @param	int		page number
	 * @return	array	list of files
	 */
	public function fetch_files($page)
	{
		$config = $this->config->item('pagination');
		$offset = $config['per_page'] * ($page - 1);

		$query = $this->db->limit($config['per_page'], $offset)->get("site_files_{$this->bootstrap->site_id}");
		return $query->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a count of static files
	 *
	 * @access	public
	 * @return	int		file count
	 */
	public function fetch_file_types()
	{
		return array(
			'css'	=> $this->lang->line('stylesheet'),
			'js'	=> $this->lang->line('javascript')
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a count of static files
	 *
	 * @access	public
	 * @return	int		file count
	 */
	public function count_files()
	{
		return $this->db->count_all_results("site_files_{$this->bootstrap->site_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new stylesheet/script file
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function add_file()
	{
		$file_type = $this->input->post('file_type');

		// Get file upload configuration
		$upload = $this->config->item('upload');
		$config = $upload['admin'];

		// Set the destination folder
		$config['upload_path'] .= $file_type;

		// Initialize the file upload library
		$this->upload->initialize($config);

		// Upload the file
		if ($this->upload->do_upload('file'))
		{
			$file = $this->upload->data();
			$data = array(
				'file_name'		=> $file['orig_name'],
				'file_type'		=> $file_type,
				'relative_path'	=> $config['upload_path'].'/'.$file['file_name']
			);

			if ($this->db->insert("site_files_{$this->bootstrap->site_id}", $data))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a static file from the DB
	 *
	 * @access	public
	 * @return	bool	true if successful
	 */
	public function delete_file($file_id)
	{
		// Get the file entry from the DB
		$this->db->where('file_id', $file_id);
		$file = $this->db->get("site_files_{$this->bootstrap->site_id}")->row();

		if ($file !== FALSE)
		{
			// Delete the actual file
			@unlink(realpath($file->relative_path));

			// Delete the entry from the DB
			if ($this->db->delete("site_files_{$this->bootstrap->site_id}", array('file_id' => $file_id)))
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------
}