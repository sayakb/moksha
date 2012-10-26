<?php

/**
 * Static files management screens
 *
 * Allows you to add/remove stylesheets and scripts to your page
 *
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Files extends CI_Controller {

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

		$this->load->model('site_admin/files_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Static files management screen
	 *
	 * @access	public
	 */
	public function manage($page = 1)
	{
		// Initialize pagination
		$this->pagination->initialize(
			array_merge($this->config->item('pagination'), array(
				'base_url'		=> base_url('admin/files/manage'),
				'total_rows'	=> $this->files_model->count_files(),
				'uri_segment'	=> 4
			))
		);
		
		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('styles_scripts_exp'),
			'files'			=> $this->files_model->fetch_files($page),
			'pagination'	=> $this->pagination->create_links()
		);

		// Load the view
		$this->template->load('site_admin/files_manage', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new user stylesheet/script to the site
	 *
	 * @access	public
	 */
	public function add()
	{
		if ($this->form_validation->run('site_admin/files'))
		{
			if ($this->files_model->add_file())
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('file_added'));
				redirect(base_url('admin/files/manage'));
			}
			else
			{
				$upload_errors = $this->upload->display_errors();

				if (empty($upload_errors))
				{
					$this->template->error_msgs = $this->lang->line('file_add_error');
				}
				else
				{
					$this->template->error_msgs = $upload_errors;
				}
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('site_adm'),
			'page_desc'		=> $this->lang->line('styles_scripts_exp'),
			'types'			=> $this->files_model->fetch_file_types(),
			'file_type'		=> set_value('file_type')
		);

		// Load the view
		$this->template->load('site_admin/files_editor', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a stylesheet/script from the database
	 *
	 * @access	public
	 * @param	int		file id to delete
	 */
	public function delete($file_id)
	{
		if ($this->template->confirm_box('lang:file_del_confirm'))
		{
			if ($this->files_model->delete_file($file_id))
			{
				$this->session->set_flashdata('success_msg', $this->lang->line('file_deleted'));
			}
			else
			{
				$this->session->set_flashdata('error_msg', $this->lang->line('file_del_error'));
			}
		}

		redirect(base_url('admin/files/manage'));
	}

	// --------------------------------------------------------------------

	/**
	 * Checks if a file was selected for upload
	 *
	 * @access	public
	 * @return	bool	true if valid
	 */
	public function check_file()
	{
		if ( ! isset($_FILES['file']))
		{
			$this->form_validation->set_message('check_file', $this->lang->line('select_file'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Validate the selected file type
	 *
	 * @access	public
	 * @return	bool	true if valid
	 */
	public function check_file_type($type)
	{
		$valid_types = $this->files_model->fetch_file_types();

		if ( ! isset($valid_types[$type]))
		{
			$this->form_validation->set_message('check_file_type', $this->lang->line('invalid_file_type'));
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
