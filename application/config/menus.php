<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Menus
| -------------------------------------------------------------------------
| This file defines menu items used across the board
|
*/

$config['menus']['central'] = array(
	'welcome'		=> array(
		'url'		=> 'admin/central',
		'label'		=> 'homepage',
	),

	'manage_sites'	=> array(
		'url'		=> 'admin/central/sites',
		'label'		=> 'manage_sites',
	),
);


/* End of file hooks.php */
/* Location: ./application/config/hooks.php */