<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Form validation
| -------------------------------------------------------------------------
| Defines validation rules for all controller paths
|
*/

$config = array(
	'user/auth/login' => array(
		array(
			'field' =>	'username',
			'label' =>	'lang:username',
			'rules' =>	'required|' .
						'max_length[100]|' .
						'xss_clean'
		),

		array(
			'field' =>	'password',
			'label' =>	'lang:password',
			'rules' =>	'required'
		),
	),

	'central_admin/sites/manage' => array(
		array(
			'field' =>	'site_url',
			'label' =>	'lang:site_url',
			'rules' =>	'required|' .
						'max_length[255]|' .
						'is_unique[sites.site_url]|' .
						'trim|' .
						'htmlspecialchars'
		)
	)
);


/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */