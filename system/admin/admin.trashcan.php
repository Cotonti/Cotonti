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

$t = new XTemplate(sed_skinfile('admin.trashcan'));

$adminpath[] = array(sed_url('admin', 'm=trashcan'), $L['Trashcan']);
$adminhelp = $L['adm_help_trashcan'];

$id = sed_import('id', 'G', 'INT');
$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

/* === Hook === */
foreach (sed_getextplugins('admin.trashcan.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'wipe')
{
	sed_check_xg();
	/* === Hook === */
	foreach (sed_getextplugins('admin.trashcan.wipe') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$sql = sed_sql_query("DELETE FROM $db_trash WHERE tr_id='$id'");

	sed_message('adm_trashcan_deleted');
}
elseif($a == 'wipeall')
{
	sed_check_xg();
	/* === Hook === */
	foreach (sed_getextplugins('admin.trashcan.wipeall') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$sql = sed_sql_query("TRUNCATE $db_trash");

	sed_message('adm_trashcan_prune');
}
elseif($a == 'restore')
{
	sed_check_xg();
	/* === Hook === */
	foreach (sed_getextplugins('admin.trashcan.restore') as $pl)
	{
		include $pl;
	}
	/* ===== */
	if(sed_trash_restore($id))
	{
		sed_trash_delete($id);
	}

	sed_message('adm_trashcan_restored');
}

$totalitems = sed_sql_rowcount($db_trash);
$pagenav = sed_pagenav('admin', 'm=trashcan', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

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
			$icon = $R['admin_icon_comments'];
			$typestr = $L['Comment'];
		break;

		case 'forumpost':
			$icon = $R['admin_icon_forums_posts'];
			$typestr = $L['Post'];
		break;

		case 'forumtopic':
			$icon = $R['admin_icon_forums_topics'];
			$typestr = $L['Topic'];
		break;

		case 'page':
			$icon = $R['admin_icon_page'];
			$typestr = $L['Page'];
		break;

		case 'pm':
			$icon = $R['admin_icon_pm'];
			$typestr = $L['Private_Messages'];
		break;

		case 'user':
			$icon = $R['admin_icon_user'];
			$typestr = $L['User'];
		break;

		default:
			$icon = $R['admin_icon_tools'];
			$typestr = $row['tr_type'];
		break;
	}

	$t->assign(array(
		'ADMIN_TRASHCAN_DATE' => date($cfg['dateformat'], $row['tr_date'] + $usr['timezone'] * 3600),
		'ADMIN_TRASHCAN_TYPESTR_ICON' => $icon,
		'ADMIN_TRASHCAN_TYPESTR' => $typestr,
		'ADMIN_TRASHCAN_TITLE' => htmlspecialchars($row['tr_title']),
		'ADMIN_TRASHCAN_TRASHEDBY' => ($row['tr_trashedby'] == 0) ? $L['System'] : sed_build_user($row['tr_trashedby'], htmlspecialchars($row['user_name'])),
		'ADMIN_TRASHCAN_ROW_WIPE_URL' => sed_url('admin', 'm=trashcan&a=wipe&id='.$row['tr_id'].'&d='.$d.'&'.sed_xg()),
		'ADMIN_TRASHCAN_ROW_RESTORE_URL' => sed_url('admin', 'm=trashcan&a=restore&id='.$row['tr_id'].'&d='.$d.'&'.sed_xg())
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN.TRASHCAN_ROW');
	$ii++;
}

$t->assign(array(
	'ADMIN_TRASHCAN_CONF_URL' => sed_url('admin', 'm=config&n=edit&o=core&p=trash'),
	'ADMIN_TRASHCAN_WIPEALL_URL' => sed_url('admin', 'm=trashcan&a=wipeall&'.sed_xg()),
	'ADMIN_TRASHCAN_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_TRASHCAN_PAGNAV' => $pagenav['main'],
	'ADMIN_TRASHCAN_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_TRASHCAN_TOTALITEMS' => $totalitems,
	'ADMIN_TRASHCAN_COUNTER_ROW' => $ii,
	'ADMIN_TRASHCAN_PAGESQUEUED' => $pagesqueued
));


if (sed_check_messages())
{
	$t->assign('MESSAGE_TEXT', sed_implode_messages());
	$t->parse('MAIN.MESSAGE');
	sed_clear_messages();
}

/* === Hook  === */
foreach (sed_getextplugins('admin.trashcan.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
if (SED_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}

?>