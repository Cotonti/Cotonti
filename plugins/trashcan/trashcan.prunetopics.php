<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.functions.prunetopics
[END_COT_EXT]
==================== */

/**
 * Trashcan delete post
 *
 * @package trashcan
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
cot_require('trashcan', true);

if ($cfg['plugin']['trashcan']['trash_forum'])
{
	$sql = $cot_db->query("SELECT * FROM $db_forum_topics WHERE ft_id='$q'");
	while ($row = $sql->fetch())
	{
		$parenttrashid = cot_trash_put('forumtopic', $L['Topic']." #".$q." (no post left)", "q".$q, $row);
		cot_trash_put('forumpost', $L['Post']." #".$row['fp_id']." from topic #".$q, "p".$row['fp_id']."-q".$q, "fp_topicid='$q'", $parenttrashid);
	}
}
?>