<?php

/**
 * Parameters for ratings config implantation into modules
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

// Options for implantation
$ratingsOptions = [
	[
		'name' => 'enable_ratings',
		'type' => COT_CONFIG_TYPE_RADIO,
		'default' => '1',
	],
];

// Modules list to implant into their root config
$ratingsModulesList = [];

// Module list to implant into their structure config
$ratingsModulesStructList = ['page'];
