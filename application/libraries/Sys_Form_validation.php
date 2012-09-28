<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Form Validation Class extension
 *
 * @package		Moksha
 * @category	Library
 * @author		Moksha Team
 */
class Sys_Form_validation extends CI_Form_validation {

	/**
	 * Value to ignore when doing a unique check
	 *
	 * @var array
	 */
	var $unique_exempts = array();

	// --------------------------------------------------------------------

	/**
	 * Run the Validator
	 *
	 * This function does all the work.
	 *
	 * @access	public
	 * @param	string	validation group
	 * @return	bool
	 */
	public function run($group = '')
	{
		$validation = parent::run($group);

		// If validation succeeds, clear the field data
		if ($validation)
		{
			$this->_field_data = array();
		}

		return $validation;
	}

	// --------------------------------------------------------------------

	/**
	 * Match one field to another
	 * This is an extension of the core to support update exempts
	 *
	 * @access	public
	 * @param	string	value to be validated
	 * @param	string	field to be validated
	 * @return	bool	true if unique
	 */
	public function is_unique($str, $field)
	{
		list($table, $field) = explode('.', $field);

		// Non-central tables have a _siteId suffix
		if ( ! in_central())
		{
			$table = "{$table}_{$this->CI->bootstrap->site_id}";
		}

		// Check for exempts and apply them in the query
		if (isset($this->unique_exempts[$field]))
		{
			$this->CI->db->where("{$field} !=", $this->unique_exempts[$field]);
		}
		
		$query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
		return $query->num_rows() === 0;
    }

	// --------------------------------------------------------------------

	/**
	 * Clear post data if length of item is zero
	 */
	public function _execute($row, $rules, $postdata = NULL, $cycles = 0)
	{
		if (( ! is_array($postdata) AND strlen($postdata) == 0) OR (is_array($postdata) AND empty($postdata)))
		{
			$postdata = NULL;
		}

		return parent::_execute($row, $rules, $postdata, $cycles);
	}

	// --------------------------------------------------------------------
}
// END Form Validation Class

/* End of file Sys_Form_validation.php */
/* Location: ./application/libraries/Sys_Form_validation.php */
