<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Forum Statistics
 *
 * @package forumstats
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

require_once cot_incfile('forums', 'module');

$out['subtitle'] = $L['forumstats_title'];

$totalsections = $db->countRows($db_forum_stats); // remake
$totaltopics = $db->countRows($db_forum_topics);
$totalposts = $db->countRows($db_forum_posts);

$sql = $db->query("SELECT SUM(fs_viewcount) FROM $db_forum_stats");
$totalviews = $sql->fetchColumn();

$sql = $db->query("SELECT * FROM $db_forum_topics WHERE 1 ORDER BY ft_postcount DESC LIMIT 10");
$ii = 0;

while ($row = $sql->fetch())
{
	if (cot_auth('forums', $row['ft_cat'], 'R'))
	{
		$ii++;
		$ft_title = $row['ft_title'];
		$ft_title .= ($row['ft_sticky']) ? " (*)" : '';
		$ft_title .= ($row['ft_state']) ? " (x)" : '';
		$t->assign(array(
			'FORUMSTATS_REPLIEDTOP_II' => $ii,
			'FORUMSTATS_REPLIEDTOP_FORUMS' => cot_breadcrumbs(cot_forums_buildpath($row['ft_cat'], false), false),
			'FORUMSTATS_REPLIEDTOP_URL' => cot_url('forums', 'm=posts&q='.$row['ft_id']),
			'FORUMSTATS_REPLIEDTOP_TITLE' => htmlspecialchars($ft_title),
			'FORUMSTATS_REPLIEDTOP_POSTCOUNT' => $row['ft_postcount']
		));
		$t->parse('MAIN.FORUMSTATS_REPLIEDTOP_USER');
	}
	else
	{
		$ii++;
		$t->assign(array(
			'FORUMSTATS_REPLIEDTOP_II' => $ii,
			'FORUMSTATS_REPLIEDTOP_FORUMS' => cot_breadcrumbs(cot_forums_buildpath($row['ft_cat'], false), false),
			'FORUMSTATS_REPLIEDTOP_POSTCOUNT' => $row['ft_postcount']
		));
		$t->parse('MAIN.FORUMSTATS_REPLIEDTOP_NO_USER');
	}
}
$sql->closeCursor();

$sql = $db->query("SELECT * FROM $db_forum_topics WHERE 1 ORDER BY ft_viewcount DESC LIMIT 10");

$ii = 0;

while ($row = $sql->fetch())
{
	if (cot_auth('forums', $row['ft_cat'], 'R'))
	{
		$ii++;
		$ft_title = $row['ft_title'];
		$ft_title .= ($row['ft_sticky'] && $row['ft_state']) ? " (!)" : '';
		$ft_title .= ($row['ft_sticky'] && !$row['ft_state']) ? " (*)" : '';
		$ft_title .= ($row['ft_state'] && !$row['ft_sticky']) ? " (x)" : '';
		$t->assign(array(
			'FORUMSTATS_VIEWEDTOP_II' => $ii,
			'FORUMSTATS_VIEWEDTOP_FORUMS' => cot_breadcrumbs(cot_forums_buildpath($row['ft_cat'], false), false),
			'FORUMSTATS_VIEWEDTOP_URL' => cot_url('forums', 'm=posts&q='.$row['ft_id']),
			'FORUMSTATS_VIEWEDTOP_TITLE' => htmlspecialchars($ft_title),
			'FORUMSTATS_VIEWEDTOP_VIEWCOUNT' => $row['ft_viewcount']
		));
		$t->parse('MAIN.FORUMSTATS_VIEWEDTOP_USER');
	}
	else
	{
		$ii++;
		$t->assign(array(
			'FORUMSTATS_VIEWEDTOP_II' => $ii,
			'FORUMSTATS_VIEWEDTOP_FORUMS' => cot_breadcrumbs(cot_forums_buildpath($row['ft_cat'], false), false),
			'FORUMSTATS_VIEWEDTOP_VIEWCOUNT' => $row['ft_viewcount']
		));
		$t->parse('MAIN.FORUMSTATS_VIEWEDTOP_NO_USER');
	}
}
$sql->closeCursor();

$ii = 0;
$tmpstats = '';
$sql = $db->query("SELECT user_id, user_name, user_postcount
FROM $db_users
WHERE 1 ORDER by user_postcount DESC
LIMIT 10");

while ($row = $sql->fetch())
{
	$ii++;
	$t->assign(array(
		'FORUMSTATS_POSTERSTOP_II' => $ii,
		'FORUMSTATS_POSTERSTOP_USER_NAME' => cot_build_user($row['user_id'], htmlspecialchars($row['user_name'])),
		'FORUMSTATS_POSTERSTOP_USER_POSTCOUNT' => $row["user_postcount"]
	));
	$t->parse('MAIN.POSTERSTOP');
}
$sql->closeCursor();

$t->assign(array(
	'FORUMSTATS_TOTALSECTIONS' => $totalsections,
	'FORUMSTATS_TOTALTOPICS' => $totaltopics,
	'FORUMSTATS_TOTALPOSTS' => $totalposts,
	'FORUMSTATS_TOTALVIEWS' => $totalviews
));

?>