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

	'controls'	=> array(
		'url'	=> 'admin/controls/manage',
		'label'	=> 'manage_controls'
	),

	'hubs'		=> array(
		'url'	=> 'admin/hubs/manage',
		'label'	=> 'manage_hubs'
	),
);


/* End of file menus.php */
/* Location: ./application/config/menus.php */