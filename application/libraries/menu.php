<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Menu Library
 *
 * This class generates menu 
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Menu {

	var $CI;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
	}

	/**
	 * Menu generator
	 *
	 * Generates a menu for a specific group and page
	 *
	 * @access	public
	 * @param	group Group identifier
	 * @param	page Page identifier
	 */
	public function generate($group, $page)
	{
		$output = '';
		
		// Load the menu configuration
		$this->CI->config->load('menus');

		// Grab all the menus
		$menus = $this->CI->config->item('menus');

		if (isset($menus[$group]) AND is_array($menus[$group]))
		{
			foreach ($menus[$group] as $key => $item)
			{
				if ($key == $page)
				{
					$active = ' class="active"';
				}
				else
				{
					$active = '';
				}

				// Fetch the item label
				$label = $this->CI->lang->line($item['label']);
				$url = base_url($item['url']);

				// Generate the item
				$output .= "<li{$active}><a href='{$url}'>{$label}</a></li>";
			}
		}

		return $output;
	}
}
// END Menu class

/* End of file template.php */
/* Location: ./application/libraries/menu.php */