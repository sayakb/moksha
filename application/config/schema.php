<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Schemas
| -------------------------------------------------------------------------
| This file lets you define the schemas for all Moksha tables.
|
| The central tables are created only once by the installer, while
| the site tables are auto-generated whenever a new site is created
|
|  - For fields,use CI style schema
|  - For keys,use column => flag (TRUE for a primary key) pattern
|  - Multi-column keys should be separated by a comma: column1,column2 => flag
|
*/

$config['schema']['central_sessions'] = array(
	'fields' => array(
		'session_id' => array(
			'type'			=> 'VARCHAR',
			'constraint'	=> 40,
			'null'			=> FALSE,
			'default'		=> '0'
		),
		'ip_address' => array(
			'type'			=> 'VARCHAR',
			'constraint'	=> 45,
			'null'			=> FALSE,
			'default'		=> '0'
		),
		'user_agent' => array(
			'type'			=> 'VARCHAR',
			'constraint'	=> 120,
			'null'			=> FALSE
		),
		'last_activity' => array(
			'type'			=> 'INT',
			'constraint'	=> 10,
			'unsigned'		=> TRUE,
			'null'			=> FALSE,
			'default'		=> '0'
		),
		'user_data'			=> array(
			'type'			=> 'TEXT',
			'null'			=> FALSE
		)
	),
	'keys' => array(
		'session_id'		=> TRUE,
		'last_activity'		=> FALSE
	)
);

$config['schema']['central_sites'] = array(
	'fields' => array(
		'site_id' => array(
			'type'				=> 'BIGINT',
			'constraint'		=> 20,
			'null'				=> FALSE,
			'auto_increment'	=> TRUE
		),
		'site_url' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 255,
			'null'				=> FALSE
		)
	),
	'keys' => array(
		'site_id'				=> TRUE,
		'site_url'				=> FALSE
	)
);

$config['schema']['central_users'] = array(
	'fields' => array(
		'user_id' => array(
			'type'				=> 'BIGINT',
			'constraint'		=> 20,
			'null'				=> FALSE,
			'auto_increment'	=> TRUE
		),
		'user_name' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 100,
			'null'				=> FALSE
		),
		'password' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 128,
			'null'				=> FALSE
		),
		'email_address' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 225,
			'null'				=> FALSE
		),
		'founder' => array(
			'type'				=> 'TINYINT',
			'constraint'		=> 1,
			'null'				=> FALSE,
			'default'			=> 0
		)
	),
	'keys' => array(
		'user_id'				=> TRUE,
		'user_name,password'	=> FALSE
	)
);

$config['schema']['central_logs'] = array(
	'fields' => array(
		'site_id' => array(
			'type'			=> 'BIGINT',
			'constraint'	=> 20,
			'null'			=> FALSE
		),
		'message' => array(
			'type'			=> 'TEXT',
			'null'			=> FALSE
		),
		'log_time' => array(
			'type'			=> 'INT',
			'constraint'	=> 10,
			'unsigned'		=> TRUE,
			'null'			=> FALSE,
			'default'		=> '0'
		)
	)
);

$config['schema']['site_sessions'] = array(
	'fields' => array(
		'session_id' => array(
			'type'			=> 'VARCHAR',
			'constraint'	=> 40,
			'null'			=> FALSE,
			'default'		=> '0'
		),
		'ip_address' => array(
			'type'			=> 'VARCHAR',
			'constraint'	=> 45,
			'null'			=> FALSE,
			'default'		=> '0'
		),
		'user_agent' => array(
			'type'			=> 'VARCHAR',
			'constraint'	=> 120,
			'null'			=> FALSE
		),
		'last_activity' => array(
			'type'			=> 'INT',
			'constraint'	=> 10,
			'unsigned'		=> TRUE,
			'null'			=> FALSE,
			'default'		=> '0'
		),
		'user_data'			=> array(
			'type'			=> 'TEXT',
			'null'			=> FALSE
		)
	),
	'keys' => array(
		'session_id'		=> TRUE,
		'last_activity'		=> FALSE
	)
);

$config['schema']['site_hubs'] = array(
	'fields' => array(
		'hub_id' => array(
			'type'				=> 'BIGINT',
			'constraint'		=> 20,
			'null'				=> FALSE,
			'auto_increment'	=> TRUE
		),
		'hub_name' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 100,
			'null'				=> FALSE
		),
		'driver' => array(
			'type'				=> 'CHAR',
			'constraint'		=> 5,
			'null'				=> FALSE
		),
		'source' => array(
			'type'				=> 'TEXT',
			'null'				=> TRUE
		)
	),
	'keys' => array(
		'hub_id'				=> TRUE,
		'hub_name'				=> FALSE
	)
);

