<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Moksha Control Generator
 *
 * @package		Moksha
 * @category	Helpers
 * @author		Sayak Banerjee <sayakb@kde.org>
 */

// ------------------------------------------------------------------------

/**
 * Creates a parahraph control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_paragraph($name, $options, $data = FALSE)
{
	$content = expr($options->disp_src, $data);
	return "<p class='{$options->classes}'>{$content}</p>";
}

// ------------------------------------------------------------------------

/**
 * Creates a heading (big) control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_heading_big($name, $options, $data = FALSE)
{
	$content = expr($options->disp_src, $data);
	return "<h2 class='{$options->classes}'>{$content}</h2>";
}

// ------------------------------------------------------------------------

/**
 * Creates a heading (normal) control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_heading_normal($name, $options, $data = FALSE)
{
	$text = expr($options->disp_src, $data);
	return "<h3 class='{$options->classes}'>{$text}</h3>";
}

// ------------------------------------------------------------------------

/**
 * Creates a heading (small) control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_heading_small($name, $options, $data = FALSE)
{
	$text = expr($options->disp_src, $data);
	return "<h4 class='{$options->classes}'>{$text}</h4>";
}

// ------------------------------------------------------------------------

/**
 * Creates a hyperlink control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_hyperlink($name, $options, $data = FALSE)
{
	$url	= expr($options->get_path, $data, $options->format);
	$text	= expr($options->disp_src, $data);

	return "<p><a href='{$url}' class='{$options->classes}'>{$text}</a></p>";
}

// ------------------------------------------------------------------------

/**
 * Creates an ordered list
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_ordered_list($name, $options, $data = FALSE)
{
	$items	= explode('\n', expr($options->disp_src, $data));
	$list	= '';

	foreach ($items as $item)
	{
		if (substr($item, 0, 1) == '-')
		{
			$item  = trim(substr($item, 1));
			$list .= "<li>{$item}</li>";
		}
	}

	return "<ol class='{$options->classes}'>{$list}</ol>";
}

// ------------------------------------------------------------------------

/**
 * Creates an un-ordered list
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_unordered_list($name, $options, $data = FALSE)
{
	$items	= explode('\n', expr($options->disp_src, $data));
	$list	= '';

	foreach ($items as $item)
	{
		if (substr($item, 0, 1) == '-')
		{
			$item  = trim(substr($item, 1));
			$list .= "<li>{$item}</li>";
		}
	}

	return "<ul class='{$options->classes}'>{$list}</ul>";
}

// ------------------------------------------------------------------------

/**
 * Creates pagination links
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_pagination($name, $options, $data = FALSE)
{
	$page = $this->CI->dynamic->context->page;
	$rows = $this->CI->dynamic->context->rows;

	$this->CI->pagination->initialize(
		array_merge($this->CI->config->item('pagination'), array(
			'full_tag_open'	=> "<ul class='{$options->classes}'>",
			'base_url'		=> base_url(expr($page->page_url)),
			'total_rows'	=> $rows,
			'uri_segment'	=> count(explode('/', $page->page_url)) + 1
		))
	);

	return $this->CI->pagination->create_links();
}

// ------------------------------------------------------------------------

/**
 * Creates a textbox control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_textbox($name, $options, $data = FALSE)
{
	$value	= set_value($name, expr($options->get_path, $data, $options->format));

	$label	= expr($options->disp_src, $data);
	$ctrl	= "<input name='{$name}' type='text' value='{$value}' class='{$options->classes}' />";

	return form_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a password box control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_password($name, $options, $data = FALSE)
{
	$value	= set_value($name);

	$label	= expr($options->disp_src, $data);
	$ctrl	= "<input name='{$name}' type='password' class='{$options->classes}' />";

	return form_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a textarea control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function context_textarea($name, $options, $data = FALSE)
{
	$value	= set_value($name, expr($options->get_path, $data, $options->format));

	$label	= expr($options->disp_src, $data);
	$ctrl	= "<textarea name='{$name}' class='{$options->classes}'>{$value}</textarea>";

	return form_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a page notice control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_notice($name, $options, $data = FALSE)
{
	$notice = '';

	if (isset($this->CI->dynamic->context->success_msgs))
	{
		$notice .=	"<div class='alert alert-success'>".
						$this->CI->dynamic->context->success_msgs.
					"</div>";
	}

	if (isset($this->CI->dynamic->context->error_msgs))
	{
		$notice .=	"<div class='alert alert-error'>".
						$this->CI->dynamic->context->error_msgs.
					"</div>";
	}

	return $notice;
}

// ------------------------------------------------------------------------

/**
 * Creates a WYSIWYG control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function context_wysiwyg($name, $options, $data = FALSE)
{
	$value	= set_value($name, expr($options->get_path, $data, $options->format));

	$label	= expr($options->disp_src, $data);
	$ctrl	= "<textarea name='{$name}' class='wysiwyg {$options->classes}'>{$value}</textarea>";

	return form_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a syntax highlighted code box control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_codebox($name, $options, $data = FALSE)
{
	$content = expr($options->disp_src, $data);
	return "<pre class='prettyprint linenums {$options->classes}'>{$content}</pre>";
}

// ------------------------------------------------------------------------

/**
 * Creates a checkbox control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_checkbox($name, $options, $data = FALSE)
{
	$value		= set_value($name, expr($options->get_path, $data, $options->format));
	$checked	= empty($value) ? '' : "checked='checked'";

	$text		= expr($options->disp_src, $data);
	$text		= explode('\n', $text);

	if (count($text) >= 2)
	{
		$label = $text[0];
		$field = $text[1];
	}
	else
	{
		$label = NULL;
		$field = $text[0];
	}

	$ctrl	=	"<label class='checkbox'>".
					"<input name='{$name}' type='checkbox' {$checked} class='{$options->classes}' /> {$field}".
				"</label>";

	return form_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a radio button control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_radio($name, $options, $data = FALSE)
{
	$value		= set_value($name, expr($options->get_path, $data, $options->format));
	$checked	= empty($value) ? '' : "checked='checked'";

	$text		= expr($options->disp_src, $data);
	$text		= explode('\n', $text);

	if (count($text) >= 2)
	{
		$label = $text[0];
		$field = $text[1];
	}
	else
	{
		$label = NULL;
		$field = $text[0];
	}

	$ctrl	=	"<label class='radio'>".
					"<input name='{$name}' type='radio' {$checked} class='{$options->classes}' /> {$field}".
				"</label>";

	return form_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a file upload control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_file_upload($name, $options, $data = FALSE)
{
	$label	= expr($options->disp_src, $data);
	$ctrl	= "<input name='{$name}' type='file' class='{$options->classes}' />";

	return form_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a hidden control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_hidden($name, $options, $data = FALSE)
{
	$value	= set_value($name, expr($options->get_path, $data, $options->format));
	
	return "<input name='{$name}' type='hidden' value='{$value}' />";
}

// ------------------------------------------------------------------------

/**
 * Creates an image control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_image($name, $options, $data = FALSE)
{
	$image = expr($options->get_path, $data, $options->format);
	$title = expr($options->disp_src, $data);

	return "<p><img src='{$image}' alt='{$title}' title='{$title}' class='{$options->classes}' /></p>";
}

// ------------------------------------------------------------------------

/**
 * Creates a dropdown select menu
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_select($name, $options, $data = FALSE)
{
	$value		= set_value($name, expr($options->get_path, $data, $options->format));
	$items		= explode('\n', expr($options->disp_src, $data));
	$options	= '';
	$label		= NULL;

	if (substr($items[0], 0, 1) != '-')
	{
		$label = trim(substr($items[0], 1));
	}

	foreach ($items as $item)
	{
		if (substr($item, 0, 1) == '-')
		{
			$item		= trim(substr($item, 1));
			$selected	= $item == $value ? "selected='selected'" : '';

			$options	= "<option value='{$item}' {$selected}>{$item}</option>";
		}
	}

	return form_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a multi-select menu
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_multiselect($name, $options, $data = FALSE)
{
	$name	= $name.'[]';
	$values	= set_value($name, expr($options->get_path, $data, $options->format));

	if ( ! is_array($values))
	{
		$values = @unserialize($values);
	}

	if (is_array($values))
	{
		$items		= explode('\n', expr($options->disp_src, $data));
		$options	= '';
		$label		= NULL;

		if (substr($items[0], 0, 1) != '-')
		{
			$label = trim(substr($items[0], 1));
		}

		foreach ($items as $item)
		{
			if (substr($item, 0, 1) == '-')
			{
				$item		= trim(substr($item, 1));
				$selected	= in_array($item, $values) ? "selected='selected'" : '';

				$options	= "<option value='{$item}' {$selected}>{$item}</option>";
			}
		}

		return form_group($label, $ctrl);
	}

	return NULL;
}

// ------------------------------------------------------------------------

/**
 * Creates a submit button control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_submit_button($name, $options, $data = FALSE)
{
	$text = expr($options->disp_src, $data);
	return "<input name='{$name}' type='submit' text='{$text}' />";
}

// ------------------------------------------------------------------------

/**
 * Creates a reset button control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @return	string	control markup
 */
function control_reset_button($name, $options, $data = FALSE)
{
	$text = expr($options->disp_src, $data);
	return "<input name='{$name}' type='reset' text='{$text}' />";
}

// ------------------------------------------------------------------------


/* End of file control_helper.php */
/* Location: ./application/helpers/control_helper.php */