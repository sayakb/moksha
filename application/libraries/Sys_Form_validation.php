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
		// Do we even have any data to process?  Mm?
		if (count($_POST) == 0)
		{
			return FALSE;
		}

		// Does the _field_data array containing the validation rules exist?
		// If not, we look to see if they were assigned via a config file
		if (count($this->_field_data) == 0)
		{
			// No validation rules?  We're done...
			if (count($this->_config_rules) == 0)
			{
				return FALSE;
			}

			// Is there a validation rule for the particular URI being accessed?
			$uri = ($group == '') ? trim($this->CI->uri->ruri_string(), '/') : $group;

			if ($uri != '' AND isset($this->_config_rules[$uri]))
			{
				$this->set_rules($this->_config_rules[$uri]);
			}
			else
			{
				$this->set_rules($this->_config_rules);
			}

			// We're we able to set the rules correctly?
			if (count($this->_field_data) == 0)
			{
				log_message('debug', "Unable to find validation rules");
				return FALSE;
			}
		}

		// Load the language file containing error messages
		$this->CI->lang->load('form_validation');

		// Cycle through the rules for each field, match the
		// corresponding $_POST item and test for errors
		foreach ($this->_field_data as $field => $row)
		{
			// Fetch the data from the corresponding $_POST array and cache it in the _field_data array.
			// Depending on whether the field name is an array or a string will determine where we get it from.

			if ($row['is_array'] == TRUE)
			{
				$this->_field_data[$field]['postdata'] = $this->_reduce_array($_POST, $row['keys']);
			}
			else
			{
				if (isset($_POST[$field]) AND $_POST[$field] != "")
				{
					$this->_field_data[$field]['postdata'] = $_POST[$field];
				}
			}

			$this->_execute($row, explode('|', $row['rules']), $this->_field_data[$field]['postdata']);
		}

		// Did we end up with any errors?
		$total_errors = count($this->_error_array);

		if ($total_errors > 0)
		{
			$this->_safe_form_data = TRUE;
		}

		// Now we need to re-set the POST data with the new, processed data
		$this->_reset_post_array();

		// No errors, validation passes!
		if ($total_errors == 0)
		{
			// Clear field value as validation succeeded
			$this->_field_data = array();
			
			return TRUE;
		}

		// Validation fails
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Match one field to another
	 * This is an extension of the core to have it work with central
	 * and site databases based on context
	 *
	 * @access	public
	 * @param	string	value to be validated
	 * @param	string	field to be validated
	 * @return	bool	true if unique
	 */
	public function is_unique($str, $field)
	{
		list($table, $field) = explode('.', $field);

		// Determine the sub dir we are in
		$subdir = $this->CI->router->fetch_directory();

		if ($subdir == 'central_admin/')
		{
			$db = $this->CI->db_c;
		}
		else
		{
			$db = $this->CI->db_s;
			$context = $this->CI->bootstrap->site_id;
			$table = "site_{$context}_{$table}";
		}

		if (isset($this->unique_exempts[$field]))
		{
			$db->where("{$field} !=", $this->unique_exempts[$field]);
		}
		
		$query = $db->limit(1)->get_where($table, array($field => $str));
		return $query->num_rows() === 0;
    }

	// --------------------------------------------------------------------
}
// END Form Validation Class

/* End of file Sys_Form_validation.php */
/* Location: ./application/libraries/Sys_Form_validation.php */
