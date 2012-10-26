<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Condition resource loading
| -------------------------------------------------------------------------
| This file defines the resources that will be conditionally loaded
| for certain pages only
|
*/

$config['conditional'] = array(
	'admin' => array(
		'libraries' => array(
			'admin_log',
			'encrypt'
		),
		'helpers' => array(
			'array',
			'file',
			'form',
			'download'
		),
		'languages' => array(
			'auth',
			'admin_log',
			'calendar',
			'central_admin',
			'site_admin'
		)
	),

	'install' => array(
		'languages' => array(
			'install'
		)
	),

	'login' => array(
		'languages' => array(
			'auth'
		)
	),

	'register' => array(
		'languages' => array(
			'auth'
		)
	),
);


/* End of file conditionals.php */
/* Location: ./application/config/conditionals.php */