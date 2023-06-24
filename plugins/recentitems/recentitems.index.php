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
 *
 * @var XTemplate $t
 */
defined('COT_CODE') or die('Wrong URL');

$enforums = $t->hasTag('RECENT_FORUMS');
$enpages = $t->hasTag('RECENT_PAGES');

if ($enpages || $enforums) {
	require_once cot_incfile('recentitems', 'plug');

	if (
        $enpages
        && Cot::$cfg['plugin']['recentitems']['recentpages']
        && cot_module_active('page')
        && cot_auth('page', 'any')
    ) {
		require_once cot_incfile('page', 'module');

        $riPageCacheKey = null;
        $riPageUseCache = false;
		// Try to load from cache for guests
		if (Cot::$usr['id'] == 0 && Cot::$cache && (int) Cot::$cfg['plugin']['recentitems']['cache_ttl'] > 0) {
            $riPageUseCache = true;
            $riPageCacheKey = "$theme.$lang.pages";
            $riHtml = Cot::$cache->disk->get(
                $riPageCacheKey,
                'recentitems',
                (int) Cot::$cfg['plugin']['recentitems']['cache_ttl']
            );
		}

		if (empty($riHtml)) {
            $riHtml = cot_build_recentpages(
                'recentitems.pages.index',
                'recent',
                Cot::$cfg['plugin']['recentitems']['maxpages'],
                0,
                Cot::$cfg['plugin']['recentitems']['recentpagestitle'],
                Cot::$cfg['plugin']['recentitems']['recentpagestext'], Cot::$cfg['plugin']['recentitems']['rightscan']
            );
			if (Cot::$usr['id'] == 0 && Cot::$cache && (int) Cot::$cfg['plugin']['recentitems']['cache_ttl'] > 0) {
                Cot::$cache->disk->store($riPageCacheKey, $riHtml, 'recentitems');
			}
		}

		$t->assign('RECENT_PAGES', $riHtml);
		unset($riHtml);
	}

	if (
        $enforums
        && Cot::$cfg['plugin']['recentitems']['recentforums']
        && cot_module_active('forums')
        && cot_auth('forums', 'any')
    ) {
		require_once cot_incfile('forums', 'module');

        $riForumsCacheKey = null;
        $riForumsUseCache = false;
		// Try to load from cache for guests
		if (Cot::$usr['id'] == 0 && Cot::$cache && (int) Cot::$cfg['plugin']['recentitems']['cache_ttl'] > 0) {
            $riForumsUseCache = true;
            $riForumsCacheKey = "$theme.$lang.forums";
			$riHtml = Cot::$cache->disk->get(
                $riForumsCacheKey,
                'recentitems',
                (int) Cot::$cfg['plugin']['recentitems']['cache_ttl']
            );
		}

		if (empty($riHtml)) {
            $riHtml = cot_build_recentforums(
                'recentitems.forums.index',
                'recent',
                Cot::$cfg['plugin']['recentitems']['maxtopics'],
                0,
                Cot::$cfg['plugin']['recentitems']['recentforumstitle'],
                Cot::$cfg['plugin']['recentitems']['rightscan']
            );

            if ($riForumsUseCache) {
                Cot::$cache->disk->store($riForumsCacheKey, $riHtml, 'recentitems');
			}
		}

		$t->assign('RECENT_FORUMS', $riHtml);
		unset($riHtml);
	}
}
