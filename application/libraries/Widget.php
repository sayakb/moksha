<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Widget Library for Moksha
 *
 * This class handles global widget operations
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
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
	 * Fetches a specific widget for a site
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @return	mixed	widget details, FALSE if not found
	 */
	public function get($widget_id)
	{
		$this->CI->db->where('widget_id', $widget_id);
		$widget = $this->CI->db->get("site_widgets_{$this->CI->bootstrap->site_id}")->row();

		if (count($widget) != 0)
		{
			$widget->widget_data = unserialize($widget->widget_data);
			return $widget;
		}
		else
		{
			return FALSE;
		}
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
	 * Creates a new widget
	 *
	 * @access	public
	 * @param	string	widget name
	 * @param	string	operation key for the widget
	 * @param	string	roles having access to the widget
	 * @param	object	widget data
	 * @return	bool	true if successful
	 */
	public function create($widget_name, $widget_key, $widget_roles, $widget_data)
	{
		$data = array(
			'widget_name'	=> $widget_name,
			'widget_key'	=> $widget_key,
			'widget_roles'	=> $widget_roles,
			'widget_data'	=> serialize($widget_data)
		);

		return $this->CI->db->insert("site_widgets_{$this->CI->bootstrap->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Modifies a new widget
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @param	string	widget name
	 * @param	string	operation key for the widget
	 * @param	string	roles having access to the widget
	 * @param	object	widget data
	 * @return	bool	true if successful
	 */
	public function modify($widget_id, $widget_name, $widget_key, $widget_roles, $widget_data)
	{
		$data = array(
			'widget_name'	=> $widget_name,
			'widget_key'	=> $widget_key,
			'widget_roles'	=> $widget_roles,
			'widget_data'	=> serialize($widget_data)
		);

		return $this->CI->db->update("site_widgets_{$this->CI->bootstrap->site_id}", $data, array('widget_id' => $widget_id));
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
		return $this->CI->db->delete("site_widgets_{$this->CI->bootstrap->site_id}", array('widget_id' => $widget_id));
	}

	// --------------------------------------------------------------------
}
// END Widget class

/* End of file Widget.php */
/* Location: ./application/libraries/Widget.php */