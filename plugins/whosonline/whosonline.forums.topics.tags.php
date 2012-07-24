<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.tags
Tags=forums.topics.tpl:{FORUMS_TOPICS_VIEWERS},{FORUMS_TOPICS_VIEWER_NAMES}
[END_COT_EXT]
==================== */

/**
 * Forums online users in section display
 *
 * @package whosonline
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['forums']['cat_' . $s]['allowviewers'])
{

	$v = 0;
	$sql_forums_view = $db->query("SELECT online_name, online_userid FROM $db_online WHERE online_location='Forums' AND online_subloc=".$db->quote($structure['forums'][$s]['title']));
	while ($rowv = $sql_forums_view->fetch())
	{
		if ($rowv['online_name'] != 'v')
		{
			$fs_viewers_names .= ($v>0) ? ', ' : '';
			$fs_viewers_names .= cot_build_user($rowv['online_userid'], htmlspecialchars($rowv['online_name']));
			$v++;
		}
	}
	$sql_forums_view->closeCursor();
	$fs_viewers = $v;

	$t->assign(array(
		'FORUMS_TOPICS_VIEWERS' => $fs_viewers,
		'FORUMS_TOPICS_VIEWER_NAMES' => $fs_viewers_names
	));
	$t->parse('MAIN.FORUMS_SECTIONS_VIEWERS');
}

?>