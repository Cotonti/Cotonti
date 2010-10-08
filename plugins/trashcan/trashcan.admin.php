<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Trashcan interface
 *
 * @package trash
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');
cot_require('trashcan', true);
cot_require_lang('trashcan', 'plug');

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

$tr_t = new XTemplate(cot_skinfile('trashcan.admin'), true);

$adminpath[] = array(cot_url('admin', 'm=trashcan'), $L['Trashcan']);
$adminhelp = $L['adm_help_trashcan'];

$id = cot_import('id', 'G', 'INT');
$d = cot_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

/**
 * Restores a trash item
 *
 * @param int $id Trash item ID
 * @return bool Operation success or failure
 */
function cot_trash_restore($id)
{
	global $db_forum_topics, $db_forum_posts, $db_trash;

	$columns = array();
	$datas = array();

	$tsql = cot_db_query("SELECT * FROM $db_trash WHERE tr_id='$id' LIMIT 1");
	if ($res = cot_db_fetchassoc($tsql))
	{
		$res['tr_datas'] = unserialize($res['tr_datas']);
	}

	switch($res['tr_type'])
	{
		case 'comment':
			global $db_com;
			cot_db_insert($db_com, $res['tr_datas']);
			cot_log("Comment #".$res['tr_itemid']." restored from the trash can.", 'adm');
			return (TRUE);
			break;

		case 'forumpost':
			global $db_forum_posts;
			$sql = cot_db_query("SELECT ft_id FROM $db_forum_topics WHERE ft_id='".$res['tr_datas']['fp_topicid']."'");

			if ($row = cot_db_fetcharray($sql))
			{
				cot_db_insert($db_forum_posts, $res['tr_datas']);
				cot_log("Post #".$res['tr_itemid']." restored from the trash can.", 'adm');
				cot_forum_resynctopic($res['tr_datas']['fp_topicid']);
				cot_forum_sectionsetlast($res['tr_datas']['fp_sectionid']);
				cot_forum_resync($res['tr_datas']['fp_sectionid']);
				return TRUE;
			}
			else
			{
				$sql1 = cot_db_query("SELECT tr_id FROM $db_trash WHERE tr_type='forumtopic' AND tr_itemid='q".$res['tr_datas']['fp_topicid']."'");
				if ($row1 = cot_db_fetcharray($sql1))
				{
					cot_trash_restore($row1['tr_id']);
					cot_db_delete($db_trash, "tr_id='".$row1['tr_id']."'");
				}
			}
			break;

		case 'forumtopic':
			global $db_forum_topics;
			cot_db_insert($db_forum_topics, $res['tr_datas']);
			cot_log("Topic #".$res['tr_datas']['ft_id']." restored from the trash can.", 'adm');

			$sql = cot_db_query("SELECT tr_id FROM $db_trash WHERE tr_type='forumpost' AND tr_itemid LIKE '%-".$res['tr_itemid']."'");

			while ($row = cot_db_fetcharray($sql))
			{
				$tsql = cot_db_query("SELECT * FROM $db_trash WHERE tr_id='{$row['tr_id']}' LIMIT 1");
				if ($res2 = cot_db_fetchassoc($tsql))
				{
					$res2['tr_datas'] = unserialize($res2['tr_datas']);
				}
				cot_db_insert($db_forum_posts, $res2['tr_datas']);
				cot_db_delete($db_trash, "tr_id='".$row['tr_id']."'");
				cot_log("Post #".$res2['tr_datas']['fp_id']." restored from the trash can (belongs to topic #".$res2['tr_datas']['fp_topicid'].").", 'adm');
			}

			cot_forum_resynctopic($res['tr_itemid']);
			cot_forum_sectionsetlast($res['tr_datas']['ft_sectionid']);
			cot_forum_resync($res['tr_datas']['ft_sectionid']);
			return TRUE;
			break;

		case 'page':
			global $db_pages, $db_structure;
			cot_db_insert($db_pages, $res['tr_datas']);
			cot_log("Page #".$res['tr_itemid']." restored from the trash can.", 'adm');
			$sql = cot_db_query("SELECT page_cat FROM $db_pages WHERE page_id='".$res['tr_itemid']."'");
			$row = cot_db_fetcharray($sql);
			$sql = cot_db_query("SELECT structure_id FROM $db_structure WHERE structure_code='".$row['page_cat']."'");
			if (cot_db_numrows($sql) == 0)
			{
				$sql = cot_db_query("UPDATE $db_pages SET page_cat='restored' WHERE page_id='".$res['tr_itemid']."'");
			}
			return TRUE;
			break;

		case 'user':
			global $db_users;
			cot_db_insert($db_users, $res['tr_datas']);
			cot_log("User #".$res['tr_itemid']." restored from the trash can.", 'adm');
			return TRUE;
			break;

		default:
			cot_db_insert($res['tr_type'], $res['tr_datas']);
			cot_log("RES #".$res['tr_itemid']." restored from the trash can.", 'adm');
			return TRUE;
			break;
	}
}

