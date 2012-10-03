<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Widget Library for Moksha
 *
 * This class handles actions related to site widgets
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Widget {

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
	public function fetch_controls()
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
	 * Fetches a list of widgets for the site
	 *
	 * @access	public
	 * @return	array	list of widgets
	 */
	public function fetch_all()
	{
		return $this->CI->db->get("site_widgets_{$this->CI->bootstrap->site_id}")->result();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a specific widget for a site
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @return	object	widget details
	 */
	public function fetch($widget_id)
	{
		$this->CI->db->where('widget_id', $widget_id);
		return $this->CI->db->get("site_widgets_{$this->CI->bootstrap->site_id}")->row();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetches a count of widgets added to the site
	 *
	 * @access	public
	 * @return	int		count of widgets
	 */
	public function count()
	{
		return $this->CI->db->count_all("site_widgets_{$this->CI->bootstrap->site_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new widget to the DB
	 *
	 * @access	public
	 * @param	string	widget name
	 * @param	array	widget metadata
	 * @return	bool	true if succeeded
	 */
	public function add($widget_name, $widget_data)
	{
		$data = array(
			'widget_name'	=> $widget_name,
			'widget_data'	=> serialize($widget_data)
		);

		return $this->CI->db->insert("site_widgets_{$this->CI->bootstrap->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Updated an existing widget
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @param	string	widget name
	 * @param	array	widget metadata
	 * @return	bool	true if succeeded
	 */
	public function update($widget_id, $widget_name, $widget_data)
	{
		$data = array(
			'widget_name'	=> $widget_name,
			'widget_data'	=> serialize($widget_data)
		);

		$this->CI->db->where('widget_id', $widget_id);
		return $this->CI->db->update("site_widgets_{$this->CI->bootstrap->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Deletes a specific widget
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @return	bool	true if succeeded
	 */
	public function delete($widget_id)
	{
		$this->CI->db->where('widget_id', $widget_id);
		return $this->CI->db->delete("site_widgets_{$this->CI->bootstrap->site_id}");
	}

	// --------------------------------------------------------------------
}
// END Widget class

/* End of file Widget.php */
/* Location: ./application/libraries/Widget.php */
