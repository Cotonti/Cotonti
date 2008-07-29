<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.statistics.hits.inc.php
Version=110
Updated=2006-jun-30
Type=Core.admin
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if ( !defined('SED_CODE') || !defined('SED_ADMIN') ) { die('Wrong URL.'); }

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['auth_read']);

$adminpath[] = array ("admin.php?m=other", $L['Other']);
$adminpath[] = array ("admin.php?m=hits", $L['Hits']);
$adminhelp = $L['adm_help_hits'];

$f = sed_import('f','G','TXT');
$v = sed_import('v','G','TXT');

if ($f=='year' || $f=='month')
	{
	$adminpath[] = array ("admin.php?m=hits&amp;f=".$f."&amp;v=".$v, "(".$v.")");
	$sql = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '$v%' ORDER BY stat_name DESC");
	$adminmain .= "<h4>".$v." :</h4>";
	$adminmain .= "<table class=\"cells\">";

	while ($row = sed_sql_fetcharray($sql))
		{
		$y = substr($row['stat_name'], 0, 4);
		$m = substr($row['stat_name'], 5, 2);
		$d = substr($row['stat_name'], 8, 2);
		$dat = @date('Y-m-d D', mktime(0,0,0,$m,$d,$y));
		$hits_d[$dat] = $row['stat_value'];
		}

	$hits_d_max = max($hits_d);

	foreach ($hits_d as $day => $hits)
		{
		$percentbar = floor(($hits / $hits_d_max) * 100);
		$adminmain .= "<tr><td style=\"width:128px; text-align:center; padding:1px;\">".$day."</td>";
		$adminmain .= "<td style=\"text-align:right; width:96px; padding:1px;\">".$hits." ".$L['Hits']."</td>";
		$adminmain .= "<td style=\"text-align:right; width:40px; padding:1px;\">$percentbar%</td><td>";
		$adminmain .= "<div style=\"width:320px;\"><div class=\"bar_back\">";
		$adminmain .= "<div class=\"bar_front\" style=\"width:".$percentbar."%;\"></div></div></div></td></tr>";
		}

	$adminmain .= "</table>";
	}
else
	{
	$sql = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_name DESC");
	$sqlmax = sed_sql_query("SELECT * FROM $db_stats WHERE stat_name LIKE '20%' ORDER BY stat_value DESC LIMIT 1");
	$rowmax = sed_sql_fetcharray($sqlmax);
	$max_date = $rowmax['stat_name'];
	$max_hits = $rowmax['stat_value'];

	$L['adm_maxhits'] = (empty($L['adm_maxhits'])) ? "Maximum hitcount was reached %1\$s, %2\$s pages displayed this day." : $L['adm_maxhits']; 

	$adminmain = sprintf($L['adm_maxhits'], $max_date, $max_hits);

	$ii = 0;
	$hits_m = array();
	$hits_w = array();

	while ($row = sed_sql_fetcharray($sql))
		{
		$y = substr($row['stat_name'], 0, 4);
		$m = substr($row['stat_name'], 5, 2);
		$d = substr($row['stat_name'], 8, 2);
		$w = @date('W', mktime(0,0,0,$m,$d,$y));
		$hits_w[$y."-W".$w] += $row['stat_value'];
		$hits_m[$y."-".$m] += $row['stat_value'];
		$hits_y[$y] += $row['stat_value'];
		}

	$hits_w_max = max($hits_w);
	$hits_m_max = max($hits_m);
	$hits_y_max = max($hits_y);

	$adminmain .= "<h4>".$L['adm_byyear']." :</h4>";
	$adminmain .= "<table class=\"cells\">";

	foreach ($hits_y as $year => $hits)
		{
		$percentbar = floor(($hits / $hits_y_max) * 100);
		$adminmain .= "<tr><td style=\"width:80px;text-align:center; padding:1px;\">";
		$adminmain .= "<a href=\"admin.php?m=hits&amp;f=year&amp;v=$year\">".$year."</a></td>";
		$adminmain .= "<td style=\"text-align:right; width:96px; padding:1px;\">".$hits." ".$L['Hits']."</td>";
		$adminmain .= "<td style=\"text-align:right; width:40px; padding:1px;\">$percentbar%</td><td>";
		$adminmain .= "<div style=\"width:320px;\"><div class=\"bar_back\">";
		$adminmain .= "<div class=\"bar_front\" style=\"width:".$percentbar."%;\"></div></div></div></td></tr>";
		}

	$adminmain .= "</table>";

	$adminmain .= "<h4>".$L['adm_bymonth']." :</h4>";
	$adminmain .= "<table class=\"cells\">";

	foreach ($hits_m as $month => $hits)
		{
		$percentbar = floor(($hits / $hits_m_max) * 100);
		$adminmain .= "<tr><td style=\"width:80px; text-align:center; padding:1px;\">";
		$adminmain .= "<a href=\"admin.php?m=hits&amp;f=month&amp;v=$month\">".$month."</a></td>";
		$adminmain .= "<td style=\"text-align:right; width:96px; padding:1px;\">".$hits." ".$L['Hits']."</td>";
		$adminmain .= "<td style=\"text-align:right; width:40px; padding:1px;\">$percentbar%</td>";
		$adminmain .= "<td style=\"padding:1px;\">";
		$adminmain .= "<div style=\"width:320px;\"><div class=\"bar_back\">";
		$adminmain .= "<div class=\"bar_front\" style=\"width:".$percentbar."%;\"></div></div></div></td></tr>";
		}

	$adminmain .= "</table>";

	$adminmain .= "<h4>".$L['adm_byweek']." :</h4>";
	$adminmain .= "<table class=\"cells\">";

	foreach ($hits_w as $week => $hits)
		{
		$ex = explode ("-W", $week);
		$percentbar = floor(($hits / $hits_w_max) * 100);
		$adminmain .= "<tr><td style=\"width:80px; text-align:center; padding:1px;\">".$week."</td>";
		$adminmain .= "<td style=\"text-align:right; width:96px; padding:1px;\">".$hits." ".$L['Hits']."</td>";
		$adminmain .= "<td style=\"text-align:right; width:40px; padding:1px;\">$percentbar%</td>";
		$adminmain .= "<td style=\"padding:1px;\">";
		$adminmain .= "<div style=\"width:320px;\"><div class=\"bar_back\">";
		$adminmain .= "<div class=\"bar_front\" style=\"width:".$percentbar."%;\"></div></div></div></td></tr>";
		}
	$adminmain .= "</table>";
	}

?>
