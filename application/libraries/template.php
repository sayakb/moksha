<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Template Library
 *
 * This class exposes a custom template load method that auto-includes the
 * page header and footer
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Template {

	var $CI;
	var $template_defaults;
	var $error_msgs;
	var $success_msgs;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
		$this->template_defaults = $this->template_defaults();

		$this->error_msgs = NULL;
		$this->success_msgs = NULL;
	}

	/**
	 * Default template key initializer
	 *
	 * Assigns default values for common template data
	 *
	 * @access	private
	 */
	private function template_defaults()
	{
		return array(
			'page_title'		=> $this->CI->lang->line('moksha'),
			'page_desc'			=> $this->CI->lang->line('moksha_desc'),
			'page_text_dir'		=> $this->CI->config->item('text_direction'),
			'page_lang'			=> $this->CI->config->item('language'),
			'page_charset'		=> $this->CI->config->item('charset'),
			'page_copyright'	=> $this->CI->lang->line('default_copyright'),
			'page_notice'		=> NULL,
			'page_menu'			=> NULL,
		);
	}

	/**
	 * Load the header template
	 *
	 * @access	public
	 * @param	category Category of the template (admin/central)
	 * @param	template Template to be loaded
	 * @param	data Data to be passed to the template
	 * @param	output Output the template as return value
	 * @return	Parsed template, if $output is set to TRUE
	 */
	public function admin($category, $template, $data = array(), $output = FALSE)
	{
		$output = '';

		// User can pass data as empty string
		if ( ! is_array($data))
		{
			$data = $this->template_defaults;
		}
		else
		{
			$data = array_merge($this->template_defaults, $data);
		}

		// Get validation errors
		$validation_msgs = validation_errors();

		// Read error and success messages from session
		$error_flash = $this->CI->session->flashdata('error_msg');
		$success_flash = $this->CI->session->flashdata('success_msg');

		// Override local error messages with validation/session messages
		if ( ! empty($validation_msgs))
		{
			$this->error_msgs = $validation_msgs;
		}
		else if ( ! empty($error_flash))
		{
			$this->error_msgs = $error_flash;
		}
		else if ( ! empty($success_flash))
		{
			$this->success_msgs = $success_flash;
		}

		// Show error first, and if there are none, show success messages
		if ( ! empty($this->error_msgs))
		{
			$data['page_notice'] = array(
				'type'		=> 'error',
				'message'	=> $this->error_msgs
			);
		}
		else if ( ! empty($this->success_msgs))
		{
			$data['page_notice'] = array(
				'type'		=> 'success',
				'message'	=> $this->success_msgs
			);
		}

		// We assume that output is being returned
		$output .= $this->CI->load->view("{$category}/header", $data, $output);
		$output .= $this->CI->load->view("{$category}/{$template}", $data, $output);
		$output .= $this->CI->load->view("{$category}/footer", $data, $output);

		return $output;
	}
}
// END Template class

/* End of file template.php */
/* Location: ./application/libraries/template.php */