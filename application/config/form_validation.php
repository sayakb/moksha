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
			'field' =>	'username',
			'label' =>	'lang:username',
			'rules' =>	'required|' .
						'min_length[3]|' .
						'max_length[100]'
		),
		array(
			'field' =>	'password',
			'label' =>	'lang:password',
			'rules' =>	'required'
		)
	),

	// Central: add site
	'central_admin/sites/add' => array(
		array(
			'field' =>	'site_url',
			'label' =>	'lang:site_url',
			'rules' =>	'required|' .
						'max_length[255]|' .
						'is_unique[sites.site_url]|' .
						'trim|' .
						'htmlspecialchars'
		),
		array(
			'field' =>	'site_slug',
			'label' =>	'lang:site_slug',
			'rules' =>	'required|' .
						'min_length[5]|' .
						'max_length[20]|' .
						'is_unique[sites.site_slug]|' .
						'alpha_numeric|' .
						'trim'
		)
	),

	// Central: edit site
	'central_admin/sites/edit' => array(
		array(
			'field' =>	'site_url',
			'label' =>	'lang:site_url',
			'rules' =>	'required|' .
						'max_length[255]|' .
						'is_unique[sites.site_url]|' .
						'trim|' .
						'htmlspecialchars'
		)
	),

	// Central: add user
	'central_admin/users/add' => array(
		array(
			'field' =>	'username',
			'label' =>	'lang:username',
			'rules' =>	'required|' .
						'min_length[3]|' .
						'max_length[100]|' .
						'is_unique[users.user_name]|' .
						'alpha_dash'
		),
		array(
			'field' =>	'password',
			'label' =>	'lang:password',
			'rules' =>	'required|' .
						'matches[confirm_password]'
		),
		array(
			'field' =>	'confirm_password',
			'label' =>	'lang:confirm_password',
			'rules' =>	'required'
		),
		array(
			'field' =>	'email',
			'label' =>	'lang:email_address',
			'rules' =>	'required|' .
						'valid_email|' .
						'is_unique[users.user_email]'
		),
	),

	// Central: edit user
	'central_admin/users/edit' => array(
		array(
			'field' =>	'username',
			'label' =>	'lang:username',
			'rules' =>	'required|' .
						'min_length[3]|' .
						'max_length[100]|' .
						'alpha_dash|' .
						'is_unique[users.user_name]'
		),
		array(
			'field' =>	'password',
			'label' =>	'lang:password',
			'rules' =>	'matches[confirm_password]'
		),
		array(
			'field' =>	'confirm_password',
			'label' =>	'lang:confirm_password'
		),
		array(
			'field' =>	'email',
			'label' =>	'lang:email_address',
			'rules' =>	'required|' .
						'valid_email|' .
						'is_unique[users.user_email]'
		),
	),
);


/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */