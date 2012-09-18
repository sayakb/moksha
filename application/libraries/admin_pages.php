<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Admin page generator
 *
 * Generates pagination for admin pages
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Moksha Team
 */
class Admin_pages {

	var $CI;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();
	}

	/**
	 * Generates pagination links
	 *
	 * @access	public
	 * @param	base_url Base URL for page links
	 * @param 	current_page Current page number
	 * @param 	total_items Total item count
	 */
	public function generate($base_url, $current_page, $total_items)
	{
		$output = '';
		$per_page = $this->CI->config->item('per_page');
		$page_count = ceil($total_items / $per_page);

		$prev_page = $current_page - 1;
		$next_page = $current_page + 1;

		if ($page_count > 1)
		{
			if ($current_page == 1)
			{
				$output .= "<li class='disabled'><span>&laquo;</span></li>";
			}
			else
			{
				$output .= "<li><a href='{$base_url}/{$prev_page}'>&laquo;</a></li>";
			}
			
			for ($page = 1; $page <= $page_count; $page++)
			{
				if ($page == $current_page)
				{
					$output .= "<li class='active'><span>{$page}</span></li>";
				}
				else
				{
					$output .= "<li><a href='{$base_url}/{$page}'>{$page}</a></li>";
				}
			}

			if ($current_page == $page_count)
			{
				$output .= "<li class='disabled'><span>&raquo;</span></li>";
			}
			else
			{
				$output .= "<li><a href='{$base_url}/{$next_page}'>&raquo;</a></li>";
			}
		}

		return $output;
	}
}
// END Admin_pages class

/* End of file admin_pages.php */
/* Location: ./application/libraries/admin_pages.php */