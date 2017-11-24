<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home.mainpanel
[END_COT_EXT]
==================== */

/**
 * Hits
 *
 * @package Hits
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('hits', 'plug');
require_once cot_incfile('hits', 'plug');

$timeback_interval = $cfg['plugin']['hits']['timeback'] ? $cfg['plugin']['hits']['timeback'] : 7;
$timeback_interval_str = cot_declension($timeback_interval, $Ls['Days']);

$tt = new XTemplate(cot_tplfile('hits.admin.home', 'plug', true));
//Show hit stats
// INFO: `disablehitstats` var not actually defined in setup file now, but may be used (had been set) by another extension
if (!$cfg['plugin']['hits']['disablehitstats'])
{
	$sql = $db->query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name DESC LIMIT ".$timeback_interval);
	while ($row = $sql->fetch())
	{
		$year = mb_substr($row['stat_name'], 0, 4);
		$mons = mb_substr($row['stat_name'], 5, 2);
		$day = mb_substr($row['stat_name'], 8, 2);
		$dat = @date('d D', mktime(0, 0, 0, $mons, $day, $year));
		$hits_d[$dat] = $row['stat_value'];
	}
	$sql->closeCursor();

	if (is_array($hits_d))
	{
		$hits_d_max = max($hits_d);

		foreach ($hits_d as $day => $hits)
		{
			$percentbar = floor(($hits / $hits_d_max) * 100);
			$tt->assign(array(
				'ADMIN_HOME_DAY' => $day,
				'ADMIN_HOME_HITS' => $hits,
				'ADMIN_HOME_PERCENTBAR' => $percentbar
			));
			$tt->parse('MAIN.STAT.ADMIN_HOME_ROW');
		}
	}
	$tt->assign('ADMIN_HOME_MORE_HITS_URL', cot_url('admin', 'm=other&p=hits'));
	$tt->assign('HITS_STAT_HEADER', cot_rc($L['hits_hits'], "days=$timeback_interval_str") );
	$tt->parse('MAIN.STAT');
}

//Show activity stats
if (!$cfg['plugin']['hits']['disableactivitystats'] && cot_module_active('page'))
{
	$timeback = $sys['now'] - ($timeback_interval * 86400);
	require_once cot_incfile('page', 'module');
	$sql = $db->query("SELECT COUNT(*) FROM $db_users WHERE user_regdate > $timeback");
	$newusers = $sql->fetchColumn();

	$sql = $db->query("SELECT COUNT(*) FROM $db_pages WHERE page_date > $timeback");
	$newpages = $sql->fetchColumn();

	if (cot_module_active('forums'))
	{
		require_once cot_incfile('forums', 'module');

		$sql = $db->query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_creationdate > $timeback");
		$newtopics = $sql->fetchColumn();

		$sql = $db->query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_updated > $timeback");
		$newposts = $sql->fetchColumn();
	}

	if (function_exists('cot_get_newcomments'))
	{
		$newcomments = cot_get_newcomments($timeback);
	}

	if (cot_module_active('pm'))
	{
	 require_once cot_incfile('pm', 'module');
		$sql = $db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_date > $timeback");
		$newpms = $sql->fetchColumn();
	}

	$tt->assign(array(
		'ADMIN_HOME_NEWUSERS_URL' => cot_url('users', 'f=all&s=regdate&w=desc'),
		'ADMIN_HOME_NEWUSERS' => $newusers,
		'ADMIN_HOME_NEWPAGES_URL' => cot_url('admin', 'm=page'),
		'ADMIN_HOME_NEWPAGES' => $newpages,
		'ADMIN_HOME_NEWTOPICS_URL' => cot_url('forums'),
		'ADMIN_HOME_NEWTOPICS' => $newtopics,
		'ADMIN_HOME_NEWPOSTS_URL' => cot_url('forums'),
		'ADMIN_HOME_NEWPOSTS' => $newposts,
		'ADMIN_HOME_NEWCOMMENTS_URL' => cot_url('admin', 'm=comments'),
		'ADMIN_HOME_NEWCOMMENTS' => $newcomments,
		'ADMIN_HOME_NEWPMS' => $newpms
	));
	$tt->assign('ACTIVITY_STAT_HEADER', cot_rc($L['hits_activity'], "days=$timeback_interval_str") );
	$tt->parse('MAIN.ACTIVITY');
}


$tt->parse('MAIN');

$line = $tt->text('MAIN');
