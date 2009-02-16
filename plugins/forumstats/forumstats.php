<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

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
 * @package Cotonti
 * @version 0.0.3
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD
 */


if (!defined('SED_CODE') || !defined('SED_PLUG')) { die('Wrong URL.'); }

$plugin_title = $L['plu_title'];

if (!$forumstats)
{
	$totalsections = sed_sql_rowcount($db_forum_sections);
	$totaltopics = sed_sql_rowcount($db_forum_topics);
	$totalposts = sed_sql_rowcount($db_forum_posts);

	$sql = sed_sql_query("SELECT SUM(fs_viewcount) FROM $db_forum_sections");
	$totalviews = sed_sql_result($sql, 0, "SUM(fs_viewcount)");

	$sql = sed_sql_query("SELECT SUM(fs_topiccount_pruned) FROM $db_forum_sections");
	$totaltopics += sed_sql_result($sql, 0, "SUM(fs_topiccount_pruned)");

	$sql = sed_sql_query("SELECT SUM(fs_postcount_pruned) FROM $db_forum_sections");
	$totalposts += sed_sql_result($sql, 0, "SUM(fs_postcount_pruned)");

	$plugin_body = "<table><tr><td>".$L['plu_sections']." :</td><td style=\"text-align:right;\">".$totalsections."</td></tr>";
	$plugin_body .= "<tr><td>".$L['plu_topics']." : </td><td style=\"text-align:right;\">".$totaltopics."</td></tr>";
	$plugin_body .= "<tr><td>".$L['plu_posts']." : </td><td style=\"text-align:right;\">".$totalposts."</td></tr>";
	$plugin_body .= "<tr><td>".$L['plu_views']." : </td><td style=\"text-align:right;\">".$totalviews."</td></tr></table>";

	$plugin_body .= "<h4>".$L['plu_repliedtop10']." :</h4>";

	$sql = sed_sql_query("SELECT t.ft_id, t.ft_title, t.ft_postcount, t.ft_sticky, t.ft_state,
	s.fs_id, s.fs_title, s.fs_category
	FROM $db_forum_topics t
	LEFT JOIN $db_forum_sections s ON t.ft_sectionid=s.fs_id
	WHERE 1
	ORDER BY ft_postcount DESC LIMIT 10");

	$ii=0;

	while ($row = sed_sql_fetcharray($sql))
	if(sed_auth('forums', $row['fs_id'], 'R'))
	{
		$ii++;
		$ft_title=$row['ft_title'];
		$ft_title .= ($row['ft_sticky']) ? " (*)" : '';
		$ft_title .= ($row['ft_state']) ? " (x)" : '';

		$plugin_body .= "#".$ii." : ".sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category'])." ".$cfg['separator']." ";
		$plugin_body .= (sed_auth('forums', $row['fs_id'], 'R')) ? "<a href=\"".sed_url('forums', 'm=posts&q='.$row['ft_id'])."\">".sed_cc($ft_title)."</a> (".$row['ft_postcount'].")" : $L['plu_hidden'];
		$plugin_body .= "<br />";
	}

	$plugin_body .= "<h4>".$L['plu_viewedtop10']." :</h4>";

	$sql = sed_sql_query("SELECT t.ft_id, t.ft_title, t.ft_viewcount, t.ft_sticky, t.ft_state,
	s.fs_id, s.fs_title, s.fs_category
	FROM $db_forum_topics AS t
	LEFT JOIN  $db_forum_sections AS s ON t.ft_sectionid=s.fs_id
	WHERE 1
	ORDER BY ft_viewcount DESC LIMIT 10");

	$ii=0;

	while ($row = sed_sql_fetcharray($sql))
	if(sed_auth('forums', $row['fs_id'], 'R'))
	{
		$ii++;
		$ft_title = $row['ft_title'];
		$ft_title .= ($row['ft_sticky'] && $row['ft_state']) ? " (!)" : '';
		$ft_title .= ($row['ft_sticky'] && !$row['ft_state']) ? " (*)" : '';
		$ft_title .= ($row['ft_state'] && !$row['ft_sticky']) ? " (x)" : '';

		$plugin_body .= "#".$ii." : ".sed_build_forums($row['fs_id'], $row['fs_title'], $row['fs_category'])." ".$cfg['separator']." ";
		$plugin_body .= (sed_auth('forums', $row['fs_id'], 'R')) ? "<a href=\"".sed_url('forums', 'm=posts&q='.$row['ft_id'])."\">".sed_cc($ft_title)."</a> (".$row['ft_viewcount'].")"  : $L['plu_hidden'];
		$plugin_body .= "<br />";
	}

	$plugin_body .= "<h4>".$L['plu_posterstop10']." :</h4>";

	$ii=0;
	$tmpstats = '';
	$sql = sed_sql_query("SELECT user_id, user_name, user_postcount
	FROM $db_users
	WHERE 1 ORDER by user_postcount DESC
	LIMIT 10");

	while ($row = sed_sql_fetcharray($sql))
	{
		$ii++;
		$plugin_body .= "#".$ii." : ".sed_build_user($row['user_id'], sed_cc($row['user_name']))." (".$row["user_postcount"].")<br />";
	}

	sed_cache_store('forumstats', $plugin_body, 600);
}
else
{ $plugin_body = $forumstats; }

?>
