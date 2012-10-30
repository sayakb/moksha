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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_paragraph($name, $options, $data = FALSE, $context = NULL)
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_heading_big($name, $options, $data = FALSE, $context = NULL)
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_heading_normal($name, $options, $data = FALSE, $context = NULL)
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_heading_small($name, $options, $data = FALSE, $context = NULL)
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_hyperlink($name, $options, $data = FALSE, $context = NULL)
{
	$url	= expr($options->get_path, $data, $options->format);
	$text	= expr($options->disp_src, $data);

	return "<a href='{$url}' class='{$options->classes}'>{$text}</a>";
}

// ------------------------------------------------------------------------

/**
 * Creates an ordered list
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_ordered_list($name, $options, $data = FALSE, $context = NULL)
{
	$items	= explode('<br>', expr($options->disp_src, $data));
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_unordered_list($name, $options, $data = FALSE, $context = NULL)
{
	$items	= explode('<br>', expr($options->disp_src, $data));
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_pagination($name, $options, $data = FALSE, $context = NULL)
{
	$CI			=& get_instance();
	$page_url	= base_url(expr($context->page->page_url, $data));
	$page_sgmt	= str_replace(base_url(), '', $page_url);

	$CI->pagination->initialize(
		array_merge($CI->config->item('pagination'), array(
			'base_url'		=> $page_url,
			'total_rows'	=> intval(expr($options->get_path, $data, $options->format)),
			'uri_segment'	=> count(explode('/', $page_sgmt))
		))
	);

	return "<div class='pagination {$options->classes}'>".$CI->pagination->create_links().'</div>';
}

// ------------------------------------------------------------------------

/**
 * Creates a textbox control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_textbox($name, $options, $data = FALSE, $context = NULL)
{
	$value	= set_value($name, expr($options->get_path, $data, $options->format));

	$label	= expr($options->disp_src, $data);
	$ctrl	= "<input name='{$name}' type='text' value='{$value}' class='{$options->classes}' />";

	return _control_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a password box control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_password($name, $options, $data = FALSE, $context = NULL)
{
	$value	= set_value($name);

	$label	= expr($options->disp_src, $data);
	$ctrl	= "<input name='{$name}' type='password' class='{$options->classes}' />";

	return _control_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a textarea control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_textarea($name, $options, $data = FALSE, $context = NULL)
{
	$value	= set_value($name, expr($options->get_path, $data, $options->format));

	$label	= expr($options->disp_src, $data);
	$ctrl	= "<textarea name='{$name}' class='{$options->classes}'>{$value}</textarea>";

	return _control_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a date picker control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_date_picker($name, $options, $data = FALSE, $context = NULL)
{
	$value	= set_value($name, expr($options->get_path, $data, $options->format));
	$label	= expr($options->disp_src, $data);

	$ctrl	=	"<div class='input-append'>".
					"<input name='{$name}' type='text' value='{$value}' class='datepicker {$options->classes}' />".
					"<span class='add-on'><i class='icon-calendar'></i></span>".
				"</div>";

	return _control_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a page notice control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_notice($name, $options, $data = FALSE, $context = NULL)
{
	$success	= $context->success_msgs;
	$error		= $context->error_msgs;
	$notice		= '';

	if ($success !== FALSE)
	{
		$notice .=	"<div class='alert alert-success'>".
						"<button type='button' class='close' data-dismiss='alert'>&times;</button>".$success.
					"</div>";
	}

	if ($error !== FALSE)
	{
		$notice .=	"<div class='alert alert-error'>".
						"<button type='button' class='close' data-dismiss='alert'>&times;</button>".$error.
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_wysiwyg($name, $options, $data = FALSE, $context = NULL)
{
	$value	= set_value($name, expr($options->get_path, $data, $options->format));

	$label	= expr($options->disp_src, $data);
	$ctrl	= "<textarea name='{$name}' class='wysiwyg {$options->classes}'>{$value}</textarea>";

	return _control_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a syntax highlighted code box control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_codebox($name, $options, $data = FALSE, $context = NULL)
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_checkbox($name, $options, $data = FALSE, $context = NULL)
{
	$text = expr($options->disp_src, $data);
	$text = explode('<br>', $text);

	if (count($text) >= 2)
	{
		$label = $text[0];
		$field = $text[1];
		$value = htmlspecialchars($field);
	}
	else
	{
		$label = NULL;
		$field = $text[0];
		$value = htmlspecialchars($field);
	}

	$get_val = expr($options->get_path, $data, $options->format);
	$checked = $get_val == $value ? "checked='checked'" : '';

	$ctrl	=	"<label class='checkbox'>".
					"<input name='{$name}' type='checkbox' {$checked} class='{$options->classes}' value='{$value}' /> {$field}".
				"</label>";

	return _control_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a radio button control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_radio($name, $options, $data = FALSE, $context = NULL)
{
	$text = expr($options->disp_src, $data);
	$text = explode('<br>', $text);

	if (count($text) >= 2)
	{
		$label = $text[0];
		$field = $text[1];
		$value = htmlspecialchars($field);
	}
	else
	{
		$label = NULL;
		$field = $text[0];
		$value = htmlspecialchars($field);
	}

	$get_val = expr($options->get_path, $data, $options->format);
	$checked = $get_val == $value ? "checked='checked'" : '';

	$ctrl	=	"<label class='radio'>".
					"<input name='{$name}' type='radio' {$checked} class='{$options->classes}' value='{$value}' /> {$field}".
				"</label>";

	return _control_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a file upload control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_file_upload($name, $options, $data = FALSE, $context = NULL)
{
	$label	= expr($options->disp_src, $data);
	$ctrl	= "<input name='{$name}' type='file' class='{$options->classes}' />";

	return _control_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a file download control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_file_download($name, $options, $data = FALSE, $context = NULL)
{
	$value	= expr($options->get_path, $data, $options->format);
	$info	= @unserialize($value);

	if (is_array($info))
	{
		$name	= $info['name'];
		$url	= base_url($info['url']);

		$label	= expr($options->disp_src, $data);
		$ctrl	= "<i class='icon-download-alt'></i> <a href='{$url}'>{$name}</a>";

		return _control_group($label, $ctrl);
	}

	return NULL;
}

// ------------------------------------------------------------------------

/**
 * Creates a hidden control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_hidden($name, $options, $data = FALSE, $context = NULL)
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_image($name, $options, $data = FALSE, $context = NULL)
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_select($name, $options, $data = FALSE, $context = NULL)
{
	$value	= set_value($name, expr($options->get_path, $data, $options->format));
	$items	= explode("<br>", expr($options->disp_src, $data));
	$label	= NULL;
	$list	= '';

	if (substr($items[0], 0, 1) != '-')
	{
		$label = trim($items[0]);
	}

	foreach ($items as $item)
	{
		if (substr($item, 0, 1) == '-')
		{
			$item		 = strip_tags(trim(substr($item, 1)));
			$selected	 = $item == $value ? "selected='selected'" : '';
			$list		.= "<option value='{$item}' {$selected}>{$item}</option>";
		}
	}

	$ctrl = "<select name='{$name}' class='{$options->classes}'>{$list}</select>";
	return _control_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates a multi-select menu
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_multiselect($name, $options, $data = FALSE, $context = NULL)
{
	$name	= $name.'[]';
	$values	= set_value($name, unserialize(expr($options->get_path, $data, $options->format)));

	if ( ! is_array($values))
	{
		$values = array();
	}

	$items	= explode('<br>', expr($options->disp_src, $data));
	$label	= NULL;
	$list	= '';

	if (substr($items[0], 0, 1) != '-')
	{
		$label = trim($items[0]);
	}

	foreach ($items as $item)
	{
		if (substr($item, 0, 1) == '-')
		{
			$item		 = strip_tags(trim(substr($item, 1)));
			$selected	 = in_array($item, $values) ? "selected='selected'" : '';
			$list		.= "<option value='{$item}' {$selected}>{$item}</option>";
		}
	}

	$ctrl = "<select name='{$name}' multiple='multiple' class='{$options->classes}'>{$list}</select>";
	return _control_group($label, $ctrl);
}

// ------------------------------------------------------------------------

/**
 * Creates progress bar controls
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_progress_bar($name, $options, $data = FALSE, $context = NULL)
{
	$CI			=& get_instance();

	$column		= trim(strip_tags($options->disp_src));
	$hub_name	= $context->hub;
	$columns	= $CI->hub->column_list($hub_name);

	if (in_array($column, $columns))
	{
		$counts	= $CI->hub->count_items($hub_name, $column);
		$total	= $CI->hub->count_all($hub_name);
		$output	= '';

		foreach ($counts as $column => $count)
		{
			$pcnt	= $count / $total * 100;
			$label	= "{$column} ({$count})";

			$bar	=	"<div class='progress {$options->classes}'>".
							"<div class='bar' style='width: {$pcnt}%;'></div>".
						"</div>";

			$output .= _control_group($label, $bar);
		}

		return $output;
	}

	return NULL;
}

// ------------------------------------------------------------------------

/**
 * Creates a modal popup control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_popup_message($name, $options, $data = FALSE, $context = NULL)
{
	$CI			=& get_instance();

	$caption	= $CI->lang->line('message');
	$button		= $CI->lang->line('ok');
	$message	= expr($options->disp_src, $data);

	$modal	=	"<div id='{$name}' class='modal modal-medium hide fade {$options->classes}'>".
					"<div class='modal-header'>".
						"<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>".
						"<h3>{$caption}</h3>".
					"</div>".
					"<div class='modal-body'>".
						"<p>{$message}</p>".
					"</div>".
					"<div class='modal-footer'>".
						"<a href='#' data-dismiss='modal' class='btn'>{$button}</a>".
					"</div>".
				"</div>".
				"<script type='text/javascript'>".
					"$('#{$name}').modal('show');".
				"</script>";

	return $modal;
}

// ------------------------------------------------------------------------

/**
 * Creates a link dropdown control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_drop_links($name, $options, $data = FALSE, $context = NULL)
{
	$text	= trim(expr($options->disp_src, $data));
	$items	= explode('<br>', $text);
	$menu	= '';

	if (substr($items[0], 0, 1) != '-')
	{
		$toggle = trim($items[0]);
		array_shift($items);

		foreach ($items as $item)
		{
			$item	= trim(substr($item, 1));
			$pos	= strpos($item, ':');

			$text	= trim(substr($item, 0, $pos));
			$link	= trim(substr($item, $pos + 1));

			$menu .= "<li><a href='{$link}'>{$text}</a></li>";
		}

		$ctrl = "<div class='dropdown'>".
					"<a class='dropdown-toggle' data-toggle='dropdown' href='#'>".
						$toggle."<b class='caret'></b>".
					"</a>".
					"<ul class='dropdown-menu' role='menu' aria-labelledby='dLabel'>".
						$menu.
					"</ul>".
				"</div>";

		return $ctrl;
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
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_submit_button($name, $options, $data = FALSE, $context = NULL)
{
	$text = strip_tags(expr($options->disp_src, $data));
	return "<input name='{$name}' type='submit' value='{$text}' class='btn {$options->classes}' />";
}

// ------------------------------------------------------------------------

/**
 * Creates a delete link control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_delete_link($name, $options, $data = FALSE, $context = NULL)
{
	$text = expr($options->disp_src, $data);
	return "<input name='{$name}' type='submit' value='{$text}' class='btn btn-link btn-delete {$options->classes}' />";
}

// ------------------------------------------------------------------------

/**
 * Creates a reset button control
 *
 * @access	public
 * @param	string	name of the control
 * @param	object	control options
 * @param	object	data context for expressions
 * @param	object	dynamic data context
 * @return	string	control markup
 */
function control_reset_button($name, $options, $data = FALSE, $context = NULL)
{
	$text = strip_tags(expr($options->disp_src, $data));
	return "<input name='{$name}' type='reset' value='{$text}' class='btn{$options->classes}' />";
}

// ------------------------------------------------------------------------

/**
 * Control group generator
 *
 * @access	private
 * @param	string	control label
 * @param	string	markup for the controls
 * @return	string	control group
 */
function _control_group($label, $controls)
{
	return	"<div class='control-group'>".
				"<label class='control-label'>{$label}</label>".
				"<div class='controls'>{$controls}</div>".
			"</div>";
}

// ------------------------------------------------------------------------


/* End of file control_helper.php */
/* Location: ./application/helpers/control_helper.php */