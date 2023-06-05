<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=standalone
  [END_COT_EXT]
  ==================== */

/**
 * Displays statistics info
 *
 * @package Statistics
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') && defined('COT_PLUG') or die('Wrong URL');

require_once cot_incfile('hits', 'plug');

$s = cot_import('s', 'G', 'TXT');

Cot::$out['subtitle'] = Cot::$L['Statistics'];

$totaldbusers = Cot::$db->countRows(Cot::$db->users);
$totalmailsent = cot_stat_get('totalmailsent');

$sql = Cot::$db->query("SELECT stat_name FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name ASC LIMIT 1");
$row = $sql->fetch();
$since = $row['stat_name'];

$sql = Cot::$db->query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_value DESC LIMIT 1");
$row = $sql->fetch();
$max_date = $row['stat_name'];
$max_hits = $row['stat_value'];

if ($s == 'usercount') {
	$sql1 = Cot::$db->query("DROP TEMPORARY TABLE IF EXISTS tmp1");
	$sql = Cot::$db->query("CREATE TEMPORARY TABLE tmp1 SELECT user_country, COUNT(*) as usercount FROM $db_users GROUP BY user_country");
	$sql = Cot::$db->query("SELECT * FROM tmp1 WHERE 1 ORDER by usercount DESC");
	$sql1 = Cot::$db->query("DROP TEMPORARY TABLE IF EXISTS tmp1");
} else {
    $sql = Cot::$db->query("SELECT user_country, COUNT(*) as usercount FROM $db_users GROUP BY user_country ORDER BY user_country ASC");
}

$sqltotal = Cot::$db->query("SELECT COUNT(*) FROM $db_users WHERE 1");
$totalusers = $sqltotal->fetchColumn();

$ii = 0;

while ($row = $sql->fetch()) {
	$country_code = $row['user_country'];

	if (!empty($country_code) && $country_code != '00') {
		$ii = $ii + $row['usercount'];
		$t->assign(array(
			'STATISTICS_COUNTRY_FLAG' => cot_build_flag($country_code),
			'STATISTICS_COUNTRY_COUNT' => $row['usercount'],
			'STATISTICS_COUNTRY_NAME' => cot_build_country($country_code)
		));
		$t->parse('MAIN.ROW_COUNTRY');
	}
}
$sql->closeCursor();

$totaldbviews = 0;
if (cot_module_active('forums')) {
	require_once cot_incfile('forums', 'module');
	$totaldbviews = Cot::$db->query("SELECT SUM(fs_viewcount) FROM $db_forum_stats")->fetchColumn();
	$totaldbposts = Cot::$db->countRows($db_forum_posts);
	$totaldbtopics = Cot::$db->countRows($db_forum_topics);
	if (Cot::$usr['id'] > 0)
	{
		$sql = Cot::$db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_posterid='" . Cot::$usr['id'] . "'");
		$user_postscount = $sql->fetchColumn();
		$sql = Cot::$db->query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_firstposterid='" . Cot::$usr['id'] . "'");
		$user_topicscount = $sql->fetchColumn();

		$t->assign(array(
			'STATISTICS_USER_POSTSCOUNT' => $user_postscount,
			'STATISTICS_USER_TOPICSCOUNT' => $user_topicscount
		));
	}
	$t->assign(array(
		'STATISTICS_TOTALDBPOSTS' => $totaldbposts,
		'STATISTICS_TOTALDBTOPICS' => $totaldbtopics
	));
}

if (cot_module_active('page')) {
	require_once cot_incfile('page', 'module');

	$totaldbpages = Cot::$db->countRows($db_pages);

    // Total pages displayed since site creation
	$totalpages = cot_stat_get('totalpages');
	$t->assign(array(
		'STATISTICS_TOTALDBPAGES' => $totaldbpages,
		'STATISTICS_TOTALPAGES' => $totalpages,
	));
}

if (cot_module_active('pfs')) {
	require_once cot_incfile('pfs', 'module');

	$totaldbfiles = Cot::$db->countRows($db_pfs);
	$totaldbfilesize = Cot::$db->query("SELECT SUM(pfs_size) FROM $db_pfs")->fetchColumn();
	$t->assign(array(
		'STATISTICS_TOTALDBFILES' => $totaldbfiles,
		'STATISTICS_TOTALDBFILESIZE' => floor($totaldbfilesize / 1024),
	));
}

if (cot_module_active('pm')) {
	require_once cot_incfile('pm', 'module');

	$totalpmsent = cot_stat_get('totalpms');
	$totalpmactive = Cot::$db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_tostate<2")->fetchColumn();
	$totalpmarchived = Cot::$db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_tostate=2")->fetchColumn();
	$t->assign(array(
		'STATISTICS_TOTALPMSENT' => $totalpmsent,
		'STATISTICS_TOTALPMACTIVE' => $totalpmactive,
		'STATISTICS_TOTALPMARCHIVED' => $totalpmarchived,
	));
}

if (cot_module_active('polls')) {
	require_once cot_incfile('polls', 'module');

	$totaldbpolls = Cot::$db->countRows($db_polls);
	$totaldbpollsvotes = Cot::$db->countRows($db_polls_voters);
	$t->assign(array(
		'STATISTICS_TOTALDBPOLLS' => $totaldbpolls,
		'STATISTICS_TOTALDBPOLLSVOTES' => $totaldbpollsvotes,
	));
}

if (cot_plugin_active('ratings')) {
	require_once cot_incfile('ratings', 'plug');

	$totaldbratings = Cot::$db->countRows($db_ratings);
	$totaldbratingsvotes = Cot::$db->countRows($db_rated);
	$t->assign(array(
		'STATISTICS_TOTALDBRATINGS' => $totaldbratings,
		'STATISTICS_TOTALDBRATINGSVOTES' => $totaldbratingsvotes,
	));
}

$t->assign(array(
	'STATISTICS_PLU_URL' => cot_url('plug', 'e=statistics'),
	'STATISTICS_SORT_BY_USERCOUNT' => cot_url('plug', 'e=statistics&s=usercount'),
	'STATISTICS_MAX_DATE' => $max_date,
	'STATISTICS_MAX_HITS' => $max_hits,
	'STATISTICS_SINCE' => $since,
	'STATISTICS_TOTALDBUSERS' => $totaldbusers,
	'STATISTICS_TOTALMAILSENT' => $totalmailsent,
	'STATISTICS_TOTALDBVIEWS' => $totaldbviews,
	'STATISTICS_UNKNOWN_COUNT' => $totalusers - $ii,
	'STATISTICS_TOTALUSERS' => $totalusers
));

if (Cot::$usr['id'] > 0) {
	/* === Hook === */
	foreach (cot_getextplugins('statistics.user') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.IS_USER');
} else {
	$t->parse('MAIN.IS_NOT_USER');
}
/* === Hook === */
foreach (cot_getextplugins('statistics.tags') as $pl)
{
	include $pl;
}
/* ===== */
Cot::$L['plu_title'] = Cot::$L['Statistics'];