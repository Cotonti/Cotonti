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
 * Module title
 */
$title = 'Forums';

/**
 * This is displayed as last modification date in Admin panel.
 * Modify this every time you make valuable changes in the module
 */
$date = '2010-02-18';

/**
 * Increase this every time you add SQL/PHP patches, make changes in DB, etc.
 * This will help auto-updater to know how to update the module.
 * Supports SVN Rev keyword and plain integer values
 */
$revision = '$Rev$';

/**
 * Custom permissions allowed for specified groups.
 * A default mask will be used if not specified.
 */
$auth_permit = array(
	COT_GROUP_DEFAULT => 'RW',
	COT_GROUP_GUESTS => 'R',
	COT_GROUP_INACTIVE => 'R',
	COT_GROUP_BANNED => '0',
	COT_GROUP_MEMBERS => 'RW',
	COT_GROUP_SUPERADMINS => 'RW12345A'
);

/**
 * Disable the ability to grant some permissions if needed.
 * A default mask will be used if not specified.
 */
$auth_lock = array(
	COT_GROUP_DEFAULT => '0',
	COT_GROUP_GUESTS => 'W12345A',
	COT_GROUP_INACTIVE => 'W12345A',
	COT_GROUP_BANNED => 'RW12345A',
	COT_GROUP_MEMBERS => 'A',
	COT_GROUP_SUPERADMINS => 'RW12345A'
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

/**
 * Binds module or plugin parts to hooks.
 * Format: 'part_name' => 'hook.name'
 * This will bind modulename.part_name.php to a hook hook.name
 */
$hook_bindings = array(
	array(
		'part' => 'rss',
		'hook' => 'rss.main',
		'order' => 20
	)
);

/**
 * Other modules required for this one to work
 */
$dependencies = array();

?>
