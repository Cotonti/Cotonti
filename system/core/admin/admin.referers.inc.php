<?php
/**
 * Administration panel - Referers manager
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

$t = new XTemplate(sed_skinfile('admin.referers.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=referers'), $L['Referers']);
$adminhelp = $L['adm_help_referers'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

/* === Hook  === */
$extp = sed_getextplugins('admin.referers.first');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

if($a == 'prune' && $usr['isadmin'])
{
	$sql = sed_sql_query("TRUNCATE $db_referers");

	$adminwarnings = ($sql) ? $L['adm_ref_prune'] : $L['Error'];

}
elseif($a == 'prunelowhits' && $usr['isadmin'])
{
	$sql = sed_sql_query("DELETE FROM $db_referers WHERE ref_count<6");

	$adminwarnings = ($sql) ? $L['adm_ref_prunelowhits'] : $L['Error'];
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_rowcount($db_referers);
if($cfg['jquery'] AND $cfg['turnajax'])
{
	$pagnav = sed_pagination(sed_url('admin','m=referers'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'ajaxSend', "url: '".sed_url('admin','m=referers&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=referers'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'ajaxSend', "url: '".sed_url('admin','m=referers&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
}
else
{
	$pagnav = sed_pagination(sed_url('admin','m=referers'), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=referers'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
}

$sql = sed_sql_query("SELECT * FROM $db_referers ORDER BY ref_count DESC LIMIT $d, ".$cfg['maxrowsperpage']);

if(sed_sql_numrows($sql) > 0)
{
	while($row = mysql_fetch_array($sql))
	{
		preg_match("#//([^/]+)/#", $row['ref_url'], $a);
		$host = preg_replace('#^www.#i', '', $a[1]);
		$referers[$host][$row['ref_url']] = $row['ref_count'];
	}

	$ii = 0;
	/* === Hook - Part1 : Set === */
	$extp = sed_getextplugins('admin.referers.loop');
	/* ===== */
	foreach($referers as $referer => $url)
	{

		$t->assign(array("ADMIN_REFERERS_REFERER" => htmlspecialchars($referer)));
		
		foreach($url as $uri => $count)
		{
			$t -> assign(array(
				"ADMIN_REFERERS_URI" => htmlspecialchars(sed_cutstring($uri, 128)),
				"ADMIN_REFERERS_COUNT" => $count,
				"ADMIN_REFERERS_ODDEVEN" => sed_build_oddeven($ii)
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
			$t -> parse("REFERERS.REFERERS_ROW.REFERERS_URI");
		}
		$t->parse("REFERERS.REFERERS_ROW");
		$ii++;
	}
	$is_ref_empty = true;
}
else
{
	$is_ref_empty = false;
}

$t -> assign(array(
	"ADMIN_REFERERS_URL_PRUNE" => sed_url('admin', "m=referers&a=prune&".sed_xg()),
	"ADMIN_REFERERS_URL_PRUNELOWHITS" => sed_url('admin', "m=referers&a=prunelowhits&".sed_xg()),
	"ADMIN_REFERERS_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_REFERERS_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_REFERERS_PAGNAV" => $pagnav,
	"ADMIN_REFERERS_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_REFERERS_TOTALITEMS" => $totalitems,
	"ADMIN_REFERERS_ON_PAGE" => $ii
));

/* === Hook  === */
$extp = sed_getextplugins('admin.referers.tags');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

$t -> parse("REFERERS");
$adminmain = $t -> text("REFERERS");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>