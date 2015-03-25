<?php
/**
 * Parameters for ratings config implantation into modules
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
