<?php

/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags,header.tags
Tags=index.tpl:{RECENT_PAGES},{RECENT_FORUMS};header.tpl:{RECENT_PAGES},{RECENT_FORUMS}
Order=10,20
[END_COT_EXT]
==================== */

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package recentitmes
 * @version 0.9.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

if (cot_get_caller() == $cfg['plugin']['recentitems']['part'])
{
	require_once cot_incfile('users', 'module');
	require_once cot_incfile('recentitems', 'plug');

	if ($cfg['plugin']['recentitems']['recentpages'] && cot_module_active('page'))
	{
		require_once cot_incfile('page', 'module');
		$res = cot_build_recentpages('recentitems.pages.index', 'recent', $cfg['plugin']['recentitems']['maxpages'], 0, $cfg['plugin']['recentitems']['recentpagestitle'], $cfg['plugin']['recentitems']['recentpagestext'], $cfg['plugin']['recentitems']['rightscan']);
		$t->assign('RECENT_PAGES', $res);
	}

	if ($cfg['plugin']['recentitems']['recentforums'] && cot_module_active('forums'))
	{
		require_once cot_incfile('forums', 'module');
		$res = cot_build_recentforums('recentitems.forums.index', 'recent', $cfg['plugin']['recentitems']['maxtopics'], 0, $cfg['plugin']['recentitems']['recentforumstitle'], $cfg['plugin']['recentitems']['rightscan']);
		$t->assign('RECENT_FORUMS', $res);
	}
}

?>