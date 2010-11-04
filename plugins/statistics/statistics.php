<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Displays statistics info
 *
 * @package statistics
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

// TODO show statistics for installed modules only

cot_require('forums');
cot_require('page');
cot_require('pfs');
cot_require('pm');
cot_require('polls');

$s = cot_import('s', 'G', 'TXT');

$plugin_title = $L['plu_title'];

$totaldbpages = $db->countRows($db_pages);
$totaldbratings = $db->countRows($db_ratings);
$totaldbratingsvotes = $db->countRows($db_rated);
$totaldbpolls = $db->countRows($db_polls);
$totaldbpollsvotes = $db->countRows($db_polls_voters);
$totaldbposts = $db->countRows($db_forum_posts);
$totaldbtopics = $db->countRows($db_forum_topics);
$totaldbfiles = $db->countRows($db_pfs);
$totaldbusers = $db->countRows($db_users);

$totalpages = cot_stat_get('totalpages');
$totalmailsent = cot_stat_get('totalmailsent');
$totalpmsent = cot_stat_get('totalpms');

$totaldbviews = $db->query("SELECT SUM(fs_viewcount) FROM $db_forum_stats")->fetchColumn();
$totaldbfilesize = $db->query("SELECT SUM(pfs_size) FROM $db_pfs")->fetchColumn();
$totalpmactive = $db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_tostate<2")->fetchColumn();
$totalpmarchived = $db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_tostate=2")->fetchColumn();


$sql = $db->query("SELECT stat_name FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name ASC LIMIT 1");
$row = $sql->fetch();
$since = $row['stat_name'];

$sql = $db->query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_value DESC LIMIT 1");
$row = $sql->fetch();
$max_date = $row['stat_name'];
$max_hits = $row['stat_value'];

if ($usr['id'] > 0)
{
	$sql = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_posterid='".$usr['id']."'");
	$user_postscount = $sql->fetchColumn();
	$sql = $db->query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_firstposterid='".$usr['id']."'");
	$user_topicscount = $sql->fetchColumn();

	$t->assign(array(
		'STATISTICS_USER_POSTSCOUNT' => $user_postscount,
		'STATISTICS_USER_TOPICSCOUNT' => $user_topicscount
	));

	/* === Hook === */
	foreach (cot_getextplugins('statistics.user') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.IS_USER');
}
else
{
	$t->parse('MAIN.IS_NOT_USER');
}

if ($s == 'usercount')
{
	$sql1 = $db->query("DROP TEMPORARY TABLE IF EXISTS tmp1");
	$sql = $db->query("CREATE TEMPORARY TABLE tmp1 SELECT user_country, COUNT(*) as usercount FROM $db_users GROUP BY user_country");
	$sql = $db->query("SELECT * FROM tmp1 WHERE 1 ORDER by usercount DESC");
	$sql1 = $db->query("DROP TEMPORARY TABLE IF EXISTS tmp1");
}
else
{
	$sql = $db->query("SELECT user_country, COUNT(*) as usercount FROM $db_users GROUP BY user_country ASC");
}

$sqltotal = $db->query("SELECT COUNT(*) FROM $db_users WHERE 1");
$totalusers = $sqltotal->fetchColumn();

$ii = 0;

while ($row = $sql->fetch())
{
	$country_code = $row['user_country'];

	if (!empty($country_code) && $country_code != '00')
	{
		$ii = $ii + $row['usercount'];
		$t->assign(array(
			'STATISTICS_COUNTRY_FLAG' => cot_build_flag($country_code),
			'STATISTICS_COUNTRY_COUNT' => $row['usercount'],
			'STATISTICS_COUNTRY_NAME' => cot_build_country($country_code)
		));
		$t->parse('MAIN.ROW_COUNTRY');
	}
}

$t->assign(array(
	'STATISTICS_PLU_URL' => cot_url('plug', 'e=statistics'),
	'STATISTICS_SORT_BY_USERCOUNT' => cot_url('plug', 'e=statistics&s=usercount'),
	'STATISTICS_MAX_DATE' => $max_date,
	'STATISTICS_MAX_HITS' => $max_hits,
	'STATISTICS_SINCE' => $since,
	'STATISTICS_TOTALPAGES' => $totalpages,
	'STATISTICS_TOTALDBUSERS' => $totaldbusers,
	'STATISTICS_TOTALDBPAGES' => $totaldbpages,
	'STATISTICS_TOTALMAILSENT' => $totalmailsent,
	'STATISTICS_TOTALPMSENT' => $totalpmsent,
	'STATISTICS_TOTALPMACTIVE' => $totalpmactive,
	'STATISTICS_TOTALPMARCHIVED' => $totalpmarchived,
	'STATISTICS_TOTALDBVIEWS' => $totaldbviews,
	'STATISTICS_TOTALDBPOSTS' => $totaldbposts,
	'STATISTICS_TOTALDBPOSTSPRUNED' => $totaldbpostspruned,
	'STATISTICS_TOTALDBTOPICS' => $totaldbtopics,
	'STATISTICS_TOTALDBTOPICSPRUNED' => $totaldbtopicspruned,
	'STATISTICS_TOTALDBRATINGS' => $totaldbratings,
	'STATISTICS_TOTALDBRATINGSVOTES' => $totaldbratingsvotes,
	'STATISTICS_TOTALDBPOLLS' => $totaldbpolls,
	'STATISTICS_TOTALDBPOLLSVOTES' => $totaldbpollsvotes,
	'STATISTICS_TOTALDBFILES' => $totaldbfiles,
	'STATISTICS_TOTALDBFILESIZE' => floor($totaldbfilesize / 1024),
	'STATISTICS_UNKNOWN_COUNT' => $totalusers - $ii,
	'STATISTICS_TOTALUSERS' => $totalusers
));

/* === Hook === */
foreach (cot_getextplugins('statistics.tags') as $pl)
{
	include $pl;
}
/* ===== */

?>