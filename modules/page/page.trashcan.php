<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=trashcan.api
[END_COT_EXT]
==================== */

/**
 * Trash can support for pages
 *
 * @package Page
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');

// Register restoration table
$trash_types['page'] = Cot::$db->pages;

/**
 * Sync page action
 *
 * @param array $data trashcan item data
 * @return bool
 * @global Cache $cache
 *
 * @todo проверить
 */
function cot_trash_page_sync($data)
{
    cot_page_updateStructureCounters($data['page_cat']);
    if (Cot::$cache && Cot::$cfg['cache_page']) {
        Cot::$cache->page->clear('page');
    }
	return true;
}