$config['schema']['site_users'] = array(
	'fields' => array(
		'user_id' => array(
			'type'				=> 'BIGINT',
			'constraint'		=> 20,
			'null'				=> FALSE,
			'auto_increment'	=> TRUE
		),
		'user_name' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 100,
			'null'				=> FALSE
		),
		'password' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 128,
			'null'				=> FALSE
		),
		'email_address' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 225,
			'null'				=> FALSE
		),
		'roles' => array(
			'type'				=> 'TEXT'
		),
		'active' => array(
			'type'				=> 'TINYINT',
			'constraint'		=> 1,
			'null'				=> FALSE,
			'default'			=> 1
		),
		'founder' => array(
			'type'				=> 'TINYINT',
			'constraint'		=> 1,
			'null'				=> FALSE,
			'default'			=> 0
		)
	),
	'keys' => array(
		'user_id'				=> TRUE,
		'user_name,password'	=> FALSE
	)
);

$config['schema']['site_captcha'] = array(
	'fields' => array(
		'captcha_id' => array(
			'type'				=> 'BIGINT',
			'constraint'		=> 20,
			'null'				=> FALSE,
			'auto_increment'	=> TRUE
		),
		'captcha_time' => array(
			'type'				=> 'INT',
			'constraint'		=> 10,
			'unsigned'			=> TRUE,
			'null'				=> FALSE
		),
		'ip_address' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 16,
			'null'				=> FALSE,
			'default'			=> '0'
		),
		'word' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 20,
			'null'				=> FALSE
		)
	),
	'keys' => array(
		'captcha_id'			=> TRUE,
		'word'					=> FALSE
	)
);

$config['schema']['site_roles'] = array(
	'fields' => array(
		'role_id' => array(
			'type'				=> 'BIGINT',
			'constraint'		=> 20,
			'null'				=> FALSE,
			'auto_increment'	=> TRUE
		),
		'role_name' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 100,
			'null'				=> FALSE
		)
	),
	'keys' => array(
		'role_id'				=> TRUE
	)
);

$config['schema']['site_widgets'] = array(
	'fields' => array(
		'widget_id' => array(
			'type'				=> 'BIGINT',
			'constraint'		=> 20,
			'null'				=> FALSE,
			'auto_increment'	=> TRUE
		),
		'widget_name' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 100,
			'null'				=> FALSE
		),
		'access_roles' => array(
			'type'				=> 'TEXT',
			'null'				=> FALSE
		),
		'password_path' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 64
		),
		'frameless' => array(
			'type'				=> 'TINYINT',
			'constraint'		=> 1,
			'null'				=> FALSE,
			'default'			=> 0
		),
		'update_key' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 100
		),
		'empty_tpl' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 255
		),
		'widget_data' => array(
			'type'				=> 'TEXT',
			'null'				=> FALSE
		)
	),
	'keys' => array(
		'widget_id'				=> TRUE
	)
);

$config['schema']['site_pages'] = array(
	'fields' => array(
		'page_id' => array(
			'type'				=> 'BIGINT',
			'constraint'		=> 20,
			'null'				=> FALSE,
			'auto_increment'	=> TRUE
		),
		'page_title' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 100,
			'null'				=> FALSE
		),
		'page_url' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 255,
			'null'				=> FALSE
		),
		'page_layout' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 5,
			'null'				=> FALSE
		),
		'success_url' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 255,
			'null'				=> FALSE
		),
		'error_url' => array(
			'type'				=> 'VARCHAR',
			'constraint'		=> 255,
			'null'				=> FALSE
		),
		'access_roles' => array(
			'type'				=> 'TEXT',
			'null'				=> FALSE
		),
		'widgets' => array(
			'type'				=> 'TEXT',
			'null'				=> FALSE
		),
		'access_count' => array(
			'type'				=> 'BIGINT',
			'constraint'		=> 15,
			'null'				=> FALSE,
			'default'			=> 0
		)
	),
	'keys' => array(
		'page_id'				=> TRUE
	)
);

$config['schema']['site_files'] = array(
	'fields' => array(
		'file_id' => array(
			'type'				=> 'BIGINT',
			'constraint'		=> 20,
			'null'				=> FALSE,
			'auto_increment'	=> TRUE
		),
		'file_name' => array(
			'type'				=> 'TEXT',
			'null'				=> FALSE
		),
		'file_type' => array(
			'type'				=> 'CHAR',
			'constraint'		=> 3,
			'null'				=> FALSE
		),
		'relative_path' => array(
			'type'				=> 'TEXT',
			'null'				=> FALSE
		)
	),
	'keys' => array(
		'file_id'				=> TRUE
	)
);

$config['schema']['site_config'] = array(
	'fields' => array(
		'key' => array(
			'type'			=> 'VARCHAR',
			'constraint'	=> 20,
			'null'			=> FALSE
		),
		'value' => array(
			'type'			=> 'TEXT',
			'null'			=> FALSE
		)
	),
	'keys' => array(
		'key'				=> TRUE
	)
);

$config['schema']['site_stats'] = array(
	'fields' => array(
		'year' => array(
			'type'			=> 'INT',
			'constraint'	=> 4,
			'null'			=> FALSE
		),
		'month' => array(
			'type'			=> 'INT',
			'constraint'	=> 2,
			'null'			=> FALSE
		),
		'visitors' => array(
			'type'			=> 'BIGINT',
			'constraint'	=> 15,
			'null'			=> FALSE,
			'default'		=> 0
		)
	),
	'keys' => array(
		'year,month'		=> TRUE
	)
);


/* End of file schemas.php */
/* Location: ./application/config/schemas.php */