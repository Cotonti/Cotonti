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

list($comments_link, $comments_display) = sed_build_comments('v'.$id, sed_url('polls', 'id='.$id), true);
$t->assign(array(
	"POLLS_COMMENTS" => $comments_link,
	"POLLS_COMMENTS_DISPLAY" => $comments_display
));

?>