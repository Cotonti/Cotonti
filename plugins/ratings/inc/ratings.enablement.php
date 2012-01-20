<?php
/**
 * Parameters for ratings config implantation into modules
 *
 * @package ratings
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

// Options for implantation
$rat_options = array(
	array(
		'name' => 'enable_ratings',
		'type' => COT_CONFIG_TYPE_RADIO,
		'default' => '1'
	)
);

// Modules list to implant into their root config
$rat_modules_list = array();

// Module list to implant into their structure config
$rat_modules_struct_list = array('page');
?>
