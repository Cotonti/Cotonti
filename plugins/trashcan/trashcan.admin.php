<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=tools
[END_COT_EXT]
==================== */

/**
 * Trashcan interface
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('plug', 'trashcan');
cot_block(Cot::$usr['isadmin']);

require_once cot_incfile('users', 'module');
if (cot_module_active('page')) {
    require_once cot_incfile('page', 'module');
}
if (cot_module_active('forums')) {
    require_once cot_incfile('forums', 'module');
}
if (cot_plugin_active('comments')) {
    require_once cot_incfile('comments', 'plug');
}

require_once cot_incfile('trashcan', 'plug');
require_once cot_langfile('trashcan', 'plug');

$adminhelp = Cot::$L['adm_help_trashcan'];
$adminTitle = Cot::$L['Trashcan'];

$id = cot_import('id', 'G', 'INT');
$maxperpage = (Cot::$cfg['maxrowsperpage'] && is_numeric(Cot::$cfg['maxrowsperpage']) && Cot::$cfg['maxrowsperpage'] > 0) ?
    Cot::$cfg['maxrowsperpage'] : 15;
list($pg, $d, $durl) = cot_import_pagenav('d', $maxperpage);
$info = ($a == 'info') ? 1 : 0;

/* === Hook === */
foreach (cot_getextplugins('trashcan.admin.first') as $pl) {
	include $pl;
}
/* ===== */

if ($a == 'wipe') {
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('trashcan.admin.wipe') as $pl) {
		include $pl;
	}
	/* ===== */

	cot_trash_delete($id);
	cot_message('adm_trashcan_deleted');
	cot_redirect(cot_url('admin', 'm=other&p=trashcan', '', true));

} elseif ($a == 'wipeall') {
	cot_check_xg();

	/* === Hook === */
	foreach (cot_getextplugins('trashcan.admin.wipeall') as $pl) {
		include $pl;
	}
	/* ===== */

    $sqlToPrune = Cot::$db->query('SELECT tr_id FROM ' . Cot::$db->quoteTableName(Cot::$db->trash));
    $pruned = 0;
    while ($itemToPrune = $sqlToPrune->fetchColumn()) {
        $pruned++;
        cot_trash_delete($itemToPrune);
    }
    $sqlToPrune->closeCursor();

    if ($pruned > 0) {
        cot_message('adm_trashcan_prune');
    }
	cot_redirect(cot_url('admin', 'm=other&p=trashcan', '', true));

} elseif ($a == 'restore') {
	cot_check_xg();
	/* === Hook === */
	foreach (cot_getextplugins('trashcan.admin.restore') as $pl) {
		include $pl;
	}
	/* ===== */
	cot_trash_restore($id);

	cot_message('adm_trashcan_restored');
	cot_redirect(cot_url('admin', 'm=other&p=trashcan', '', true));
}

$tr_t = new XTemplate(cot_tplfile(($info) ? 'trashcan.info.admin' : 'trashcan.admin', 'plug', true));

$totalitems = (int) Cot::$db->query("SELECT COUNT(*) FROM $db_trash WHERE tr_parentid=0")->fetchColumn();
$pagenav = cot_pagenav('admin', 'm=other&p=trashcan', $d, $totalitems, $maxperpage, 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql_query = ($info) ? "AND tr_id=$id LIMIT 1" : "ORDER by tr_id DESC LIMIT $d, ".$maxperpage;
$sql = $db->query("SELECT t.*, u.user_name FROM $db_trash AS t
	LEFT JOIN $db_users AS u ON t.tr_trashedby=u.user_id
	WHERE tr_parentid=0 $sql_query");

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('trashcan.admin.loop');
/* ===== */
foreach ($sql->fetchAll() as $row)
{
	$ii++;
	switch($row['tr_type'])
	{
		case 'comment':
			$icon = Cot::$R['admin_icon_comments'];
			$typestr = Cot::$L['comments_comment'];
			$enabled = cot_plugin_active('comments') ? 1 : 0;
			break;

		case 'forumpost':
			$icon = Cot::$R['admin_icon_forums_posts'];
			$typestr = Cot::$L['forums_post'];
			$enabled = cot_module_active('forums') ? 1 : 0;
			break;

		case 'forumtopic':
			$icon = Cot::$R['admin_icon_forums_topics'];
			$typestr = Cot::$L['Topic'];
			$enabled = cot_module_active('forums') ? 1 : 0;
			break;

		case 'page':
			$icon = Cot::$R['admin_icon_page'];
			$typestr = Cot::$L['Page'];
			$enabled = cot_module_active('page') ? 1 : 0;
			break;

		case 'user':
			$icon = Cot::$R['admin_icon_user'];
			$typestr = Cot::$L['User'];
			$enabled = 1;
			break;

		default:
			$icon = Cot::$R['admin_icon_tools'];
			$typestr = Cot::$row['tr_type'];
			$enabled = 1;
			break;
	}

    $trashcanWipeUrl = cot_url('admin', 'm=other&p=trashcan&a=wipe&id=' . $row['tr_id'] . '&d=' . $durl .
        '&' . cot_xg());
	$tr_t->assign(array(
		'ADMIN_TRASHCAN_DATE' => cot_date('datetime_medium', $row['tr_date']),
		'ADMIN_TRASHCAN_DATE_STAMP' => $row['tr_date'],
		'ADMIN_TRASHCAN_TYPESTR_ICON' => $icon,
		'ADMIN_TRASHCAN_TYPESTR' => $typestr,
		'ADMIN_TRASHCAN_TITLE' => htmlspecialchars($row['tr_title']),
		'ADMIN_TRASHCAN_TRASHEDBY' => ($row['tr_trashedby'] == 0) ?
            Cot::$L['System'] : cot_build_user($row['tr_trashedby'], $row['user_name']),
		'ADMIN_TRASHCAN_ROW_WIPE_URL' => cot_confirm_url($trashcanWipeUrl, 'admin'),
		'ADMIN_TRASHCAN_ROW_RESTORE_URL' => cot_url('admin', 'm=other&p=trashcan&a=restore&id=' .
            $row['tr_id'] . '&d=' . $durl . '&' . cot_xg()),
		'ADMIN_TRASHCAN_ROW_INFO_URL' => cot_url('admin', 'm=other&p=trashcan&a=info&id=' . $row['tr_id']),
		'ADMIN_TRASHCAN_ROW_RESTORE_ENABLED' => $enabled,
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl) {
		include $pl;
	}
	/* ===== */

	if ($info) {
		$adminpath[] = array(cot_url('admin', 'm=other&p=trashcan&a=info&id='.$id), $row['tr_title']);
		$data = unserialize($row['tr_datas']);
		if (!empty($data)) {
			foreach ($data as $key => $val) {
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

if ($ii == 0) {
	$tr_t->parse('MAIN.TRASHCAN_EMPTY');
}

$trashcanWipeAllUrl = cot_url('admin', 'm=other&p=trashcan&a=wipeall&' . cot_xg());
$tr_t->assign(array(
	'ADMIN_TRASHCAN_CONF_URL' => cot_url('admin', 'm=config&n=edit&o=plug&p=trashcan'),
	'ADMIN_TRASHCAN_WIPEALL_URL' => cot_confirm_url($trashcanWipeAllUrl, 'admin'),
	'ADMIN_TRASHCAN_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_TRASHCAN_PAGNAV' => $pagenav['main'],
	'ADMIN_TRASHCAN_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_TRASHCAN_TOTALITEMS' => $totalitems,
	'ADMIN_TRASHCAN_COUNTER_ROW' => $ii,
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
