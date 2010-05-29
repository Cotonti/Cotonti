<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=list.tags
File=comments.list.tags
Hooks=list.tags
Tags=list.tpl:{LIST_COMMENTS},{LIST_COMMENTS_DISPLAY}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

list($list_comments, $list_comments_display) = sed_build_comments('list_'.$c, sed_url('list', 'c='.$c), $cat['com']);
$t->assign(array(
	'LIST_COMMENTS' => sed_comments_link('list', 'c='.$c, 'list', $c),
	'LIST_COMMENTS_DISPLAY' => sed_comments_display('list', $c)
));

?>