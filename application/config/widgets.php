<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| List of controls
| -------------------------------------------------------------------------
| Defines all the controls and their properties that are available
| for adding to widgets
|
*/
$config['widgets']['controls'] = array(
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
	'ordered_list' => (object)array(
		'icon'	=> 'list',
		'label'	=> 'field_ordered_list',
	),
	'unordered_list' => (object)array(
		'icon'	=> 'list',
		'label'	=> 'field_unordered_list',
	),
	'pagination' => (object)array(
		'icon'	=> 'pagination',
		'label'	=> 'field_pagination',
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
	'file_upload' => (object)array(
		'icon'	=> 'file-upload',
		'label'	=> 'field_file_upload',
	),
	'file_download' => (object)array(
		'icon'	=> 'file-download',
		'label'	=> 'field_file_download',
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

/*
| -------------------------------------------------------------------------
| Form validations
| -------------------------------------------------------------------------
| Lists CI validations to be executed on widget form submit
|
*/
$config['widgets']['validations'] = array(
	'required',
	'is_unique',
	'alpha',
	'numeric',
	'alpha_numeric',
	'valid_email'
);

/*
| -------------------------------------------------------------------------
| Expressions
| -------------------------------------------------------------------------
| Defines control path expressions and their replacements
|
*/
$config['widgets']['expressions'] = array(
	'url'		=> '$this->CI->uri->segment($value, "")',
	'hub'		=> 'isset($row->$value) ? $row->$value : ""',
	'get'		=> '$this->CI->input->get($value)',
	'post'		=> '$this->CI->input->post($value)',
	'cookie'	=> '$this->CI->input->cookie($value)',
	'server'	=> '$this->CI->input->server(strtoupper($value))',
	'user'		=> 'user_data($value)',
	'calc'		=> 'eval("return $value;")'
);


/* End of file controls.php */
/* Location: ./application/config/controls.php */