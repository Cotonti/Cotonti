<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=recentitems.recentpages.tags
File=comments.recentitems.recentpages.tags
Hooks=recentitems.recentpages.tags
Tags=recentitems.tpl:{PAGE_ROW_COMMENTS}
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
$recentitems->assign(array(
	"PAGE_ROW_COMMENTS" => $pag['page_comments']
));

?>