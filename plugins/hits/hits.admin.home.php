<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.home
[END_COT_EXT]
==================== */

/**
 * Hits
 *
 * @package Hits
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('hits', 'plug');
require_once cot_incfile('hits', 'plug');

$tt = new XTemplate(cot_tplfile('hits.admin.home', 'plug'));
//Show hit stats
if (!$cfg['plugin']['hits']['disablehitstats'])
{
	$timeback_stats = 15;// 15 days

	$sql = $db->query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name DESC LIMIT ".$timeback_stats);
	while ($row = $sql->fetch())
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
			$tt->assign(array(
				'ADMIN_HOME_DAY' => $day,
				'ADMIN_HOME_HITS' => $hits,
				'ADMIN_HOME_PERCENTBAR' => $percentbar
			));
			$tt->parse('MAIN.STAT.ADMIN_HOME_ROW');
		}
	}
	$tt->assign('ADMIN_HOME_MORE_HITS_URL', cot_url('admin', 'm=hits'));
	$tt->parse('MAIN.STAT');
}

//Show activity stats
if (!$cfg['plugin']['hits']['disableactivitystats'] && cot_module_active('page'))
{
	$timeback = $sys['now_offset'] - (7 * 86400);// 7 days

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
	$tt->parse('MAIN.ACTIVITY');
}


$tt->parse('MAIN');

//Show db stats
if (!$cfg['plugin']['hits']['disabledbstats'])
{
	$sql = $db->query("SHOW TABLES");

	while ($row = $sql->fetch(PDO::FETCH_NUM))
	{
		$table_name = $row[0];
		$status = $db->query("SHOW TABLE STATUS LIKE '$table_name'");
		$status1 = $status->fetch();
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

	$sql = $db->query("SELECT DISTINCT(pl_code) FROM $db_plugins WHERE 1 GROUP BY pl_code");
	$totalplugins = $sql->rowCount();

	$sql = $db->query("SELECT COUNT(*) FROM $db_plugins");
	$totalhooks = $sql->fetchColumn();

	$t->assign(array(
		'ADMIN_HOME_DB_TOTAL_ROWS' => $total_rows,
		'ADMIN_HOME_DB_INDEXSIZE' => number_format(($total_index_length / 1024), 1, '.', ' '),
		'ADMIN_HOME_DB_DATASSIZE' => number_format(($total_data_length / 1024), 1, '.', ' '),
		'ADMIN_HOME_DB_TOTALSIZE' => number_format(($total_length / 1024), 1, '.', ' '),
		'ADMIN_HOME_TOTALPLUGINS' => $totalplugins,
		'ADMIN_HOME_TOTALHOOKS' => $totalhooks
	));
}

$t->assign('ADMIN_HOME_HITS', $tt->text('MAIN'));


?>