<?php
/**
 * Setup file for Forums module
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

/**
 * Custom permissions allowed for specified groups.
 * A default mask will be used if not specified.
 */
$auth_permit = array(
	SED_GROUP_DEFAULT => 'RW',
	SED_GROUP_GUESTS => 'R',
	SED_GROUP_INACTIVE => 'R',
	SED_GROUP_BANNED => '0',
	SED_GROUP_MEMBERS => 'RW',
	SED_GROUP_TOPADMINS => 'RW12345A'
);

/**
 * Disable the ability to grant some permissions if needed.
 * A default mask will be used if not specified.
 */
$auth_lock = array(
	SED_GROUP_DEFAULT => '0',
	SED_GROUP_GUESTS => 'W12345A',
	SED_GROUP_INACTIVE => 'W12345A',
	SED_GROUP_BANNED => 'RW12345A',
	SED_GROUP_MEMBERS => 'A',
	SED_GROUP_TOPADMINS => 'RW12345A'
);

/**
 * Configuration options for the module
 */
$config_options = array(
	array(
		'name' => 'disable_forums',
		'type' => COT_CONFIG_TYPE_RADIO,
		'default' => '0'
	),
	array(
		'name' => 'hideprivateforums',
		'type' => COT_CONFIG_TYPE_RADIO,
		'default' => '0'
	),
	array(
		'name' => 'hottopictrigger',
		'type' => COT_CONFIG_TYPE_SELECT,
		'default' => '20',
		'variants' => '5,10,15,20,25,30,35,40,50'
	),
	array(
		'name' => 'maxtopicsperpage',
		'type' => COT_CONFIG_TYPE_SELECT,
		'default' => '30',
		'variants' => '5,10,15,20,25,30,40,50,60,70,100,200,500'
	),
	array(
		'name' => 'antibumpforums',
		'type' => COT_CONFIG_TYPE_RADIO,
		'default' => '0'
	),
	array(
		'name' => 'mergeforumposts',
		'type' => COT_CONFIG_TYPE_RADIO,
		'default' => '1'
	),
	array(
		'name' => 'mergetimeout',
		'type' => COT_CONFIG_TYPE_SELECT,
		'default' => '0',
		'variants' => '0,1,2,3,6,12,24,36,48,72'
	),
	array(
		'name' => 'maxpostsperpage',
		'type' => COT_CONFIG_TYPE_SELECT,
		'default' => '15',
		'variants' => '5,10,15,20,25,30,40,50,60,70,100,200,500'
	)
);

?>
