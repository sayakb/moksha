<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Expressions
| -------------------------------------------------------------------------
| Defines control path expressions and their replacements
|
*/
$config['expressions']['functions'] = array(
	'url'		=> '$this->CI->uri->segment($value, "")',
	'hub'		=> 'isset($data->$value) ? $data->$value : ""',
	'get'		=> '$this->CI->input->get($value)',
	'post'		=> '$this->CI->input->post($value)',
	'cookie'	=> '$this->CI->input->cookie($value)',
	'server'	=> '$this->CI->input->server(strtoupper($value))',
	'user'		=> 'user_data($value)',
	'calc'		=> 'eval("return $value;")',
	'sys'		=> 'in_array($value, $config["sys_functions"]) ? $value() : ""'
);

/*
| -------------------------------------------------------------------------
| Allowed system expressions
| -------------------------------------------------------------------------
| Since it is dangerous to let the user execute any method, we put a
| limit on what he can execute
|
 */
$config['expressions']['sys_functions'] = array(
	'base_url',
	'current_url',
	'homepage'
);

/* End of file parser.php */
/* Location: ./application/config/parser.php */