<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Dynamic content generator
 *
 * This class creates widgets and controls dynamically for pages
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Dynamic {

	var $CI;

	// --------------------------------------------------------------------

	/**
	 * Page being rendered
	 * 
	 * @access public
	 * @var object
	 */	
	var $page;

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
	 * Generates the output for a page
	 *
	 * @access	public
	 */
	public function generate_page($page)
	{
		
	}

	// --------------------------------------------------------------------
}
// END Dynamic class

/* End of file Dynamic.php */
/* Location: ./application/libraries/Dynamic.php */