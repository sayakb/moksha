<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| File upload for sites
| -------------------------------------------------------------------------
| Defines file upload settings for all sites
|
*/
$config['upload']['site']['upload_path']	= 'assets/dynamic/files/';
$config['upload']['site']['allowed_types']	= '*';
$config['upload']['site']['encrypt_name']	= TRUE;

/*
| -------------------------------------------------------------------------
| CSS/script upload
| -------------------------------------------------------------------------
| Defines static file upload for admin pages
|
*/
$config['upload']['admin']['upload_path']	= 'assets/dynamic/';
$config['upload']['admin']['allowed_types']	= '*';
$config['upload']['admin']['encrypt_name']	= TRUE;


/* End of file upload.php */
/* Location: ./application/config/upload.php */