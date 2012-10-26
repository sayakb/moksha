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

/*
| -------------------------------------------------------------------------
| Temporary files upload
| -------------------------------------------------------------------------
| Defines temporary files that will be deleted after reading
|
 */
$config['upload']['temp']['upload_path']	= 'assets/dynamic/files/';
$config['upload']['temp']['allowed_types']	= '*';
$config['upload']['temp']['file_name']		= '__site_tpl_file.tpl';
$config['upload']['temp']['overwrite']		= TRUE;


/* End of file upload.php */
/* Location: ./application/config/upload.php */