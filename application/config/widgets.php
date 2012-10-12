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
		'label'	=> 'field_paragraph'
	),
	'heading_big' => (object)array(
		'icon'	=> 'heading',
		'label'	=> 'field_heading_big'
	),
	'heading_normal' => (object)array(
		'icon'	=> 'heading',
		'label'	=> 'field_heading_normal'
	),
	'heading_small' => (object)array(
		'icon'	=> 'heading',
		'label'	=> 'field_heading_small'
	),
	'hyperlink' => (object)array(
		'icon'	=> 'hyperlink',
		'label'	=> 'field_hyperlink'
	),
	'ordered_list' => (object)array(
		'icon'	=> 'list',
		'label'	=> 'field_ordered_list'
	),
	'unordered_list' => (object)array(
		'icon'	=> 'list',
		'label'	=> 'field_unordered_list'
	),
	'pagination' => (object)array(
		'icon'	=> 'pagination',
		'label'	=> 'field_pagination'
	),
	'textbox' => (object)array(
		'icon'	=> 'textbox',
		'label'	=> 'field_textbox'
	),
	'password' => (object)array(
		'icon'	=> 'password',
		'label'	=> 'field_password'
	),
	'textarea' => (object)array(
		'icon'	=> 'textarea',
		'label'	=> 'field_textarea'
	),
	'wysiwyg' => (object)array(
		'icon'	=> 'wysiwyg',
		'label'	=> 'field_wysiwyg'
	),
	'codebox' => (object)array(
		'icon'	=> 'codebox',
		'label'	=> 'field_codebox'
	),
	'checkbox' => (object)array(
		'icon'	=> 'checkbox',
		'label'	=> 'field_checkbox'
	),
	'radio' => (object)array(
		'icon'	=> 'radio',
		'label'	=> 'field_radio'
	),
	'notice' => (object)array(
		'icon'	=> 'notice',
		'label'	=> 'field_notice'
	),
	'file_upload' => (object)array(
		'icon'	=> 'file-upload',
		'label'	=> 'field_file_upload'
	),
	'file_download' => (object)array(
		'icon'	=> 'file-download',
		'label'	=> 'field_file_download'
	),
	'hidden' => (object)array(
		'icon'	=> 'hidden',
		'label'	=> 'field_hidden'
	),
	'image' => (object)array(
		'icon'	=> 'image',
		'label'	=> 'field_image'
	),
	'select' => (object)array(
		'icon'	=> 'select',
		'label'	=> 'field_select'
	),
	'multiselect' => (object)array(
		'icon'	=> 'multiselect',
		'label'	=> 'field_multiselect'
	),
	'submit_button' => (object)array(
		'icon'	=> 'button',
		'label'	=> 'field_submit_button'
	),
	'reset_button' => (object)array(
		'icon'	=> 'button',
		'label'	=> 'field_reset_button'
	)
);

/*
| -------------------------------------------------------------------------
| Submit control
| -------------------------------------------------------------------------
| Defines the key for the submit button
|
 */
$config['widgets']['submit'] = 'submit_button';

/*
| -------------------------------------------------------------------------
| Notice control
| -------------------------------------------------------------------------
| Defines the key for the page notice control
|
 */
$config['widgets']['notice'] = 'notice';

/*
| -------------------------------------------------------------------------
| Form validations
| -------------------------------------------------------------------------
| Lists CI validations to be executed on widget form submit
|
*/
$config['widgets']['validations'] = array(
	'required',
	'alpha',
	'numeric',
	'alpha_numeric',
	'valid_email'
);


/* End of file widgets.php */
/* Location: ./application/config/widgets.php */