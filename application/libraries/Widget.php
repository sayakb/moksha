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
		$widget = $this->CI->db->get("site_widgets_{$this->CI->site->site_id}")->row();

		if (count($widget) != 0)
		{
			$widget->widget_data = unserialize($widget->widget_data);
			return $widget;
		}
		else
		{
			show_error($this->CI->lang->line('resource_404'));
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
		return $this->CI->db->count_all("site_widgets_{$this->CI->site->site_id}");
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a new widget
	 *
	 * @access	public
	 * @param	string	widget name
	 * @param	array	widget options
	 * @param	object	widget data
	 * @return	bool	true if successful
	 */
	public function create($widget_name, $widget_options, $widget_data)
	{
		$data = array_merge($widget_options, array(
			'widget_name' => $widget_name,
			'widget_data' => serialize($widget_data)
		));

		return $this->CI->db->insert("site_widgets_{$this->CI->site->site_id}", $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Modifies a new widget
	 *
	 * @access	public
	 * @param	int		widget identifier
	 * @param	string	widget name
	 * @param	array	widget options
	 * @param	object	widget data
	 * @return	bool	true if successful
	 */
	public function modify($widget_id, $widget_name, $widget_options, $widget_data)
	{
		$data = array_merge($widget_options, array(
			'widget_name' => $widget_name,
			'widget_data' => serialize($widget_data)
		));

		return $this->CI->db->update("site_widgets_{$this->CI->site->site_id}", $data, array('widget_id' => $widget_id));
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
		return $this->CI->db->delete("site_widgets_{$this->CI->site->site_id}", array('widget_id' => $widget_id));
	}

	// --------------------------------------------------------------------
}
// END Widget class

/* End of file Widget.php */
/* Location: ./application/libraries/Widget.php */