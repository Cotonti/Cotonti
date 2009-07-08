<?php
/**
 * Administration panel - Trash can
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.trashcan.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=trashcan'), $L['Trashcan']);
$adminhelp = $L['adm_help_trashcan'];

$id = sed_import('id', 'G', 'INT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

/* === Hook === */
$extp = sed_getextplugins('admin.trashcan.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if($a == 'wipe')
{
	sed_check_xg();
	/* === Hook === */
	$extp = sed_getextplugins('admin.trashcan.wipe');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */
	$sql = sed_sql_query("DELETE FROM $db_trash WHERE tr_id='$id'");
	$adminwarnings = $L['adm_trashcan_deleted'];
}
elseif($a == 'wipeall')
{
	sed_check_xg();
	/* === Hook === */
	$extp = sed_getextplugins('admin.trashcan.wipeall');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */
	$sql = sed_sql_query("TRUNCATE $db_trash");
	$adminwarnings = $L['adm_trashcan_prune'];
}
elseif($a == 'restore')
{
	sed_check_xg();
	/* === Hook === */
	$extp = sed_getextplugins('admin.trashcan.restore');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */
	if(sed_trash_restore($id))
	{
		sed_trash_delete($id);
	}
	$adminwarnings = $L['adm_trashcan_restored'];
}

$is_adminwarnings = isset($adminwarnings);

$totalitems = sed_sql_rowcount($db_trash);
if($cfg['jquery'])
{
	$pagnav = sed_pagination(sed_url('admin','m=trashcan'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'ajaxSend', "url: '".sed_url('admin','m=trashcan&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=trashcan'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'ajaxSend', "url: '".sed_url('admin','m=trashcan&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
}
else
{
	$pagnav = sed_pagination(sed_url('admin','m=trashcan'), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=trashcan'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
}

$sql = sed_sql_query("SELECT t.*, u.user_name FROM $db_trash AS t
	LEFT JOIN $db_users AS u ON t.tr_trashedby=u.user_id
	WHERE 1 ORDER by tr_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.trashcan.loop');
/* ===== */
while($row = sed_sql_fetcharray($sql))
{
	switch($row['tr_type'])
	{
		case 'comment':
		$icon = "comments.gif";
		$typestr = $L['Comment'];
		break;

		case 'forumpost':
		$icon = "forums.gif";
		$typestr = $L['Post'];
		break;

		case 'forumtopic':
		$icon = "forums.gif";
		$typestr = $L['Topic'];
		break;

		case 'page':
		$icon = "page.gif";
		$typestr = $L['Page'];
		break;

		case 'pm':
		$icon = "pm.gif";
		$typestr = $L['Private_Messages'];
		break;

		case 'user':
		$icon = "user.gif";
		$typestr = $L['User'];
		break;

		default:
		$icon = "tools.gif";
		$typestr = $row['tr_type'];
		break;
	}

	$t -> assign(array(
		"ADMIN_TRASHCAN_DATE" => date($cfg['dateformat'], $row['tr_date'] + $usr['timezone'] * 3600),
		"ADMIN_TRASHCAN_TYPESTR_ICON" => $icon,
		"ADMIN_TRASHCAN_TYPESTR" => $typestr,
		"ADMIN_TRASHCAN_TITLE" => sed_cc($row['tr_title']),
		"ADMIN_TRASHCAN_TRASHEDBY" => ($row['tr_trashedby'] == 0) ? $L['System'] : sed_build_user($row['tr_trashedby'], sed_cc($row['user_name'])),
		"ADMIN_TRASHCAN_ROW_WIPE_URL" => sed_url('admin', "m=trashcan&a=wipe&id=".$row['tr_id']."&d=".$d."&".sed_xg()),
		"ADMIN_TRASHCAN_ROW_RESTORE_URL" => sed_url('admin', "m=trashcan&a=restore&id=".$row['tr_id']."&d=".$d."&".sed_xg())
	));

	/* === Hook - Part2 : Include === */
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$t -> parse("TRASHCAN.TRASHCAN_ROW");
	$ii++;
}

$t -> assign(array(
	"ADMIN_TRASHCAN_AJAX_OPENDIVID" => 'pagtab',
	"ADMIN_TRASHCAN_CONF_URL" => sed_url('admin', "m=config&n=edit&o=core&p=trash"),
	"ADMIN_TRASHCAN_WIPEALL_URL" => sed_url('admin', "m=trashcan&a=wipeall&".sed_xg()),
	"ADMIN_TRASHCAN_ADMINWARNINGS" => $adminwarnings,
	"ADMIN_TRASHCAN_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_TRASHCAN_PAGNAV" => $pagnav,
	"ADMIN_TRASHCAN_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_TRASHCAN_TOTALITEMS" => $totalitems,
	"ADMIN_TRASHCAN_COUNTER_ROW" => $ii,
	"ADMIN_TRASHCAN_PAGESQUEUED" => $pagesqueued
));

/* === Hook  === */
$extp = sed_getextplugins('admin.trashcan.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t -> parse("TRASHCAN");
$adminmain = $t -> text("TRASHCAN");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>