<?php
/**
 * Array of styles and levels in tag cloud
 * max. entries => CSS class name
 *
 * @package tags
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

$tc_styles = array(
	1 => 'xs',
	5 => 's',
	20 => 'm',
	50 => 'l',
	999999999 => 'xl'
);

$db_tags	= (isset($db_tags)) ? $db_tags : $db_x . 'tags';
$db_tag_references = (isset($db_tag_references)) ? $db_tag_references : $db_x . 'tag_references';

?>