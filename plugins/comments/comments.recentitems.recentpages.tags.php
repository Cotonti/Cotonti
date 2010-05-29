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

require_once sed_langfile('comments');
require_once sed_incfile('config', 'comments', true);
require_once sed_incfile('functions', 'comments', true);
require_once sed_incfile('resources', 'comments', true);

$page_urlp = empty($pag['page_alias']) ? 'id='.$pag['page_id'] : 'al='.$pag['page_alias'];
$recentitems->assign(array(
	'PAGE_ROW_COMMENTS' => sed_comments_link('page', $page_urlp, 'page', $pag['page_id'], $pag['page_cat'])
));

?>