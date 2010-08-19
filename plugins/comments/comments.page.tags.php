<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.tags
Tags=page.tpl:{PAGE_COMMENTS},{PAGE_COMMENTS_DISPLAY},{PAGE_COMMENTS_COUNT},{PAGE_COMMENTS_RSS}
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

require_once sed_langfile('comments');
require_once sed_incfile('config', 'comments', true);
require_once sed_incfile('functions', 'comments', true);
require_once sed_incfile('resources', 'comments', true);

$page_urlp = empty($pag['page_alias']) ? 'id='.$pag['page_id'] : 'al='.$pag['page_alias'];
$t->assign(array(
	'PAGE_COMMENTS' => sed_comments_link('page', $page_urlp, 'page', $pag['page_id'], $pag['page_cat']),
	'PAGE_COMMENTS_DISPLAY' => sed_comments_display('page', $pag['page_id'], $pag['page_cat']),
	'PAGE_COMMENTS_COUNT' => sed_comments_count('page', $pag['page_id']),
	'PAGE_COMMENTS_RSS' => sed_url('rss', 'c=comments&id=' . $pag['page_id'])
));

?>