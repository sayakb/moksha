<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Control Library for Moksha
 *
 * This class handles actions related to site controls
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Control {

	var $CI;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a list of controls
	 *
	 * @access	public
	 * @return	array	control list
	 */
	public function fetch_controls()
	{
		return array(
			'paragraph' => (object)array(
				'icon'	=> 'paragraph',
				'label'	=> 'field_paragraph',
			),
			'heading_big' => (object)array(
				'icon'	=> 'heading',
				'label'	=> 'field_heading_big',
			),
			'heading_normal' => (object)array(
				'icon'	=> 'heading',
				'label'	=> 'field_heading_normal',
			),
			'heading_small' => (object)array(
				'icon'	=> 'heading',
				'label'	=> 'field_heading_small',
			),
			'hyperlink' => (object)array(
				'icon'	=> 'hyperlink',
				'label'	=> 'field_hyperlink',
			),
			'textbox' => (object)array(
				'icon'	=> 'textbox',
				'label'	=> 'field_textbox',
			),
			'password' => (object)array(
				'icon'	=> 'password',
				'label'	=> 'field_password',
			),
			'textarea' => (object)array(
				'icon'	=> 'textarea',
				'label'	=> 'field_textarea',
			),
			'wysiwyg' => (object)array(
				'icon'	=> 'wysiwyg',
				'label'	=> 'field_wysiwyg',
			),
			'codebox' => (object)array(
				'icon'	=> 'codebox',
				'label'	=> 'field_codebox',
			),
			'checkbox' => (object)array(
				'icon'	=> 'checkbox',
				'label'	=> 'field_checkbox',
			),
			'radio' => (object)array(
				'icon'	=> 'radio',
				'label'	=> 'field_radio',
			),
			'submit_button' => (object)array(
				'icon'	=> 'button',
				'label'	=> 'field_submit_button',
			),
			'reset_button' => (object)array(
				'icon'	=> 'button',
				'label'	=> 'field_reset_button',
			),
			'file' => (object)array(
				'icon'	=> 'file',
				'label'	=> 'field_file',
			),
			'hidden' => (object)array(
				'icon'	=> 'hidden',
				'label'	=> 'field_hidden',
			),
			'image' => (object)array(
				'icon'	=> 'image',
				'label'	=> 'field_image',
			),
			'select' => (object)array(
				'icon'	=> 'select',
				'label'	=> 'field_select',
			),
			'multiselect' => (object)array(
				'icon'	=> 'multiselect',
				'label'	=> 'field_multiselect',
			)
		);
	}

	// --------------------------------------------------------------------
}
// END Control class

/* End of file Control.php */
/* Location: ./application/libraries/Control.php */