<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=news.loop
File=comments.news.loop
Hooks=news.loop
Tags=news.tpl:{PAGE_ROW_COMMENTS}
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

list($pag['page_comments'], $pag['page_comments_display']) = sed_build_comments('p' . $pag['page_id'], $pag['page_pageurl'], FALSE);
$news->assign(array(
	"PAGE_ROW_COMMENTS" => $pag['page_comments']
));

?>