<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=polls.view.tags
File=comments.polls.view.tags
Hooks=polls.view.tags
Tags=polls.tpl:{POLLS_COMMENTS},{POLLS_COMMENTS_DISPLAY}
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

require_once sed_langfile('comments');
require_once sed_incfile('config', 'comments', true);
require_once sed_incfile('functions', 'comments', true);
require_once sed_incfile('resources', 'comments', true);

$t->assign(array(
	'POLLS_COMMENTS' => sed_comments_link('polls', 'id='.$id, 'polls', $id),
	'POLLS_COMMENTS_DISPLAY' => sed_comments_display('polls', $id)
));

?>