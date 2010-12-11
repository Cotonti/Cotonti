<?php
/**
 * 0.8.0: sync forums structure
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

global $db_structure, $db_forum_stats, $structure, $db_x, $db_forum_topics, $db_forum_posts;
require_once cot_incfile('forums', 'module');

$db->query("TRUNCATE TABLE $db_forum_stats");

foreach ($structure['forums'] as $i => $x)
{
	cot_forums_insertcat($i);
	cot_forums_sync($i);
}
?>
