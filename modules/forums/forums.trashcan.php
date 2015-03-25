<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=trashcan.api
[END_COT_EXT]
==================== */

/**
 * Trash can support for forums
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forums', 'module');

// Register restoration table
$trash_types['forumpost'] = $db_forum_posts;
$trash_types['forumtopic'] = $db_forum_topics;

/**
 * Check forumpost action
 *
 * @param array $data trashcan item data
 * @return bool
 * @global CotDB $db
 */
function cot_trash_forumpost_check($data)
{
	global $db_forum_posts, $db_forum_topics, $db;
	$sql = $db->query("SELECT ft_id FROM $db_forum_topics WHERE ft_id='".$data['fp_topicid']."'");
	if ($row = $sql->fetch())
	{
		return true;
	}
	return false;
}

/**
 * Sync forumpost action
 *
 * @param array $data trashcan item data
 * @return bool
 * @global CotDB $db
 */
function cot_trash_forumpost_sync($data)
{
	global $db, $db_structure;
	cot_forums_resynctopic($data['ft_id']);
	$items = cot_forums_sync($data['ft_cat']);
	$db->update($db_structure, array("structure_count" => (int)$items), "structure_code='".$db->prep($data['ft_cat'])."' AND structure_area='forums'");
	return TRUE;
}

/**
 * Sync forumtopic action
 *
 * @param array $data trashcan item data
 * @return bool
 * @global CotDB $db
 */
function cot_trash_forumtopic_sync($data)
{
	global $db, $db_structure;
	cot_forums_resynctopic($data['ft_id']);
	$items = cot_forums_sync($data['ft_cat']);
	$db->update($db_structure, array("structure_count" => (int)$items), "structure_code='".$db->prep($data['ft_cat'])."' AND structure_area='forums'");
	return TRUE;
}
