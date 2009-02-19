<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=statistics
Part=main
File=statistics
Hooks=standalone
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Displays statistics info
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if(!defined('SED_CODE') || !defined('SED_PLUG')) { die('Wrong URL.'); }

$s = sed_import('s','G','TXT');

$plugin_title = $L['plu_title'];

$totaldbpages = sed_sql_rowcount($db_pages);
$totaldbcomments = sed_sql_rowcount($db_com);
$totaldbratings = sed_sql_rowcount($db_ratings);
$totaldbratingsvotes = sed_sql_rowcount($db_rated);
$totaldbpolls = sed_sql_rowcount($db_polls);
$totaldbpollsvotes = sed_sql_rowcount($db_polls_voters);
$totaldbposts = sed_sql_rowcount($db_forum_posts);
$totaldbtopics = sed_sql_rowcount($db_forum_topics);
$totaldbfiles = sed_sql_rowcount($db_pfs);
$totaldbusers = sed_sql_rowcount($db_users);

$totalpages = sed_stat_get('totalpages');
$totalmailsent = sed_stat_get('totalmailsent');
$totalpmsent = sed_stat_get('totalpms');

$totaldbviews = sed_sql_query("SELECT SUM(fs_viewcount) FROM $db_forum_sections");
$totaldbviews = sed_sql_result($totaldbviews,0,"SUM(fs_viewcount)");

$sql = sed_sql_query("SELECT SUM(fs_topiccount_pruned) FROM $db_forum_sections");
$totaldbtopicspruned = sed_sql_result($sql,0,"SUM(fs_topiccount_pruned)");

$sql = sed_sql_query("SELECT SUM(fs_postcount_pruned) FROM $db_forum_sections");
$totaldbpostspruned = sed_sql_result($sql,0,"SUM(fs_postcount_pruned)");

$totaldbfilesize = sed_sql_query("SELECT SUM(pfs_size) FROM $db_pfs");
$totaldbfilesize = sed_sql_result($totaldbfilesize,0,"SUM(pfs_size)");

$totalpmactive = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_state<2");
$totalpmactive = sed_sql_result($totalpmactive,0,"COUNT(*)");

$totalpmarchived = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_state=2");
$totalpmarchived = sed_sql_result($totalpmarchived,0,"COUNT(*)");

$totalpmold = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_state=3");
$totalpmold = sed_sql_result($totalpmold,0,"COUNT(*)");

$sql = sed_sql_query("SELECT stat_name FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name ASC LIMIT 1");
$row = sed_sql_fetcharray($sql);
$since = $row['stat_name'];

$sql = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_value DESC LIMIT 1");
$row = sed_sql_fetcharray($sql);
$max_date = $row['stat_name'];
$max_hits = $row['stat_value'];

if($usr['id']>0)
{
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_posterid='".$usr['id']."'");
	$user_postscount = sed_sql_result($sql,0,"COUNT(*)");
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_firstposterid='".$usr['id']."'");
	$user_topicscount = sed_sql_result($sql,0,"COUNT(*)");
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_com WHERE com_authorid='".$usr['id']."'");
	$user_comments = sed_sql_result($sql,0,"COUNT(*)");

	$t->assign(array(
		'STATISTICS_USER_POSTSCOUNT' => $user_postscount,
		'STATISTICS_USER_TOPICSCOUNT' => $user_topicscount,
		'STATISTICS_USER_COMMENTS' => $user_comments
	));
	$t->parse('MAIN.IS_USER');
}
else
{
	$t->parse('MAIN.IS_NOT_USER');
}

if($s == 'usercount')
{
	$sql1 = sed_sql_query("DROP TEMPORARY TABLE IF EXISTS tmp1");
	$sql = sed_sql_query("CREATE TEMPORARY TABLE tmp1 SELECT user_country, COUNT(*) as usercount FROM $db_users GROUP BY user_country");
	$sql = sed_sql_query("SELECT * FROM tmp1 WHERE 1 ORDER by usercount DESC");
	$sql1 = sed_sql_query("DROP TEMPORARY TABLE IF EXISTS tmp1");
}
else
{
	$sql = sed_sql_query("SELECT user_country, COUNT(*) as usercount FROM $db_users GROUP BY user_country ASC");
}

$sqltotal = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE 1");
$totalusers = sed_sql_result($sqltotal,0,"COUNT(*)");

$ii = 0;

while($row = sed_sql_fetcharray($sql))
{
	$country_code = $row['user_country'];

	if(!empty($country_code) && $country_code!='00')
	{
		$country_name = sed_build_country($country_code);
		$country_flag = sed_build_flag($country_code);
		$ii = $ii + $row['usercount'];
		$t->assign(array(
			'STATISTICS_COUNTRY_FLAG' => $country_flag,
			'STATISTICS_COUNTRY_COUNT' => $country_count,
			'STATISTICS_COUNTRY_NAME' => $country_name
		));
		$t->parse('MAIN.ROW_COUNTRY');
	}
}

$t->assign(array(
	'STATISTICS_PLU_URL' => sed_url('plug', 'e=statistics'),
	'STATISTICS_SORT_BY_USERCOUNT' => sed_url('plug', 'e=statistics&s=usercount'),
	'STATISTICS_MAX_DATE' => $max_date,
	'STATISTICS_MAX_HITS' => $max_hits,
	'STATISTICS_SINCE' => $since,
	'STATISTICS_TOTALPAGES' => $totalpages,
	'STATISTICS_TOTALDBUSERS' => $totaldbusers,
	'STATISTICS_TOTALDBPAGES' => $totaldbpages,
	'STATISTICS_TOTALDBCOMMENTS' => $totaldbcomments,
	'STATISTICS_TOTALMAILSENT' => $totalmailsent,
	'STATISTICS_TOTALPMSENT' => $totalpmsent,
	'STATISTICS_TOTALPMACTIVE' => $totalpmactive,
	'STATISTICS_TOTALPMARCHIVED' => $totalpmarchived,
	'STATISTICS_TOTALDBVIEWS' => $totaldbviews,
	'STATISTICS_TOTALDBPOSTS_AND_TOTALDBPOSTSPRUNED' => ($totaldbposts+$totaldbpostspruned),
	'STATISTICS_TOTALDBPOSTS' => $totaldbposts,
	'STATISTICS_TOTALDBPOSTSPRUNED' => $totaldbpostspruned,
	'STATISTICS_TOTALDBTOPICS_AND_TOTALDBTOPICSPRUNED' => ($totaldbtopics+$totaldbtopicspruned),
	'STATISTICS_TOTALDBTOPICS' => $totaldbtopics,
	'STATISTICS_TOTALDBTOPICSPRUNED' => $totaldbtopicspruned,
	'STATISTICS_TOTALDBRATINGS' => $totaldbratings,
	'STATISTICS_TOTALDBRATINGSVOTES' => $totaldbratingsvotes,
	'STATISTICS_TOTALDBPOLLS' => $totaldbpolls,
	'STATISTICS_TOTALDBPOLLSVOTES' => $totaldbpollsvotes,
	'STATISTICS_TOTALDBFILES' => $totaldbfiles,
	'STATISTICS_TOTALDBFILESIZE' => floor($totaldbfilesize/1024),
	'STATISTICS_UNKNOWN_COUNT' => $totalusers - $ii,
	'STATISTICS_TOTALUSERS' => $row['stat_value']
));

?>