<?php
/**
 * Array of styles and levels in tag cloud
 * max. entries => CSS class name
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
