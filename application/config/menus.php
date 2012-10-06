<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Menus
| -------------------------------------------------------------------------
| This file defines menu items used across the board
|
*/

$config['menus']['central_admin'] = array(
	'welcome'	=> array(
		'url'	=> 'admin/central',
		'label'	=> 'homepage'
	),

	'sites'		=> array(
		'url'	=> 'admin/central/sites/manage',
		'label'	=> 'manage_sites'
	),

	'users'		=> array(
		'url'	=> 'admin/central/users/manage',
		'label'	=> 'manage_users'
	),
);

$config['menus']['site_admin'] = array(
	'welcome'	=> array(
		'url'	=> 'admin',
		'label'	=> 'homepage'
	),

	'hubs'		=> array(
		'url'	=> 'admin/hubs/manage',
		'label'	=> 'manage_hubs'
	),

	'widgets'	=> array(
		'url'	=> 'admin/widgets/manage',
		'label'	=> 'manage_widgets'
	),

	'pages'	=> array(
		'url'	=> 'admin/pages/manage',
		'label'	=> 'manage_pages'
	),

	'users'	=> array(
		'url'	=> 'admin/users/manage',
		'label'	=> 'manage_users'
	),

	'roles'	=> array(
		'url'	=> 'admin/roles/manage',
		'label'	=> 'manage_roles'
	),
);


/* End of file menus.php */
/* Location: ./application/config/menus.php */