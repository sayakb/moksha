<?php

/**
 * Control management model
 *
 * Model for adding, editing and deleting controls
 *
 * @package		Moksha
 * @category	Administration
 * @author		Moksha Team
 */
class Controls_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Add a new control to the DB
	 *
	 * @access	public
	 * @return	bool	true if succeeded
	 */
	public function add_control()
	{
		$control_name	= $this->input->post('control_name');
		$controls		= $this->input->post('controls');
		$controls_ary	= explode('|', $controls);

		return $this->control->add($control_name, $controls_ary);
	}

	// --------------------------------------------------------------------

	/**
	 * Updated an existing control
	 *
	 * @access	public
	 * @return	bool	true if succeeded
	 */
	public function update_control($control_id)
	{
		$control_name	= $this->input->post('control_name');
		$controls		= $this->input->post('controls');
		$controls_ary	= explode('|', $controls);

		return $this->control->update($control_id, $control_name, $controls_ary);
	}

	// --------------------------------------------------------------------
}