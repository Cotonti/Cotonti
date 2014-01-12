<?php
/**
 * Array of styles and levels in tag cloud
 * max. entries => CSS class name
 *
 * @package tags
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2014
 * @license BSD
 */

$tc_styles = array(
	1 => 'xs',
	5 => 's',
	20 => 'm',
	50 => 'l',
	999999999 => 'xl'
);

cot::$db->registerTable('tags');
cot::$db->registerTable('tag_references');
