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
$trash_types['forumpost'] = Cot::$db->forum_posts;
$trash_types['forumtopic'] = Cot::$db->forum_topics;

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
	if ($row = $sql->fetch()) {
		return true;
	}
	return false;
}

/**
 * Sync forumpost action
 *
 * @param array $data trashcan item data
 * @return bool
 */
function cot_trash_forumpost_sync($data)
{
    cot_forums_resyncTopic($data['fp_topicid']);
	cot_forums_updateStructureCounters($data['fp_cat']);

	return true;
}

/**
 * Sync forumtopic action
 * @param array $data trashcan item data
 * @return bool
 */
function cot_trash_forumtopic_sync($data)
{
	cot_forums_resyncTopic($data['ft_id']);
    cot_forums_updateStructureCounters($data['fp_cat']);

    if (\Cot::$cache && Cot::$cfg['cache_forums']) {
        \Cot::$cache->static->clearByUri(cot_url('forums'));
    }

    return true;
}
