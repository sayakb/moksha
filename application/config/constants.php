<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Hub data driver names
|--------------------------------------------------------------------------
|
| These define the different data drivers supported by the Hub library
|
 */
define('HUB_DATABASE',		'db');
define('HUB_RSS',			'rss');

/*
|--------------------------------------------------------------------------
| Hub data types
|--------------------------------------------------------------------------
|
| These define the different data types supported by the hub DB driver
|
 */
define('DBTYPE_NONE',		'none');
define('DBTYPE_KEY',		'key');
define('DBTYPE_INT',		'int');
define('DBTYPE_TEXT',		'text');
define('DBTYPE_PASSWORD',	'password');
define('DBTYPE_DATETIME',	'datetime');

/*
|--------------------------------------------------------------------------
| User roles
|--------------------------------------------------------------------------
|
| These define the different system level user roles
|
 */
define('ROLE_ADMIN',		'0');
define('ROLE_AUTHOR',		'-1');
define('ROLE_LOGGED_IN',	'-2');


/* End of file constants.php */
/* Location: ./application/config/constants.php */