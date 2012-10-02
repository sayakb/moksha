<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Control Library for Moksha
 *
 * This class handles actions related to site controls
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Control {

	var $CI;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch different types of available controls
	 *
	 * @access	public
	 * @return	array	control list
	 */
	public function fetch_types()
	{
		return array(
			'paragraph' => (object)array(
				'icon'	=> 'paragraph',
				'label'	=> 'field_paragraph',
			),
			'heading_big' => (object)array(
				'icon'	=> 'heading',
				'label'	=> 'field_heading_big',
			),
			'heading_normal' => (object)array(
				'icon'	=> 'heading',
				'label'	=> 'field_heading_normal',
			),
			'heading_small' => (object)array(
				'icon'	=> 'heading',
				'label'	=> 'field_heading_small',
			),
			'hyperlink' => (object)array(
				'icon'	=> 'hyperlink',
				'label'	=> 'field_hyperlink',
			),
			'textbox' => (object)array(
				'icon'	=> 'textbox',
				'label'	=> 'field_textbox',
			),
			'password' => (object)array(
				'icon'	=> 'password',
				'label'	=> 'field_password',
			),
			'textarea' => (object)array(
				'icon'	=> 'textarea',
				'label'	=> 'field_textarea',
			),
			'wysiwyg' => (object)array(
				'icon'	=> 'wysiwyg',
				'label'	=> 'field_wysiwyg',
			),
			'codebox' => (object)array(
				'icon'	=> 'codebox',
				'label'	=> 'field_codebox',
			),
			'checkbox' => (object)array(
				'icon'	=> 'checkbox',
				'label'	=> 'field_checkbox',
			),
			'radio' => (object)array(
				'icon'	=> 'radio',
				'label'	=> 'field_radio',
			),
			'submit_button' => (object)array(
				'icon'	=> 'button',
				'label'	=> 'field_submit_button',
			),
			'reset_button' => (object)array(
				'icon'	=> 'button',
				'label'	=> 'field_reset_button',
			),
			'file' => (object)array(
				'icon'	=> 'file',
				'label'	=> 'field_file',
			),
			'hidden' => (object)array(
				'icon'	=> 'hidden',
				'label'	=> 'field_hidden',
			),
			'image' => (object)array(
				'icon'	=> 'image',
				'label'	=> 'field_image',
			),
			'select' => (object)array(
				'icon'	=> 'select',
				'label'	=> 'field_select',
			),
			'multiselect' => (object)array(
				'icon'	=> 'multiselect',
				'label'	=> 'field_multiselect',
			)
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a list of controls for the site
	 *
	 * @access	public
	 * @return	array	list of controls
	 */
	public function fetch_all()
	{
		return $this->CI->db->get("site_controls_{$this->CI->bootstrap->site_id}")->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a specific control for a site
	 *
	 * @access	public
	 * @param	int		control identifier
	 * @return	object	control details
	 */
	public function fetch($control_id)
	{
		$this->CI->db->where('control_id', $control_id);
		return $this->CI->db->get("site_controls_{$this->CI->bootstrap->site_id}")->row();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a count of controls added to the site
	 *
	 * @access	public
	 * @return	int		count of controls
	 */
	public function count()
	{
		return $this->CI->db->count_all("site_controls_{$this->CI->bootstrap->site_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new control to the DB
	 *
	 * @access	public
	 * @param	string	control name
	 * @param	array	controls to add
	 * @return	bool	true if succeeded
	 */
	public function add($control_name, $controls)
	{
		$data = array(
			'control_name'		=> $control_name,
			'control_elements'	=> serialize($controls)
		);

		return $this->CI->db->insert("site_controls_{$this->CI->bootstrap->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Updated an existing control
	 *
	 * @access	public
	 * @param	int		control identifier
	 * @param	string	control name
	 * @param	array	controls to add
	 * @return	bool	true if succeeded
	 */
	public function update($control_id, $control_name, $controls)
	{
		$data = array(
			'control_name'		=> $control_name,
			'control_elements'	=> serialize($controls)
		);

		$this->CI->db->where('control_id', $control_id);
		return $this->CI->db->update("site_controls_{$this->CI->bootstrap->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a specific control
	 *
	 * @access	public
	 * @param	int		control identifier
	 * @return	bool	true if succeeded
	 */
	public function delete($control_id)
	{
		$this->CI->db->where('control_id', $control_id);
		return $this->CI->db->delete("site_controls_{$this->CI->bootstrap->site_id}");
	}

	// --------------------------------------------------------------------
}
// END Control class

/* End of file Control.php */
/* Location: ./application/libraries/Control.php */