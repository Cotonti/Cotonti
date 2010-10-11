<?php
/**
 * Array of styles and levels in tag cloud
 * max. entries => CSS class name
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

$GLOBALS['tc_styles'] = array(
	1 => 'xs',
	5 => 's',
	20 => 'm',
	50 => 'l',
	999999999 => 'xl'
);

$GLOBALS['db_tags']	= (isset($GLOBALS['db_tags'])) ? $GLOBALS['db_tags'] : $GLOBALS['db_x'] . 'tags';
$GLOBALS['db_tag_references'] = (isset($GLOBALS['db_tag_references'])) ? $GLOBALS['db_tag_references'] : $GLOBALS['db_x'] . 'tag_references';

?>