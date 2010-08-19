<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=list.tags
Tags=list.tpl:{LIST_COMMENTS},{LIST_COMMENTS_DISPLAY}
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$t->assign(array(
	'LIST_COMMENTS' => sed_comments_link('list', 'c='.$c, 'list', $c),
	'LIST_COMMENTS_DISPLAY' => sed_comments_display('list', $c)
));

?>