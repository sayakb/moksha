<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Form validation
| -------------------------------------------------------------------------
| Defines validation rules for all controller paths
|
*/

$config = array(
	// Installer
	'system/install' => array(
		array(
			'field'	=>	'hostname',
			'label'	=>	'lang:hostname',
			'rules'	=>	'required'
		),
		array(
			'field'	=>	'port',
			'label'	=>	'lang:port',
			'rules'	=>	'numeric'
		),
		array(
			'field'	=>	'username',
			'label'	=>	'lang:username',
			'rules'	=>	'trim'
		),
		array(
			'field'	=>	'db_name',
			'label'	=>	'lang:db_name',
			'rules'	=>	'required'
		),
		array(
			'field'	=>	'key',
			'label'	=>	'lang:install',
			'rules'	=>	'callback_check_config'
		)
	),

	// Login pages
	'user/auth/login' => array(
		array(
			'field'	=>	'username',
			'label'	=>	'lang:username',
			'rules'	=>	'required|'.
						'xss_clean'
		),
		array(
			'field'	=>	'password',
			'label'	=>	'lang:password',
			'rules'	=>	'required'
		)
	),

	// User registration
	'user/auth/register' => array(
		array(
			'field'	=>	'username',
			'label'	=>	'lang:username',
			'rules'	=>	'required|'.
						'min_length[3]|'.
						'max_length[100]|'.
						'trim|'.
						'is_unique[site_users.user_name]|'.
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
			'field'	=>	'email_address',
			'label'	=>	'lang:email_address',
			'rules'	=>	'required|'.
						'trim|'.
						'valid_email|'.
						'is_unique[site_users.email_address]'
		),
		array(
			'field'	=>	'captcha',
			'label'	=>	'lang:visual_verif',
			'rules'	=>	'callback_check_captcha'
		)
	),

	// Central: add/edit site
	'central_admin/sites' => array(
		array(
			'field'	=>	'site_url',
			'label'	=>	'lang:site_url',
			'rules'	=>	'required|'.
						'max_length[255]|'.
						'trim|'.
						'is_unique[central_sites.site_url]'
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
						'trim|'.
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
			'field'	=>	'email_address',
			'label'	=>	'lang:email_address',
			'rules'	=>	'required|'.
						'trim|'.
						'valid_email|'.
						'is_unique[central_users.email_address]'
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
			'field'	=>	'email_address',
			'label'	=>	'lang:email_address',
			'rules'	=>	'required|'.
						'valid_email|'.
						'is_unique[central_users.email_address]'
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
						'trim|'.
						'is_unique[site_hubs.hub_name]'
		),
		array(
			'field'	=>	'hub_type',
			'label'	=>	'lang:hub_type',
			'rules'	=>	'required'
		),
		array(
			'field'	=>	'source',
			'label'	=>	'lang:hub_source',
			'rules'	=>	'callback_check_source'
		)
	),

	// Site admin: add hub columns page
	'site_admin/hubs/add/columns' => array(
		array(
			'field'	=>	'validation_key',
			'label'	=>	'lang:column_name',
			'rules'	=>	'callback_check_column_add'
		),
		array(
			'field'	=>	'column_names[]',
			'label'	=>	'lang:column_name',
			'rules'	=>	'alpha_dash|'.
						'max_length[64]'
		),
		array(
			'field'	=>	'column_datatypes[]',
			'label'	=>	'lang:data_type',
			'rules'	=>	'callback_check_column_datatype'
		)
	),

	// Site admin: edit RSS hub
	'site_admin/hubs/edit/modify_hub' => array(
		array(
			'field'	=>	'hub_name',
			'label'	=>	'lang:hub_name',
			'rules'	=>	'required|'.
						'min_length[3]|'.
						'max_length[100]|'.
						'alpha_numeric|'.
						'trim|'.
						'is_unique[site_hubs.hub_name]'
		),
		array(
			'field'	=>	'source',
			'label'	=>	'lang:hub_source',
			'rules'	=>	'callback_check_source'
		)
	),

	// Site admin: edit DB hub -> rename hub
	'site_admin/hubs/edit/rename_hub' => array(
		array(
			'field'	=>	'hub_name',
			'label'	=>	'lang:hub_name',
			'rules'	=>	'required|'.
						'min_length[3]|'.
						'max_length[100]|'.
						'alpha_numeric|'.
						'trim|'.
						'is_unique[site_hubs.hub_name]'
		)
	),

	// Site admin: edit DB hub -> add column
	'site_admin/hubs/edit/add_column' => array(
		array(
			'field'	=>	'column_name',
			'label'	=>	'lang:column_name',
			'rules'	=>	'required|'.
						'alpha_dash|'.
						'max_length[64]|'.
						'trim|'.
						'callback_check_column_edit',
		),
		array(
			'field'	=>	'column_datatype',
			'label'	=>	'lang:data_type',
			'rules'	=>	'callback_check_column_datatype|'.
						'callback_check_disallow_unique'
		)
	),
	
	// Site admin: edit DB hub -> rename column
	'site_admin/hubs/edit/rename_column' => array(
		array(
			'field'	=>	'column_name_existing',
			'label'	=>	'lang:column_name',
			'rules'	=>	'callback_check_column_dropdown'
		),
		array(
			'field'	=>	'column_name',
			'label'	=>	'lang:column_name',
			'rules'	=>	'required|'.
						'alpha_dash|'.
						'max_length[64]|'.
						'trim|'.
						'callback_check_column_edit',
		)
	),

	// Site admin: edit DB hub -> delete column
	'site_admin/hubs/edit/delete_column' => array(
		array(
			'field'	=>	'column_name_existing',
			'label'	=>	'lang:column_name',
			'rules'	=>	'callback_check_column_dropdown|'.
						'callback_check_column_delete'
		)
	),

	// Site admin: add widget
	'site_admin/widgets/add' => array(
		array(
			'field'	=>	'widget_name',
			'label'	=>	'lang:widget_name',
			'rules'	=>	'required|'.
						'alpha_dash|'.
						'trim|'.
						'is_unique[site_widgets.widget_name]'
		),
		array(
			'field'	=>	'update_key',
			'label'	=>	'lang:update_key',
			'rules'	=>	'trim|'.
						'max_length[100]'
		),
		array(
			'field'	=>	'access_roles',
			'label'	=>	'lang:widget_accessible_by',
			'rules'	=>	'callback_check_roles'
		),
		array(
			'field'	=>	'frameless',
			'label'	=>	'lang:frameless',
			'rules'	=>	'trim'
		),
		array(
			'field'	=>	'empty_tpl',
			'label'	=>	'lang:empty_notice',
			'rules'	=>	'trim|'.
						'max_length[255]'
		),
		array(
			'field'	=>	'control_keys[]',
			'label'	=>	'lang:control_keys',
			'rules'	=>	'strip_tags'
		),
		array(
			'field'	=>	'control_classes[]',
			'label'	=>	'lang:control_classes',
			'rules'	=>	'strip_tags'
		),
		array(
			'field'	=>	'control_set_paths[]',
			'label'	=>	'lang:control_set_path',
			'rules'	=>	'trim|'.
						'callback_check_set_paths'
		),
		array(
			'field'	=>	'control_formats[]',
			'label'	=>	'lang:control_formats',
			'rules'	=>	'strip_tags'
		),
		array(
			'field'	=>	'control_roles[]',
			'label'	=>	'lang:roles',
			'rules'	=>	'callback_check_roles'
		),
		array(
			'field'	=>	'attached_hub',
			'label'	=>	'lang:attached_hub',
			'rules'	=>	'required|'.
						'trim|'.
						'callback_check_hub'
		),
		array(
			'field'	=>	'data_filters',
			'label'	=>	'lang:data_filters',
			'rules'	=>	'callback_check_filters'
		),
		array(
			'field'	=>	'order_by',
			'label'	=>	'lang:order_by',
			'rules'	=>	'callback_check_orderby'
		),
		array(
			'field'	=>	'max_records',
			'label'	=>	'lang:max_records',
			'rules'	=>	'trim'
		),
		array(
			'field'	=>	'binding',
			'label'	=>	'lang:binding_type',
			'rules'	=>	'trim'
		),
		array(
			'field'	=>	'submit',
			'label'	=>	'lang:controls_required',
			'rules'	=>	'callback_check_controls'
		)
	),

	// Site admin: add user
	'site_admin/users/add' => array(
		array(
			'field'	=>	'username',
			'label'	=>	'lang:username',
			'rules'	=>	'required|'.
						'min_length[3]|'.
						'max_length[100]|'.
						'trim|'.
						'is_unique[site_users.user_name]|'.
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
			'field'	=>	'email_address',
			'label'	=>	'lang:email_address',
			'rules'	=>	'required|'.
						'trim|'.
						'valid_email|'.
						'is_unique[site_users.email_address]'
		),
		array(
			'field'	=>	'roles',
			'label'	=>	'lang:roles',
			'rules'	=>	'callback_check_roles'
		)
	),

	// Site admin: edit user
	'site_admin/users/edit' => array(
		array(
			'field'	=>	'username',
			'label'	=>	'lang:username',
			'rules'	=>	'required|'.
						'min_length[3]|'.
						'max_length[100]|'.
						'alpha_dash|'.
						'is_unique[site_users.user_name]'
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
			'field'	=>	'email_address',
			'label'	=>	'lang:email_address',
			'rules'	=>	'required|'.
						'valid_email|'.
						'is_unique[site_users.email_address]'
		),
		array(
			'field'	=>	'roles',
			'label'	=>	'lang:roles',
			'rules'	=>	'callback_check_roles'
		)
	),

	// Site admin: add/edit roles
	'site_admin/roles' => array(
		array(
			'field'	=>	'role_name',
			'label'	=>	'lang:role_name',
			'rules'	=>	'required|'.
						'max_length[100]|'.
						'alpha|'.
						'trim|'.
						'is_unique[site_roles.role_name]'
		)
	),

	// Site admin: add/edit pages
	'site_admin/pages' => array(
		array(
			'field'	=>	'pg_title',
			'label'	=>	'lang:page_title',
			'rules'	=>	'required|'.
						'max_length[100]|'.
						'is_unique[site_pages.page_title]'
		),
		array(
			'field'	=>	'pg_url',
			'label'	=>	'lang:page_url',
			'rules'	=>	'required|'.
						'trim|'.
						'max_length[255]|'.
						'is_unique[site_pages.page_url]|'.
						'callback_check_url'
		),
		array(
			'field'	=>	'success_url',
			'label'	=>	'lang:success_url',
			'rules'	=>	'trim|'.
						'max_length[255]'
		),
		array(
			'field'	=>	'error_url',
			'label'	=>	'lang:error_url',
			'rules'	=>	'trim|'.
						'max_length[255]'
		),
		array(
			'field'	=>	'pg_layout',
			'label'	=>	'lang:page_layout',
			'rules'	=>	'callback_check_layout'
		),
		array(
			'field'	=>	'access_roles',
			'label'	=>	'lang:roles',
			'rules'	=>	'callback_check_roles'
		),
		array(
			'field'	=>	'add_page',
			'label'	=>	'lang:page',
			'rules'	=>	'callback_check_widgets'
		),
	),

	// Site admin: add files
	'site_admin/files' => array(
		array(
			'field'	=>	'file',
			'label'	=>	'lang:file',
			'rules'	=>	'callback_check_file'
		),
		array(
			'field'	=>	'file_type',
			'label'	=>	'lang:file_type',
			'rules'	=>	'callback_check_file_type'
		)
	),

	// Site admin: site configuration
	'site_admin/config' => array(
		array(
			'field'	=>	'status',
			'label'	=>	'lang:site_status',
			'rules'	=>	'callback_check_flag'
		),
		array(
			'field'	=>	'login',
			'label'	=>	'lang:user_login',
			'rules'	=>	'callback_check_flag'
		),
		array(
			'field'	=>	'registration',
			'label'	=>	'lang:user_registration',
			'rules'	=>	'callback_check_flag'
		),
		array(
			'field'	=>	'captcha',
			'label'	=>	'lang:registration_captcha',
			'rules'	=>	'callback_check_flag'
		),
		array(
			'field'	=>	'stats',
			'label'	=>	'lang:visitor_stats',
			'rules'	=>	'callback_check_flag'
		)
	)
);


/* End of file form_validation.php */
/* Location:./application/config/form_validation.php */