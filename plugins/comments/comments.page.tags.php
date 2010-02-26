<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=page.tags
File=comments.page.tags
Hooks=page.tags
Tags=page.tpl:{PAGE_COMMENTS},{PAGE_COMMENTS_DISPLAY},{PAGE_COMMENTS_COUNT},{PAGE_COMMENTS_RSS}
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

list($comments_link, $comments_display, $comments_count) = sed_build_comments('p'.$pag['page_id'], $pag['page_pageurl'], ($sed_cat[$pag['page_cat']]['com']) ? true : false);
$t->assign(array(
	"PAGE_COMMENTS" => $comments_link,
	"PAGE_COMMENTS_DISPLAY" => $comments_display,
	"PAGE_COMMENTS_COUNT" => $comments_count,
	"PAGE_COMMENTS_RSS" => sed_url('rss', 'c=comments&id=' . $pag['page_id'])
));

?>