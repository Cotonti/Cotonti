<?php

/* ====================
[BEGIN_COT_EXT]
Hooks=index.tags,header.tags,footer.tags
Tags=index.tpl:{RECENT_PAGES},{RECENT_FORUMS};header.tpl:{RECENT_PAGES},{RECENT_FORUMS};footer.tpl:{RECENT_PAGES},{RECENT_FORUMS}
Order=10,20,20
[END_COT_EXT]
==================== */

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package RecentItems
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

$enforums = $t->hasTag('RECENT_FORUMS');
$enpages = $t->hasTag('RECENT_PAGES');
if ($enpages || $enforums)
{
	require_once cot_incfile('recentitems', 'plug');

	if ($enpages && $cfg['plugin']['recentitems']['recentpages'] && cot_module_active('page'))
	{
		require_once cot_incfile('page', 'module');

		// Try to load from cache for guests
		if ($usr['id'] == 0 && $cache && (int) $cfg['plugin']['recentitems']['cache_ttl'] > 0)
		{
			$ri_cache_id = "$theme.$lang.pages";
			$ri_html = $cache->disk->get($ri_cache_id, 'recentitems', (int)$cfg['plugin']['recentitems']['cache_ttl']);
		}

		if (empty($ri_html))
		{
			$ri_html = cot_build_recentpages('recentitems.pages.index', 'recent', $cfg['plugin']['recentitems']['maxpages'], 0, $cfg['plugin']['recentitems']['recentpagestitle'], $cfg['plugin']['recentitems']['recentpagestext'], $cfg['plugin']['recentitems']['rightscan']);
			if ($usr['id'] == 0 && $cache && (int)$cfg['plugin']['recentitems']['cache_ttl'] > 0)
			{
				$cache->disk->store($ri_cache_id, $ri_html, 'recentitems');
			}
		}

		$t->assign('RECENT_PAGES', $ri_html);
		unset($ri_html);
	}

	if ($enforums && $cfg['plugin']['recentitems']['recentforums'] && cot_module_active('forums'))
	{
		require_once cot_incfile('forums', 'module');

		// Try to load from cache for guests
		if ($usr['id'] == 0 && $cache && (int)$cfg['plugin']['recentitems']['cache_ttl'] > 0)
		{
			$ri_cache_id = "$theme.$lang.forums";
			$ri_html = $cache->disk->get($ri_cache_id, 'recentitems', (int) $cfg['plugin']['recentitems']['cache_ttl']);
		}

		if (empty($ri_html))
		{
			$ri_html = cot_build_recentforums('recentitems.forums.index', 'recent', $cfg['plugin']['recentitems']['maxtopics'], 0, $cfg['plugin']['recentitems']['recentforumstitle'], $cfg['plugin']['recentitems']['rightscan']);
			if ($usr['id'] == 0 && $cache && (int)$cfg['plugin']['recentitems']['cache_ttl'] > 0)
			{
				$cache->disk->store($ri_cache_id, $ri_html, 'recentitems');
			}
		}

		$t->assign('RECENT_FORUMS', $ri_html);
		unset($ri_html);
	}
}
