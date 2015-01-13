<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.functions.prunetopics
[END_COT_EXT]
==================== */

/**
 * Trashcan delete post
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');


if ($cfg['plugin']['trashcan']['trash_forum'])
{
	// We are inside cot_forum_prunetopics() function, so need some globals
	global $trash_types, $db_trash, $db_x;
	require_once cot_incfile('trashcan', 'plug');

	$sql = $db->query("SELECT * FROM $db_forum_topics WHERE ft_id=$q");
	while ($row = $sql->fetch())
	{
		$parenttrashid = cot_trash_put('forumtopic', $L['Topic']." #".$q, $q, $row);
		cot_trash_put('forumpost', 'Posts topic #'.$q, 0, "fp_topicid=$q", $parenttrashid);
	}
	$sql->closeCursor();
}
