<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Template Library
 *
 * This class exposes a custom template load method that auto-includes the
 * page header and footer
 *
 * @package		Moksha
 * @category	Libraries
 * @author		Sayak Banerjee <sayakb@kde.org>
 */
class Template {

	var $CI;

	// --------------------------------------------------------------------

	/**
	 * Error messages for the view
	 *
	 * @access public
	 * @var string
	 */
	var $error_msgs;

	// --------------------------------------------------------------------

	/**
	 * Success messages for the view
	 *
	 * @access public
	 * @var string
	 */
	var $success_msgs;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->CI =& get_instance();

		$this->error_msgs = NULL;
		$this->success_msgs = NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Default template key initializer
	 *
	 * Assigns default values for common template data
	 *
	 * @access	private
	 */
	private function template_defaults()
	{
		return array(
			'page_title'		=> $this->CI->lang->line('moksha'),
			'page_desc'			=> $this->CI->lang->line('moksha_desc'),
			'page_copyright'	=> $this->CI->lang->line('default_copyright'),
			'page_menu'			=> $this->fetch_menu(),
			'page_logout'		=> $this->fetch_logout(),
			'page_notice'		=> NULL,
			'page_dir'			=> NULL,
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Displays a confirmation box
	 *
	 * @access	public
	 * @param	string	message to be displayed
	 * @param	string	caption (heading) of the message box
	 * @return	bool	true if user confirms
	 */
	public function confirm_box($message, $caption = '')
	{
		if (isset($_POST['yes']))
		{
			return TRUE;
		}

		if (isset($_POST['no']))
		{
			return FALSE;
		}

		if (empty($caption))
		{
			$caption = $this->CI->lang->line('confirm');
		}

		if (strpos($message, 'lang:') == 0)
		{
			$message = $this->CI->lang->line(substr($message, 5));
		}

		// Assign view data
		$data = array(
			'message'		=> $message,
			'caption'		=> $caption,
		);

		exit($this->load('common/confirm_box', $data, TRUE));
	}

	// --------------------------------------------------------------------

	/**
	 * Displays an authentication box as popup
	 *
	 * @access	public
	 * @return	string	entered password
	 */
	public function password_box()
	{
		if (isset($_POST['submit']))
		{
			return $this->CI->input->post('password');
		}

		if (isset($_POST['cancel']))
		{
			return FALSE;
		}

		exit($this->load('common/password_box', array(), TRUE));
	}

	// --------------------------------------------------------------------

	/**
	 * Load the page template
	 *
	 * @access	public
	 * @param	string	view to be loaded
	 * @param	array	data to be passed to the template
	 * @param	bool	output the template as return value
	 * @return	string	parsed template, if $output is set to TRUE
	 */
	public function load($view, $data = array(), $output = FALSE)
	{
		$parsed = '';

		// No data was passed
		if (count($data) == 0)
		{
			$data = $this->template_defaults();
		}
		else
		{
			$data = array_merge($this->template_defaults(), $data);
		}

		// Get validation errors
		$validation_msgs = validation_errors();

		// Read error and success messages from session
		$error_flash = $this->CI->session->flashdata('error_msg');
		$success_flash = $this->CI->session->flashdata('success_msg');

		// Get the current sub-directory
		$data['page_dir'] = $this->CI->router->fetch_directory();

		// Override local error messages with validation/session messages
		if ( ! empty($validation_msgs))
		{
			$this->error_msgs = $validation_msgs;
		}
		else if ( ! empty($error_flash))
		{
			$this->error_msgs = $error_flash;
		}
		else if ( ! empty($success_flash))
		{
			$this->success_msgs = $success_flash;
		}

		// Show error first, and if there are none, show success messages
		if ( ! empty($this->error_msgs))
		{
			$data['page_notice'] = array(
				'type'		=> 'error',
				'message'	=> $this->error_msgs
			);
		}
		else if ( ! empty($this->success_msgs))
		{
			$data['page_notice'] = array(
				'type'		=> 'success',
				'message'	=> $this->success_msgs
			);
		}

		// We assume that output is being returned
		$parsed .= $this->CI->load->view("common/header", $data, $output);

		if ($data['page_dir'] == 'central_admin/' OR $data['page_dir'] == 'site_admin/')
		{
			$parsed .= $this->CI->load->view("common/header_admin", $data, $output);
		}

		$parsed .= $this->CI->load->view($view, $data, $output);

		if ($data['page_dir'] == 'central_admin/' OR $data['page_dir'] == 'site_admin/')
		{
			$parsed .= $this->CI->load->view("common/footer_admin", $data, $output);
		}

		$parsed .= $this->CI->load->view("common/footer", $data, $output);

		return $parsed;
	}

	// --------------------------------------------------------------------

	/**
	 * Menu generator
	 *
	 * Returns a menu for the current route, if defined
	 *
	 * @access	public
	 */
	public function fetch_menu()
	{
		$output = '';

		// Get current route info
		$page_dir = substr($this->CI->router->fetch_directory(), 0, -1);
		$controller = $this->CI->router->fetch_class();

		// Load the menu configuration
		$this->CI->config->load('menus');

		// Grab all the menus
		$menus = $this->CI->config->item('menus');

		if (isset($menus[$page_dir]) AND is_array($menus[$page_dir]))
		{
			foreach ($menus[$page_dir] as $key => $item)
			{
				$label = $this->CI->lang->line($item['label']);

				if ($key == $controller)
				{
					$active = ' class="active"';
					$href = '';
				}
				else
				{
					$active = '';
					$href = 'href="'.base_url($item['url']).'"';
				}

				// Generate the item
				$output .= "<li{$active}><a {$href}>{$label}</a></li>";
			}
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Generates a logout link
	 *
	 * @access	public
	 */
	public function fetch_logout()
	{
		$page_dir = $this->CI->router->fetch_directory();

		if ($page_dir == 'central_admin/')
		{
			$url = base_url('admin/central/logout');
		}
		else if ($page_dir == 'site_admin/')
		{
			$url = base_url('admin/logout');
		}
		else if (site_config('login') == ENABLED)
		{
			$url = base_url('logout');
		}

		if (isset($url))
		{
			return "<i class='icon-user'></i><a href='{$url}'>" .
					$this->CI->lang->line('logout').'</a>';
		}
		else
		{
			return NULL;
		}
	}

	// --------------------------------------------------------------------
}
// END Template class

/* End of file Template.php */
/* Location: ./application/libraries/Template.php */