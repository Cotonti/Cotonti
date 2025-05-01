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
 * @param array $data Page data as array (trashcan item data)
 * @return bool
 */
function cot_trash_page_sync($data)
{
    // Check if alias exists
    $page_count = Cot::$db->query(
        'SELECT COUNT(*) FROM ' . Cot::$db->pages . ' WHERE page_alias = ? AND page_id != ?',
        [$data['page_alias'], $data['page_id']]
    )->fetchColumn();
    
    if ($page_count > 0) {
        // If alias exists, generate a new one
        $data['page_alias'] = $data['page_alias'] . '_restored_' . $data['page_id'];
    }

    cot_page_updateStructureCounters($data['page_cat']);
    if (\Cot::$cache) {
        if (\Cot::$cfg['cache_page']) {
            \Cot::$cache->static->clearByUri(cot_page_url($data));
            \Cot::$cache->static->clearByUri(cot_url('page', ['c' => $data['page_cat']]));
        }
        if (Cot::$cfg['cache_index']) {
            Cot::$cache->static->clear('index');
        }
    }
	return true;
}
