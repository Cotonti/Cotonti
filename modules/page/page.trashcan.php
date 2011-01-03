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
 * @copyright Copyright (c) Cotonti Team 2008-2011
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
 */
function cot_trash_page_sync($data)
{
	global $cache, $cfg, $db_structure;

	cot_forums_resynctopic($data['ft_id']);
	$items = cot_forums_sync($data['ft_cat']);
	$db->update($db_structure, array("structure_count" => (int)$items), "structure_code='".$db->prep($data['ft_cat'])."' AND structure_area='forums'");
	return TRUE;

	cot_page_resync($data['page_cat']);
	($cache && $cfg['cache_page']) && $cache->page->clear('page');
	return true;
}
?>
