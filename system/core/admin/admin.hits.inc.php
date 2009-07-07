<?php
/**
 * Administration panel - Hits
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['auth_read']);

$t = new XTemplate(sed_skinfile('admin.hits.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=hits'), $L['Hits']);
$adminhelp = $L['adm_help_hits'];

$f = sed_import('f', 'G', 'TXT');
$v = sed_import('v', 'G', 'TXT');

/* === Hook === */
$extp = sed_getextplugins('admin.hits.first');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

if($f == 'year' || $f == 'month')
{
    $adminpath[] = array(sed_url('admin', 'm=hits&f='.$f.'&v='.$v), "(".$v.")");
    $sql = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '$v%' ORDER BY stat_name DESC");

    while($row = sed_sql_fetcharray($sql))
    {
        $y = mb_substr($row['stat_name'], 0, 4);
        $m = mb_substr($row['stat_name'], 5, 2);
        $d = mb_substr($row['stat_name'], 8, 2);
        $dat = @date($cfg['formatyearmonthday'], mktime(0, 0, 0, $m, $d, $y));
        $hits_d[$dat] = $row['stat_value'];
    }

    $hits_d_max = max($hits_d);
    $ii = 0;
    /* === Hook - Part1 : Set === */
    $extp = sed_getextplugins('admin.hits.loop');
    /* ===== */
    foreach($hits_d as $day => $hits)
    {
        $percentbar = floor(($hits / $hits_d_max) * 100);
        $t -> assign(array(
            "ADMIN_HITS_ROW_DAY" => $day,
            "ADMIN_HITS_ROW_HITS" => $hits,
            "ADMIN_HITS_ROW_PERCENTBAR" => $percentbar,
            "ADMIN_HITS_ROW_ODDEVEN" => sed_build_oddeven($ii)
            ));

        /* === Hook - Part2 : Include === */
        if(is_array($extp))
        {
        	foreach($extp as $k => $pl)
        	{
        		include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
        	}
        }
        /* ===== */

        $t -> parse("HITS.YEAR_OR_MONTH.ROW");
        $ii++;
    }

    $t -> parse("HITS.YEAR_OR_MONTH");
}
else
{
    $sql = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name DESC");
    $sqlmax = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_value DESC LIMIT 1");
    $rowmax = sed_sql_fetcharray($sqlmax);
    $max_date = $rowmax['stat_name'];
    $max_hits = $rowmax['stat_value'];

    $ii = 0;
    $hits_m = array();
    $hits_w = array();

    while($row = sed_sql_fetcharray($sql))
    {
        $y = mb_substr($row['stat_name'], 0, 4);
        $m = mb_substr($row['stat_name'], 5, 2);
        $d = mb_substr($row['stat_name'], 8, 2);
        $w = @date('W', mktime(0, 0, 0, $m, $d, $y));
        $hits_w[$y."-W".$w] += $row['stat_value'];
        $hits_m[$y."-".$m] += $row['stat_value'];
        $hits_y[$y] += $row['stat_value'];
    }

    $hits_w_max = max($hits_w);
    $hits_m_max = max($hits_m);
    $hits_y_max = max($hits_y);
    /* === Hook - Part1 : Set === */
    $extp = sed_getextplugins('admin.hits.loop');
    /* ===== */
    $ii=0;
    foreach($hits_y as $year => $hits)
    {
        $percentbar = floor(($hits / $hits_y_max) * 100);
        $t -> assign(array(
            "ADMIN_HITS_ROW_YEAR_URL" => sed_url('admin', 'm=hits&f=year&v='.$year),
            "ADMIN_HITS_ROW_YEAR" => $year,
            "ADMIN_HITS_ROW_YEAR_HITS" => $hits,
            "ADMIN_HITS_ROW_YEAR_PERCENTBAR" => $percentbar
            ));
        /* === Hook - Part2 : Include === */
        if(is_array($extp))
        {
        	foreach($extp as $k => $pl)
        	{
        		include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
        	}
        }
        /* ===== */
        $t -> parse("HITS.DEFAULT.ROW_YEAR");
        $ii++;
    }
    $ii=0;
    foreach($hits_m as $month => $hits)
    {
        $percentbar = floor(($hits / $hits_m_max) * 100);
        $t -> assign(array(
            "ADMIN_HITS_ROW_MONTH_URL" => sed_url('admin', 'm=hits&f=month&v='.$month),
            "ADMIN_HITS_ROW_MONTH" => $month,
            "ADMIN_HITS_ROW_MONTH_HITS" => $hits,
            "ADMIN_HITS_ROW_MONTH_PERCENTBAR" => $percentbar
            ));
        /* === Hook - Part2 : Include === */
        if(is_array($extp))
        {
        	foreach($extp as $k => $pl)
        	{
        		include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
        	}
        }
        /* ===== */
        $t -> parse("HITS.DEFAULT.ROW_MONTH");
        $ii++;
    }
    $ii=0;
    foreach($hits_w as $week => $hits)
    {
        $ex = explode("-W", $week);
        $percentbar = floor(($hits / $hits_w_max) * 100);
        $t -> assign(array(
            "ADMIN_HITS_ROW_WEEK" => $week,
            "ADMIN_HITS_ROW_WEEK_HITS" => $hits,
            "ADMIN_HITS_ROW_WEEK_PERCENTBAR" => $percentbar
            ));
        /* === Hook - Part2 : Include === */
        if(is_array($extp))
        {
        	foreach($extp as $k => $pl)
        	{
        		include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
        	}
        }
        /* ===== */
        $t -> parse("HITS.DEFAULT.ROW_WEEK");
        $ii++;
    }

    $t -> assign(array(
        "ADMIN_HITS_MAXHITS" => sprintf($L['adm_maxhits'], $max_date, $max_hits)
        ));
    $t -> parse("HITS.DEFAULT");
}

/* === Hook  === */
$extp = sed_getextplugins('admin.hits.tags');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

$t -> parse("HITS");
$adminmain = $t -> text("HITS");

?>