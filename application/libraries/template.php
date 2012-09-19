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
			'page_copyright'	=> $this->CI->lang->line('default_copyright'),
			'page_notice'		=> NULL,
			'page_menu'			=> NULL,
		);
	}

	/**
	 * Load the page template
	 *
	 * @access	public
	 * @param	controller Controller for the template
	 * @param	template Template to be loaded
	 * @param	data Data to be passed to the template
	 * @param	output Output the template as return value
	 * @return	Parsed template, if $output is set to TRUE
	 */
	public function load($controller, $template, $data = array(), $output = FALSE)
	{
		$parsed = '';

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
		$parsed .= $this->CI->load->view("common/header", $data, $output);

		if ($controller != 'common')
		{
			$parsed .= $this->CI->load->view("{$controller}/wrapper_top", $data, $output);
		}

		$parsed .= $this->CI->load->view("{$controller}/{$template}", $data, $output);

		if ($controller != 'common')
		{
			$parsed .= $this->CI->load->view("{$controller}/wrapper_bottom", $data, $output);
		}

		$parsed .= $this->CI->load->view("common/footer", $data, $output);

		return $parsed;
	}
}
// END Template class

/* End of file template.php */
/* Location: ./application/libraries/template.php */