/* === Hook === */
foreach (cot_getextplugins('trash.admin.first') as $pl)
{
	include $pl;
}
/* ===== */

if($a == 'wipe')
{
	cot_check_xg();
	/* === Hook === */
	foreach (cot_getextplugins('trash.admin.wipe') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$sql = cot_db_query("DELETE FROM $db_trash WHERE tr_id='$id'");

	cot_message('adm_trashcan_deleted');
}
elseif($a == 'wipeall')
{
	cot_check_xg();
	/* === Hook === */
	foreach (cot_getextplugins('trash.admin.wipeall') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$sql = cot_db_query("TRUNCATE $db_trash");

	cot_message('adm_trashcan_prune');
}
elseif($a == 'restore')
{
	cot_check_xg();
	/* === Hook === */
	foreach (cot_getextplugins('trash.admin.restore') as $pl)
	{
		include $pl;
	}
	/* ===== */
	if(cot_trash_restore($id))
	{
		cot_db_delete($db_trash, "tr_id='$id'");
	}

	cot_message('adm_trashcan_restored');
}

$totalitems = cot_db_rowcount($db_trash);
$pagenav = cot_pagenav('admin', 'm=trashcan', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql = cot_db_query("SELECT t.*, u.user_name FROM $db_trash AS t
	LEFT JOIN $db_users AS u ON t.tr_trashedby=u.user_id
	WHERE 1 ORDER by tr_id DESC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('trash.admin.loop');
/* ===== */
while($row = cot_db_fetcharray($sql))
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

		case 'user':
			$icon = $R['admin_icon_user'];
			$typestr = $L['User'];
			break;

		default:
			$icon = $R['admin_icon_tools'];
			$typestr = $row['tr_type'];
			break;
	}

	$tr_t->assign(array(
		'ADMIN_TRASHCAN_DATE' => date($cfg['dateformat'], $row['tr_date'] + $usr['timezone'] * 3600),
		'ADMIN_TRASHCAN_TYPESTR_ICON' => $icon,
		'ADMIN_TRASHCAN_TYPESTR' => $typestr,
		'ADMIN_TRASHCAN_TITLE' => htmlspecialchars($row['tr_title']),
		'ADMIN_TRASHCAN_TRASHEDBY' => ($row['tr_trashedby'] == 0) ? $L['System'] : cot_build_user($row['tr_trashedby'], htmlspecialchars($row['user_name'])),
		'ADMIN_TRASHCAN_ROW_WIPE_URL' => cot_url('admin', 'm=trashcan&a=wipe&id='.$row['tr_id'].'&d='.$d.'&'.cot_xg()),
		'ADMIN_TRASHCAN_ROW_RESTORE_URL' => cot_url('admin', 'm=trashcan&a=restore&id='.$row['tr_id'].'&d='.$d.'&'.cot_xg())
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$tr_t->parse('MAIN.TRASHCAN_ROW');
	$ii++;
}

$tr_t->assign(array(
	'ADMIN_TRASHCAN_CONF_URL' => cot_url('admin', 'm=config&n=edit&o=core&p=trash'),
	'ADMIN_TRASHCAN_WIPEALL_URL' => cot_url('admin', 'm=trashcan&a=wipeall&'.cot_xg()),
	'ADMIN_TRASHCAN_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_TRASHCAN_PAGNAV' => $pagenav['main'],
	'ADMIN_TRASHCAN_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_TRASHCAN_TOTALITEMS' => $totalitems,
	'ADMIN_TRASHCAN_COUNTER_ROW' => $ii,
	'ADMIN_TRASHCAN_PAGESQUEUED' => $pagesqueued
));


cot_display_messages($tr_t);

/* === Hook  === */
foreach (cot_getextplugins('trash.admin.tags') as $pl)
{
	include $pl;
}
/* ===== */

$tr_t->parse('MAIN');

$plugin_body = $tr_t->text('MAIN');

?>
