<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags
Tags=index.tpl:{PLUGIN_LATESTPAGES}
[END_COT_EXT]
==================== */

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package recentitmes
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot_require('users');
cot_require('recentitems', true);

if ($cfg['plugin']['recentitems']['recentpages'] && !$cfg['disable_page'])
{
	$res = cot_build_recentpages('recentitems.pages.index', 'recent', $cfg['plugin']['recentitems']['maxpages'], 0, $cfg['plugin']['recentitems']['recentpagestitle'], $cfg['plugin']['recentitems']['recentpagestext'], $cfg['plugin']['recentitems']['rightscan']);
	$t->assign("PLUGIN_LATESTPAGES", $res);
}

if ($cfg['plugin']['recentitems']['recentforums'] && !$cfg['disable_forums'])
{
	$res = cot_build_recentforums('recentitems.forums.index', 'recent', $cfg['plugin']['recentitems']['maxtopics'], 0, $cfg['plugin']['recentitems']['recentforumstitle'], $cfg['plugin']['recentitems']['rightscan']);
	$t->assign("PLUGIN_LATESTTOPICS", $res);
}

?>