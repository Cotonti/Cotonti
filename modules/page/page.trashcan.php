<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=trashcan.api
[END_COT_EXT]
==================== */

/**
 * Trash can support for pages
 *
 * @package page
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');

// Register restoration table
$trash_types['page'] = $db_pages;

/**
 * Sync page action
 *
 * @param array $data trashcan item data
 * @return bool
 * @global Cache $cache
 */
function cot_trash_page_sync($data)
{
	global $cache, $cfg, $db_structure;

	cot_page_sync($data['page_cat']);
	($cache && $cfg['cache_page']) && $cache->page->clear('page');
	return true;
}
?>
