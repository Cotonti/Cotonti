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

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

//Version Checking
preg_match('/Rev: ([0-9]+)/', $cfg['svnrevision'], $revmatch);
$cfg['svnrevision'] = $revmatch[1];
unset($revmatch);
if ($cfg['svnrevision'] > $cfg['revision'])
{
	$cfg['revision'] = $cfg['svnrevision'];
	sed_sql_query("UPDATE ".$db_config." SET `config_value`= ".(int)$cfg['svnrevision']." WHERE `config_owner` = 'core' AND `config_cat` = 'version' AND `config_name` = 'revision' LIMIT 1");
}

$t = new XTemplate(sed_skinfile('admin.home'));

$adminpath[] = array(sed_url('admin', 'm=home'), $L['Home']);

$pagesqueued = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state='1'");
$pagesqueued = sed_sql_result($pagesqueued, 0, "COUNT(*)");

if (!function_exists('gd_info') && $cfg['th_amode'] != 'Disabled')
{
	$is_adminwarnings = true;
}

//Version Checking
if ($cfg['check_updates'])
{
	$update_info = sed_cache_get('update_info');
	if (!$update_info)
	{
		if (ini_get('allow_url_fopen'))
		{
			$update_info = @file_get_contents('http://www.cotonti.com/update-check');
			if ($update_info)
			{
				$update_info = json_decode($update_info, TRUE);
				sed_cache_store('update_info', $update_info, 86400, false);
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
				sed_cache_store('update_info', $update_info, 86400, false);
			}
			curl_close($curl);
		}
	}
	if ($update_info['update_rev'] > $cfg['revision'])
	{
		$t->assign(array(
			'ADMIN_HOME_UPDATE_REVISION' => sprintf($L['home_update_revision'], $cfg['version'], $cfg['revision'], htmlspecialchars($update_info['update_ver']), (int)$update_info['update_rev']),
			'ADMIN_HOME_UPDATE_MESSAGE' => sed_parse(htmlspecialchars($update_info['update_message']), $cfg['parsebbcodepages'], $cfg['parsesmiliespages'], true),
		));
		$t->parse('HOME.UPDATE');
	}
}

//Show hit stats
if (!$cfg['disablehitstats'])
{
	$timeback_stats = 15;// 15 days

	$sql = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name DESC LIMIT ".$timeback_stats);
	while ($row = sed_sql_fetcharray($sql))
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
			$t->parse('HOME.ADMIN_HOME_ROW');
		}
	}
	$t->assign('ADMIN_HOME_MORE_HITS_URL', sed_url('admin', 'm=hits'));
}

//Show activity stats
if (!$cfg['disableactivitystats'])
{
	$timeback = $sys['now_offset'] - (7 * 86400);// 7 days

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_regdate>'$timeback'");
	$newusers = sed_sql_result($sql, 0, "COUNT(*)");

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_date >'$timeback'");
	$newpages = sed_sql_result($sql, 0, "COUNT(*)");

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_topics WHERE ft_creationdate>'$timeback'");
	$newtopics = sed_sql_result($sql, 0, "COUNT(*)");

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_forum_posts WHERE fp_updated>'$timeback'");
	$newposts = sed_sql_result($sql, 0, "COUNT(*)");

	if (function_exists('sed_get_newcomments'))
	{
		$newcomments = sed_get_newcomments($timeback);
	}

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_pm WHERE pm_date>'$timeback'");
	$newpms = sed_sql_result($sql, 0, "COUNT(*)");

	$t->assign(array(
		'ADMIN_HOME_NEWUSERS_URL' => sed_url('users', 'f=all&s=regdate&w=desc'),
		'ADMIN_HOME_NEWUSERS' => $newusers,
		'ADMIN_HOME_NEWPAGES_URL' => sed_url('admin', 'm=page'),
		'ADMIN_HOME_NEWPAGES' => $newpages,
		'ADMIN_HOME_NEWTOPICS_URL' => sed_url('forums'),
		'ADMIN_HOME_NEWTOPICS' => $newtopics,
		'ADMIN_HOME_NEWPOSTS_URL' => sed_url('forums'),
		'ADMIN_HOME_NEWPOSTS' => $newposts,
		'ADMIN_HOME_NEWCOMMENTS_URL' => sed_url('admin', 'm=comments'),
		'ADMIN_HOME_NEWCOMMENTS' => $newcomments,
		'ADMIN_HOME_NEWPMS' => $newpms
	));
}

//Show db stats
if (!$cfg['disabledbstats'])
{
	$sql = sed_sql_query("SHOW TABLES");

	while ($row = sed_sql_fetchrow($sql))
	{
		$table_name = $row[0];
		$status = sed_sql_query("SHOW TABLE STATUS LIKE '$table_name'");
		$status1 = sed_sql_fetcharray($status);
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

	$sql = sed_sql_query("SELECT DISTINCT(pl_code) FROM $db_plugins WHERE 1 GROUP BY pl_code");
	$totalplugins = sed_sql_numrows($sql);

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_plugins");
	$totalhooks = sed_sql_result($sql, 0, "COUNT(*)");

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
	"ADMIN_HOME_URL" => sed_url('admin', "m=page"),
	"ADMIN_HOME_PAGESQUEUED" => $pagesqueued,
	'ADMIN_HOME_VERSION' => $cfg['version'],
	'ADMIN_HOME_REVISION' => $L['home_rev'].$cfg['revision'],
	'ADMIN_HOME_DB_VERSION' => $cfg['dbversion']
));
$t->parse('HOME');
if (SED_AJAX)
{
	$t->out('HOME');
}
else
{
	$adminmain = $t->text('HOME');
}

/* === Hook === */
$extp = sed_getextplugins('admin.home', 'R');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if ($cfg['trash_prunedelay'] > 0)
{
	$timeago = $sys['now_offset'] - ($cfg['trash_prunedelay'] * 86400);
	$sqltmp = sed_sql_query("DELETE FROM $db_trash WHERE tr_date<$timeago");
	$deleted = sed_sql_affectedrows();
	if ($deleted > 0)
	{
		sed_log($deleted.' old item(s) removed from the trashcan, older than '.$cfg['trash_prunedelay'].' days', 'adm');
	}
}

?>