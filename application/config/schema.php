<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Schemas
| -------------------------------------------------------------------------
| This file lets you define the schemas for the tables that will be auto
| generated for each site
|
|  - For fields, use CI style schema
|  - For keys, use column => flag schema, where flag indicates a primary key
|    Multi-column keys should be separated by a comma: column1,column2 => flag
|
*/

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

		'hub_driver' => array(
			'type'				=> 'CHAR',
			'constraint'		=> 5, 
			'null'				=> FALSE
		),

		'hub_source' => array(
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
			'type'					=> 'BIGINT',
			'constraint'			=> 20, 
			'null'					=> FALSE,
			'auto_increment'		=> TRUE
		),

		'user_name' => array(
			'type'					=> 'VARCHAR',
			'constraint'			=> 100, 
			'null'					=> FALSE
		),

		'user_password' => array(
			'type'					=> 'VARCHAR',
			'constraint'			=> 128, 
			'null'					=> FALSE
		),

		'user_email' => array(
			'type'					=> 'VARCHAR',
			'constraint'			=> 225, 
			'null'					=> FALSE
		),

		'user_roles' => array(
			'type'					=> 'TEXT',
			'null'					=> FALSE
		),

		'user_founder' => array(
			'type'					=> 'TINYINT',
			'constraint'			=> 1, 
			'null'					=> FALSE
		)
	),

	'keys' => array(
		'user_id'					=> TRUE,
		'user_name,user_password'	=> FALSE
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

		'widget_width' => array(
			'type'				=> 'TINYINT',
			'constraint'		=> 1,
			'null'				=> FALSE
		),

		'widget_roles' => array(
			'type'				=> 'TEXT',
			'null'				=> FALSE
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

/* End of file schemas.php */
/* Location: ./application/config/schemas.php */