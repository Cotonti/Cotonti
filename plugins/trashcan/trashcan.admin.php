<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Trashcan interface
 *
 * @package trashcan
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

cot_require('users');
$cfg['module']['page'] && cot_require('page');
$cfg['module']['forums'] && cot_require('forums');
$cfg['plugin']['comments'] && cot_require('comments', true);

cot_require('trashcan', true);
cot_require_lang('trashcan', 'plug');

$adminhelp = $L['adm_help_trashcan'];

$id = cot_import('id', 'G', 'INT');
$d = cot_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$info = ($a == 'info') ? 1 : 0;

/* === Hook === */
foreach (cot_getextplugins('trashcan.admin.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'wipe')
{
	cot_check_xg();
	/* === Hook === */
	foreach (cot_getextplugins('trashcan.admin.wipe') as $pl)
	{
		include $pl;
	}
	/* ===== */
	cot_trash_delete($id);
	cot_message('adm_trashcan_deleted');
	cot_redirect(cot_url('admin', 'm=other&p=trashcan', '', true));
}
elseif($a == 'wipeall')
{
	cot_check_xg();
	/* === Hook === */
	foreach (cot_getextplugins('trashcan.admin.wipeall') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$sql = $db->query("TRUNCATE $db_trash");

	cot_message('adm_trashcan_prune');
	cot_redirect(cot_url('admin', 'm=other&p=trashcan', '', true));
}
elseif($a == 'restore')
{
	cot_check_xg();
	/* === Hook === */
	foreach (cot_getextplugins('trashcan.admin.restore') as $pl)
	{
		include $pl;
	}
	/* ===== */
	cot_trash_restore($id);

	cot_message('adm_trashcan_restored');
	cot_redirect(cot_url('admin', 'm=other&p=trashcan', '', true));
}

$tr_t = new XTemplate(cot_skinfile(($info) ? 'trashcan.info.admin' : 'trashcan.admin', true));
$totalitems = $db->countRows($db_trash);
$pagenav = cot_pagenav('admin', 'm=trashcan', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql_query = ($info) ? "AND tr_id='$id' LIMIT 1" : "ORDER by tr_id DESC LIMIT $d, ".$cfg['maxrowsperpage'];
$sql = $db->query("SELECT t.*, u.user_name FROM $db_trash AS t
	LEFT JOIN $db_users AS u ON t.tr_trashedby=u.user_id
	WHERE tr_parentid='0' $sql_query");

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('trashcan.admin.loop');
/* ===== */
while($row = $sql->fetch())
{
	$ii++;
	switch($row['tr_type'])
	{
		case 'comment':
			$icon = $R['admin_icon_comments'];
			$typestr = $L['Comment'];
			$enabled = ($cfg['plugin']['comments']) ? 1 : 0;
			break;

		case 'forumpost':
			$icon = $R['admin_icon_forums_posts'];
			$typestr = $L['Post'];
			$enabled = ($cfg['module']['forums']) ? 1 : 0;
			break;

		case 'forumtopic':
			$icon = $R['admin_icon_forums_topics'];
			$typestr = $L['Topic'];
			$enabled = ($cfg['module']['forums']) ? 1 : 0;
			break;

		case 'page':
			$icon = $R['admin_icon_page'];
			$typestr = $L['Page'];
			$enabled = ($cfg['module']['page']) ? 1 : 0;
			break;

		case 'user':
			$icon = $R['admin_icon_user'];
			$typestr = $L['User'];
			$enabled = 1;
			break;

		default:
			$icon = $R['admin_icon_tools'];
			$typestr = $row['tr_type'];
			$enabled = 1;
			break;
	}

	$tr_t->assign(array(
		'ADMIN_TRASHCAN_DATE' => date($cfg['dateformat'], $row['tr_date'] + $usr['timezone'] * 3600),
		'ADMIN_TRASHCAN_TYPESTR_ICON' => $icon,
		'ADMIN_TRASHCAN_TYPESTR' => $typestr,
		'ADMIN_TRASHCAN_TITLE' => htmlspecialchars($row['tr_title']),
		'ADMIN_TRASHCAN_TRASHEDBY' => ($row['tr_trashedby'] == 0) ? $L['System'] : cot_build_user($row['tr_trashedby'], htmlspecialchars($row['user_name'])),
		'ADMIN_TRASHCAN_ROW_WIPE_URL' => cot_url('admin', 'm=other&p=trashcan&a=wipe&id='.$row['tr_id'].'&d='.$d.'&'.cot_xg()),
		'ADMIN_TRASHCAN_ROW_RESTORE_URL' => cot_url('admin', 'm=other&p=trashcan&a=restore&id='.$row['tr_id'].'&d='.$d.'&'.cot_xg()),
		'ADMIN_TRASHCAN_ROW_INFO_URL' => cot_url('admin', 'm=other&p=trashcan&a=info&id='.$row['tr_id']),
		'ADMIN_TRASHCAN_ROW_RESTORE_ENABLED' => $enabled,
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	if($info)
	{
		$adminpath[] = array(cot_url('admin', 'm=other&p=trashcan&a=info&id='.$id), $row['tr_title']);
		$data = unserialize($row['tr_datas']);
		{
			foreach($data as $key => $val)
			{
				$tr_t->assign(array(
					'ADMIN_TRASHCAN_INFO_ROW' => htmlspecialchars($key),
					'ADMIN_TRASHCAN_INFO_VALUE' => $val,
				));
				$tr_t->parse('MAIN.TRASHCAN_ROW.TRASHCAN_INFOROW');
			}
		}

	}
	$tr_t->parse('MAIN.TRASHCAN_ROW');
}
if($ii == 0)
{
	$tr_t->parse('MAIN.TRASHCAN_EMPTY');
}



$tr_t->assign(array(
	'ADMIN_TRASHCAN_CONF_URL' => cot_url('admin', 'm=config&n=edit&o=plug&p=trashcan'),
	'ADMIN_TRASHCAN_WIPEALL_URL' => cot_url('admin', 'm=other&p=trashcan&a=wipeall&'.cot_xg()),
	'ADMIN_TRASHCAN_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_TRASHCAN_PAGNAV' => $pagenav['main'],
	'ADMIN_TRASHCAN_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_TRASHCAN_TOTALITEMS' => $totalitems,
	'ADMIN_TRASHCAN_COUNTER_ROW' => $ii,
	'ADMIN_TRASHCAN_PAGESQUEUED' => $pagesqueued
));


cot_display_messages($tr_t);

/* === Hook  === */
foreach (cot_getextplugins('trashcan.admin.tags') as $pl)
{
	include $pl;
}
/* ===== */

$tr_t->parse('MAIN');

$plugin_body = $tr_t->text('MAIN');

?>
