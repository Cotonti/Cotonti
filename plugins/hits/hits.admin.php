<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */
/**
 * Administration panel - Hits
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', 'hits');
cot_block($usr['auth_read']);

require_once cot_langfile('hits', 'plug');
require_once cot_incfile('hits', 'plug');
$tt = new XTemplate(cot_tplfile('hits.admin', 'plug', true));

$adminhelp = $L['adm_help_hits'];

$f = cot_import('f', 'G', 'TXT');
$v = cot_import('v', 'G', 'TXT');

/* === Hook === */
foreach (cot_getextplugins('hits.admin.first') as $pl)
{
	include $pl;
}
/* ===== */

if($f == 'year' || $f == 'month')
{
    $adminpath[] = array(cot_url('admin', 'm=other&p=hits&f='.$f.'&v='.$v), '('.$v.')');
    $sql = $db->query("SELECT * FROM $db_stats WHERE stat_name LIKE '".$db->prep($v)."%' ORDER BY stat_name DESC");

    while($row = $sql->fetch())
    {
        $y = mb_substr($row['stat_name'], 0, 4);
        $m = mb_substr($row['stat_name'], 5, 2);
        $d = mb_substr($row['stat_name'], 8, 2);
        $dat = cot_date('date_full', mktime(0, 0, 0, $m, $d, $y));
        $hits_d[$dat] = $row['stat_value'];
    }
	$sql->closeCursor();

    $hits_d_max = max($hits_d);
    $ii = 0;
    /* === Hook - Part1 : Set === */
    $extp = cot_getextplugins('hits.admin.loop');
    /* ===== */
    foreach($hits_d as $day => $hits)
    {
        $percentbar = floor(($hits / $hits_d_max) * 100);
        $tt->assign(array(
            'ADMIN_HITS_ROW_DAY' => $day,
            'ADMIN_HITS_ROW_HITS' => $hits,
            'ADMIN_HITS_ROW_PERCENTBAR' => $percentbar,
            'ADMIN_HITS_ROW_ODDEVEN' => cot_build_oddeven($ii)
            ));

        /* === Hook - Part2 : Include === */
        foreach ($extp as $pl)
        {
        	include $pl;
        }
        /* ===== */

        $tt->parse('MAIN.YEAR_OR_MONTH.ROW');
        $ii++;
    }

    $tt->parse('MAIN.YEAR_OR_MONTH');
}
else
{
	$sqlmax = $db->query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_value DESC LIMIT 1");
	if ($sqlmax->rowCount() > 0)
	{
		$rowmax = $sqlmax->fetch();
		$sqlmax->closeCursor();
	}
    $sql = $db->query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name DESC");
    
	if ($sql->rowCount() > 0 && $rowmax)
	{
		$max_date = $rowmax['stat_name'];
		$max_hits = $rowmax['stat_value'];

		$ii = 0;
		$hits_m = array();
		$hits_w = array();

		while ($row = $sql->fetch())
		{
			$y = mb_substr($row['stat_name'], 0, 4);
			$m = mb_substr($row['stat_name'], 5, 2);
			$d = mb_substr($row['stat_name'], 8, 2);
			$w = cot_date('W', mktime(0, 0, 0, $m, $d, $y));
			$hits_w[$y . '-W' . $w] += $row['stat_value'];
			$hits_m[$y . '-' . $m] += $row['stat_value'];
			$hits_y[$y] += $row['stat_value'];
		}
		$sql->closeCursor();

		$hits_w_max = max($hits_w);
		$hits_m_max = max($hits_m);
		$hits_y_max = max($hits_y);
		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('hits.admin.loop');
		/* ===== */
		$ii = 0;
		foreach ($hits_y as $year => $hits)
		{
			$percentbar = floor(($hits / $hits_y_max) * 100);
			$tt->assign(array(
				'ADMIN_HITS_ROW_YEAR_URL' => cot_url('admin', 'm=other&p=hits&f=year&v=' . $year),
				'ADMIN_HITS_ROW_YEAR' => $year,
				'ADMIN_HITS_ROW_YEAR_HITS' => $hits,
				'ADMIN_HITS_ROW_YEAR_PERCENTBAR' => $percentbar
			));
			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl) {
				include $pl;
			}
			/* ===== */
			$tt->parse('MAIN.DEFAULT.ROW_YEAR');
			$ii++;
		}
		$ii = 0;
		foreach ($hits_m as $month => $hits)
		{
			$percentbar = floor(($hits / $hits_m_max) * 100);
			$tt->assign(array(
				'ADMIN_HITS_ROW_MONTH_URL' => cot_url('admin', 'm=other&p=hits&f=month&v=' . $month),
				'ADMIN_HITS_ROW_MONTH' => $month,
				'ADMIN_HITS_ROW_MONTH_HITS' => $hits,
				'ADMIN_HITS_ROW_MONTH_PERCENTBAR' => $percentbar
			));
			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl) {
				include $pl;
			}
			/* ===== */
			$tt->parse('MAIN.DEFAULT.ROW_MONTH');
			$ii++;
		}
		$ii = 0;
		foreach ($hits_w as $week => $hits)
		{
			$ex = explode('-W', $week);
			$percentbar = floor(($hits / $hits_w_max) * 100);
			$tt->assign(array(
				'ADMIN_HITS_ROW_WEEK' => $week,
				'ADMIN_HITS_ROW_WEEK_HITS' => $hits,
				'ADMIN_HITS_ROW_WEEK_PERCENTBAR' => $percentbar
			));
			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl) {
				include $pl;
			}
			/* ===== */
			$tt->parse('MAIN.DEFAULT.ROW_WEEK');
			$ii++;
		}

		$tt->assign(array(
			'ADMIN_HITS_MAXHITS' => sprintf($L['hits_maxhits'], $max_date, $max_hits)
		));
	}
    $tt->parse('MAIN.DEFAULT');
}

/* === Hook  === */
foreach (cot_getextplugins('hits.admin.tags') as $pl)
{
	include $pl;
}
/* ===== */

$tt->parse('MAIN');
$plugin_body = $tt->text('MAIN');

?>