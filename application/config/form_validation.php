<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Form validation
| -------------------------------------------------------------------------
| Defines validation rules for all controller paths
|
*/

$config = array(
	// Login pages
	'user/auth/login' => array(
		array(
			'field'	=>	'username',
			'label'	=>	'lang:username',
			'rules'	=>	'required|'.
						'min_length[3]|'.
						'max_length[100]'
		),
		array(
			'field'	=>	'password',
			'label'	=>	'lang:password',
			'rules'	=>	'required'
		)
	),

	// Central: add site
	'central_admin/sites/add' => array(
		array(
			'field'	=>	'site_url',
			'label'	=>	'lang:site_url',
			'rules'	=>	'required|'.
						'max_length[255]|'.
						'is_unique[central_sites.site_url]|'.
						'trim|'.
						'htmlspecialchars'
		)
	),

	// Central: edit site
	'central_admin/sites/edit' => array(
		array(
			'field'	=>	'site_url',
			'label'	=>	'lang:site_url',
			'rules'	=>	'required|'.
						'max_length[255]|'.
						'is_unique[central_sites.site_url]|'.
						'trim|'.
						'htmlspecialchars'
		)
	),

	// Central: add user
	'central_admin/users/add' => array(
		array(
			'field'	=>	'username',
			'label'	=>	'lang:username',
			'rules'	=>	'required|'.
						'min_length[3]|'.
						'max_length[100]|'.
						'is_unique[central_users.user_name]|'.
						'alpha_dash'
		),
		array(
			'field'	=>	'password',
			'label'	=>	'lang:password',
			'rules'	=>	'required|'.
						'matches[confirm_password]'
		),
		array(
			'field'	=>	'confirm_password',
			'label'	=>	'lang:confirm_password',
			'rules'	=>	'required'
		),
		array(
			'field'	=>	'email',
			'label'	=>	'lang:email_address',
			'rules'	=>	'required|'.
						'valid_email|'.
						'is_unique[central_users.user_email]'
		)
	),

	// Central: edit user
	'central_admin/users/edit' => array(
		array(
			'field'	=>	'username',
			'label'	=>	'lang:username',
			'rules'	=>	'required|'.
						'min_length[3]|'.
						'max_length[100]|'.
						'alpha_dash|'.
						'is_unique[central_users.user_name]'
		),
		array(
			'field'	=>	'password',
			'label'	=>	'lang:password',
			'rules'	=>	'matches[confirm_password]'
		),
		array(
			'field'	=>	'confirm_password',
			'label'	=>	'lang:confirm_password'
		),
		array(
			'field'	=>	'email',
			'label'	=>	'lang:email_address',
			'rules'	=>	'required|'.
						'valid_email|'.
						'is_unique[central_users.user_email]'
		)
	),

	// Site admin: add hub index page
	'site_admin/hubs/add/index' => array(
		array(
			'field'	=>	'hub_name',
			'label'	=>	'lang:hub_name',
			'rules'	=>	'required|'.
						'min_length[3]|'.
						'max_length[100]|'.
						'alpha_numeric|'.
						'strtolower|'.
						'is_unique[site_hubs.hub_name]'
		),
		array(
			'field'	=>	'hub_source',
			'label'	=>	'lang:hub_source',
			'rules'	=>	'callback_check_source'
		)
	),

	// Site admin: add hub columns page
	'site_admin/hubs/add/columns' => array(
		array(
			'field'	=>	'validation_key',
			'label'	=>	'lang:column_name',
			'rules'	=>	'callback_check_columns'
		),
		array(
			'field'	=>	'col_names[]',
			'label'	=>	'lang:column_name',
			'rules'	=>	'alpha'
		),
		array(
			'field'	=>	'col_datatypes[]',
			'label'	=>	'lang:data_type',
			'rules'	=>	'integer|'.
						'greater_than[-1]|'.
						'less_than[4]'
		),
	),
);


/* End of file form_validation.php */
/* Location:./application/config/form_validation.php */