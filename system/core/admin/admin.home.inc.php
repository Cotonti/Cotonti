<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.home.inc.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) { die('Wrong URL.'); }

$adminpath[] = array ('admin.php?m=home', $L['Home']);

$pagesqueued = sed_sql_query("SELECT COUNT(*) FROM $db_pages WHERE page_state='1'");
$pagesqueued = sed_sql_result($pagesqueued, 0, "COUNT(*)");

if (!function_exists('gd_info') && $cfg['th_amode']!='Disabled')
	{ $adminwarnings .= "<p>".$L['adm_nogd']."</p>"; }

if (!empty($adminwarnings))
	{ $adminmain .= $L['adm_warnings']." :".$adminwarnings; }

$adminmain .= "<h4>".$L['adm_valqueue']." :</h4><ul>";
$adminmain .= "<li><a href=\"admin.php?m=page&amp;s=queue\">".$L['Pages']." : ".$pagesqueued."</a></li>";
$adminmain .= "</ul>";

/* === Hook for the plugins === */
$extp = sed_getextplugins('admin.home', 'R');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }

if ($cfg['trash_prunedelay']>0)
	{
	$timeago = $sys['now_offset'] - ($cfg['trash_prunedelay'] * 86400);
	$sqltmp = sed_sql_query("DELETE FROM $db_trash WHERE tr_date<$timeago");
	$deleted = mysql_affected_rows();
	if ($deleted>0)
		{ sed_log($deleted.' old item(s) removed from the trashcan, older than '.$cfg['trash_prunedelay'].' days', 'adm'); }
	}

?>
