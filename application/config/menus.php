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
	'templates'	=> array(
		'url'	=> 'admin/central/templates',
		'label'	=> 'site_templates'
	),
	'logs'		=> array(
		'url'	=> 'admin/central/logs/view',
		'label'	=> 'admin_logs'
	)
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
	'pages'		=> array(
		'url'	=> 'admin/pages/manage',
		'label'	=> 'manage_pages'
	),
	'users'		=> array(
		'url'	=> 'admin/users/manage',
		'label'	=> 'manage_users'
	),
	'roles'		=> array(
		'url'	=> 'admin/roles/manage',
		'label'	=> 'manage_roles'
	),
	'files'		=> array(
		'url'	=> 'admin/files/manage',
		'label'	=> 'styles_scripts'
	),
	'config'	=> array(
		'url'	=> 'admin/config',
		'label'	=> 'site_config'
	)
);


/* End of file menus.php */
/* Location: ./application/config/menus.php */