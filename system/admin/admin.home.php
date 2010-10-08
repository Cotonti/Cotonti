<?php
/**
 * Administration panel - Home page for administrators
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

cot_require('page'); // FIXME hard dependency

//Version Checking
preg_match('/Rev: ([0-9]+)/', $cfg['svnrevision'], $revmatch);
$cfg['svnrevision'] = $revmatch[1];
unset($revmatch);
if ($cfg['svnrevision'] > $cfg['revision'])
{
	$cfg['revision'] = $cfg['svnrevision'];
	cot_db_query("UPDATE ".$db_config." SET `config_value`= ".(int)$cfg['svnrevision']." WHERE `config_owner` = 'core' AND `config_cat` = 'version' AND `config_name` = 'revision' LIMIT 1");
}

$t = new XTemplate(cot_skinfile('admin.home'));

$adminpath[] = array(cot_url('admin', 'm=home'), $L['Home']);

$pagesqueued = cot_db_query("SELECT COUNT(*) FROM $db_pages WHERE page_state='1'");
$pagesqueued = cot_db_result($pagesqueued, 0, "COUNT(*)");

if (!function_exists('gd_info') && $cfg['th_amode'] != 'Disabled')
{
	$is_adminwarnings = true;
}

//Version Checking
if ($cfg['check_updates'])
{
	$update_info = cot_cache_get('update_info');
	if (!$update_info)
	{
		if (ini_get('allow_url_fopen'))
		{
			$update_info = @file_get_contents('http://www.cotonti.com/update-check');
			if ($update_info)
			{
				$update_info = json_decode($update_info, TRUE);
				cot_cache_store('update_info', $update_info, 86400, false);
			}
		}
		elseif (function_exists('curl_init'))
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'http://www.cotonti.com/update-check');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			$update_info = curl_exec($curl);
			if ($update_info)
			{
				$update_info = json_decode($update_info, TRUE);
				cot_cache_store('update_info', $update_info, 86400, false);
			}
			curl_close($curl);
		}
	}
	if ($update_info['update_rev'] > $cfg['revision'])
	{
		$t->assign(array(
			'ADMIN_HOME_UPDATE_REVISION' => sprintf($L['home_update_revision'], $cfg['version'], $cfg['revision'], htmlspecialchars($update_info['update_ver']), (int)$update_info['update_rev']),
			'ADMIN_HOME_UPDATE_MESSAGE' => cot_parse($update_info['update_message']),
		));
		$t->parse('MAIN.UPDATE');
	}
}

//Show hit stats
if (!$cfg['disablehitstats'])
{
	$timeback_stats = 15;// 15 days

	$sql = cot_db_query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name DESC LIMIT ".$timeback_stats);
	while ($row = cot_db_fetcharray($sql))
	{
		$year = mb_substr($row['stat_name'], 0, 4);
		$mons = mb_substr($row['stat_name'], 5, 2);
		$day = mb_substr($row['stat_name'], 8, 2);
		$dat = @date('d D', mktime(0, 0, 0, $mons, $day, $year));
		$hits_d[$dat] = $row['stat_value'];
	}

	if (is_array($hits_d))
	{
		$hits_d_max = max($hits_d);

		foreach ($hits_d as $day => $hits)
		{
			$percentbar = floor(($hits / $hits_d_max) * 100);
			$t->assign(array(
				'ADMIN_HOME_DAY' => $day,
				'ADMIN_HOME_HITS' => $hits,
				'ADMIN_HOME_PERCENTBAR' => $percentbar
			));
			$t->parse('MAIN.ADMIN_HOME_ROW');
		}
	}
	$t->assign('ADMIN_HOME_MORE_HITS_URL', cot_url('admin', 'm=hits'));
}

//Show activity stats
if (!$cfg['disableactivitystats'])
{
	$timeback = $sys['now_offset'] - (7 * 86400);// 7 days

	$sql = cot_db_query("SELECT COUNT(*) FROM $db_users WHERE user_regdate>'$timeback'");
	$newusers = cot_db_result($sql, 0, "COUNT(*)");

	$sql = cot_db_query("SELECT COUNT(*) FROM $db_pages WHERE page_date >'$timeback'");
	$newpages = cot_db_result($sql, 0, "COUNT(*)");

	cot_require('forums');

	$sql = cot_db_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_creationdate>'$timeback'");
	$newtopics = cot_db_result($sql, 0, "COUNT(*)");

	$sql = cot_db_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_updated>'$timeback'");
	$newposts = cot_db_result($sql, 0, "COUNT(*)");

	if (function_exists('cot_get_newcomments'))
	{
		$newcomments = cot_get_newcomments($timeback);
	}

	if ($cfg['module']['pm'])
	{
	 cot_require('pm');
		$sql = cot_db_query("SELECT COUNT(*) FROM $db_pm WHERE pm_date>'$timeback'");
		$newpms = cot_db_result($sql, 0, "COUNT(*)");
	}

	$t->assign(array(
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
}

//Show db stats
if (!$cfg['disabledbstats'])
{
	$sql = cot_db_query("SHOW TABLES");

	while ($row = cot_db_fetchrow($sql))
	{
		$table_name = $row[0];
		$status = cot_db_query("SHOW TABLE STATUS LIKE '$table_name'");
		$status1 = cot_db_fetcharray($status);
		$tables[] = $status1;
	}

	while (list($i,$dat) = each($tables))
	{
		$table_length = $dat['Index_length'] + $dat['Data_length'];
		$total_length += $table_length;
		$total_rows += $dat['Rows'];
		$total_index_length += $dat['Index_length'];
		$total_data_length += $dat['Data_length'];
	}

	$sql = cot_db_query("SELECT DISTINCT(pl_code) FROM $db_plugins WHERE 1 GROUP BY pl_code");
	$totalplugins = cot_db_numrows($sql);

	$sql = cot_db_query("SELECT COUNT(*) FROM $db_plugins");
	$totalhooks = cot_db_result($sql, 0, "COUNT(*)");

	$t->assign(array(
		'ADMIN_HOME_DB_TOTAL_ROWS' => $total_rows,
		'ADMIN_HOME_DB_INDEXSIZE' => number_format(($total_index_length / 1024), 1, '.', ' '),
		'ADMIN_HOME_DB_DATASSIZE' => number_format(($total_data_length / 1024), 1, '.', ' '),
		'ADMIN_HOME_DB_TOTALSIZE' => number_format(($total_length / 1024), 1, '.', ' '),
		'ADMIN_HOME_TOTALPLUGINS' => $totalplugins,
		'ADMIN_HOME_TOTALHOOKS' => $totalhooks
	));
}

$t->assign(array(
	'ADMIN_HOME_URL' => cot_url('admin', 'm=page'),
	'ADMIN_HOME_PAGESQUEUED' => $pagesqueued,
	'ADMIN_HOME_VERSION' => $cfg['version'],
	'ADMIN_HOME_REVISION' => $L['home_rev'].$cfg['revision'],
	'ADMIN_HOME_DB_VERSION' => $cfg['dbversion']
));
$t->parse('MAIN');
if (COT_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

/* === Hook === */
foreach (cot_getextplugins('admin.home', 'R') as $pl)
{
	include $pl;
}
/* ===== */

?>