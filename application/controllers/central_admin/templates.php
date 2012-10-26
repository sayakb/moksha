<?php

/**
 * Central administration site templates
 *
 * Import/export a site templates
 * 
 * @package		Moksha
 * @category	Administration
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Templates extends CI_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		if ( ! check_roles(ROLE_ADMIN))
		{
			redirect('admin/central/login');
		}

		$this->load->model('central_admin/templates_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Central administration site templates
	 *
	 * @access	public
	 */
	public function index()
	{
		// Template export operation
		if (isset($_POST['export']))
		{
			if ($this->form_validation->run('central_admin/export_template'))
			{
				$this->templates_model->export_template();
			}
		}

		// Template import operation
		if (isset($_POST['import']))
		{
			if ($this->form_validation->run('central_admin/import_template'))
			{
				if ($this->templates_model->import_template())
				{
					$this->session->set_flashdata('success_msg', $this->lang->line('import_success'));
					redirect(base_url('admin/central/templates'));
				}
				else
				{
					$errors = $this->upload->display_errors();
					$this->template->error_msgs = empty($errors) ? $this->lang->line('import_error') : $errors;
				}
			}
		}

		// Assign view data
		$data = array(
			'page_title'	=> $this->lang->line('central_adm'),
			'page_desc'		=> $this->lang->line('site_templates_exp'),
			'sites'			=> $this->templates_model->fetch_sites()
		);

		// Load the view
		$this->template->load('central_admin/site_templates', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Validates selected site
	 *
	 * @access	public
	 * @param	int		site ID to validate
	 * @return	bool	true if valid
	 */
	public function check_site($site_id)
	{
		if ( ! $this->templates_model->check_site_exists($site_id))
		{
			$this->form_validation->set_message('check_site', $this->lang->line('invalid_site'));
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