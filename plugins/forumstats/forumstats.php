<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=forumstats
Part=main
File=forumstats
Hooks=standalone
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Statistics for the forums
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') && defined('SED_PLUG') or die('Wrong URL');

$plugin_title = $L['plu_title'];

$totalsections = sed_sql_rowcount($db_forum_sections);
$totaltopics = sed_sql_rowcount($db_forum_topics);
$totalposts = sed_sql_rowcount($db_forum_posts);

$sql = sed_sql_query("SELECT SUM(fs_viewcount) FROM $db_forum_sections");
$totalviews = sed_sql_result($sql, 0, "SUM(fs_viewcount)");

$sql = sed_sql_query("SELECT SUM(fs_topiccount_pruned) FROM $db_forum_sections");
$totaltopics += sed_sql_result($sql, 0, "SUM(fs_topiccount_pruned)");

$sql = sed_sql_query("SELECT SUM(fs_postcount_pruned) FROM $db_forum_sections");
$totalposts += sed_sql_result($sql, 0, "SUM(fs_postcount_pruned)");

$sql = sed_sql_query("SELECT t.ft_id, t.ft_title, t.ft_postcount, t.ft_sticky, t.ft_state,
s.fs_id, s.fs_title, s.fs_category
FROM $db_forum_topics t
LEFT JOIN $db_forum_sections s ON t.ft_sectionid=s.fs_id
WHERE 1
ORDER BY ft_postcount DESC LIMIT 10");

$ii=0;

while($row = sed_sql_fetcharray($sql))
{
	if(sed_auth('forums', $row['fs_id'], 'R'))
	{
		$ii++;
		$ft_title = $row['ft_title'];
		$ft_title .= ($row['ft_sticky']) ? " (*)" : '';
		$ft_title .= ($row['ft_state']) ? " (x)" : '';
		$t -> assign(array(
			'FORUMSTATS_REPLIEDTOP_II' => $ii,
			'FORUMSTATS_REPLIEDTOP_FORUMS' => sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category']),
			'FORUMSTATS_REPLIEDTOP_URL' => sed_url('forums', 'm=posts&q='.$row['ft_id']),
			'FORUMSTATS_REPLIEDTOP_TITLE' => htmlspecialchars($ft_title),
			'FORUMSTATS_REPLIEDTOP_POSTCOUNT' => $row['ft_postcount']
		));
		$t -> parse('MAIN.FORUMSTATS_REPLIEDTOP_USER');
	}
	else
	{
		$ii++;
		$t -> assign(array(
			'FORUMSTATS_REPLIEDTOP_II' => $ii,
			'FORUMSTATS_REPLIEDTOP_FORUMS' => sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category']),
			'FORUMSTATS_REPLIEDTOP_POSTCOUNT' => $row['ft_postcount']
		));
		$t -> parse('MAIN.FORUMSTATS_REPLIEDTOP_NO_USER');
	}
}

$sql = sed_sql_query("SELECT t.ft_id, t.ft_title, t.ft_viewcount, t.ft_sticky, t.ft_state,
s.fs_id, s.fs_title, s.fs_category
FROM $db_forum_topics AS t
LEFT JOIN  $db_forum_sections AS s ON t.ft_sectionid=s.fs_id
WHERE 1
ORDER BY ft_viewcount DESC LIMIT 10");

$ii=0;

while($row = sed_sql_fetcharray($sql))
{
	if(sed_auth('forums', $row['fs_id'], 'R'))
	{
		$ii++;
		$ft_title = $row['ft_title'];
		$ft_title .= ($row['ft_sticky'] && $row['ft_state']) ? " (!)" : '';
		$ft_title .= ($row['ft_sticky'] && !$row['ft_state']) ? " (*)" : '';
		$ft_title .= ($row['ft_state'] && !$row['ft_sticky']) ? " (x)" : '';
		$t -> assign(array(
			'FORUMSTATS_VIEWEDTOP_II' => $ii,
			'FORUMSTATS_VIEWEDTOP_FORUMS' => sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category']),
			'FORUMSTATS_VIEWEDTOP_URL' => sed_url('forums', 'm=posts&q='.$row['ft_id']),
			'FORUMSTATS_VIEWEDTOP_TITLE' => htmlspecialchars($ft_title),
			'FORUMSTATS_VIEWEDTOP_VIEWCOUNT' => $row['ft_viewcount']
		));
		$t -> parse('MAIN.FORUMSTATS_VIEWEDTOP_USER');
	}
	else
	{
		$ii++;
		$t -> assign(array(
			'FORUMSTATS_VIEWEDTOP_II' => $ii,
			'FORUMSTATS_VIEWEDTOP_FORUMS' => sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category']),
			'FORUMSTATS_VIEWEDTOP_VIEWCOUNT' => $row['ft_viewcount']
		));
		$t -> parse('MAIN.FORUMSTATS_VIEWEDTOP_NO_USER');
	}
}

$ii=0;
$tmpstats = '';
$sql = sed_sql_query("SELECT user_id, user_name, user_postcount
FROM $db_users
WHERE 1 ORDER by user_postcount DESC
LIMIT 10");

while($row = sed_sql_fetcharray($sql))
{
	$ii++;
	$t -> assign(array(
		'FORUMSTATS_POSTERSTOP_II' => $ii,
		'FORUMSTATS_POSTERSTOP_USER_NAME' => sed_build_user($row['user_id'], htmlspecialchars($row['user_name'])),
		'FORUMSTATS_POSTERSTOP_USER_POSTCOUNT' => $row["user_postcount"]
	));
	$t -> parse('MAIN.POSTERSTOP');
}

$t -> assign(array(
	'FORUMSTATS_TOTALSECTIONS' => $totalsections,
	'FORUMSTATS_TOTALTOPICS' => $totaltopics,
	'FORUMSTATS_TOTALPOSTS' => $totalposts,
	'FORUMSTATS_TOTALVIEWS' => $totalviews,
));

?>