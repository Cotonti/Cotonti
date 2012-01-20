<?php
/**
 * Administration panel - PFS manager
 *
 * @package pfs
 * @version 0.1.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$t = new XTemplate(cot_tplfile('pfs.admin.allpfs'));

$adminpath[] = array(cot_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(cot_url('admin', 'm=pfs'), $L['PFS']);
$adminpath[] = array(cot_url('admin', 'm=pfs&s=allpfs'), $L['adm_allpfs']);
$adminhelp = $L['adm_help_allpfs'];

list($pg, $d, $durl) = cot_import_pagenav('d', $cfg['maxrowsperpage']);

/* === Hook === */
foreach (cot_getextplugins('admin.pfs.allpfs.first') as $pl)
{
	include $pl;
}
/* ===== */

unset($disp_list);

$totalitems = $db->query("SELECT COUNT(DISTINCT pfs_userid) FROM $db_pfs WHERE pfs_folderid>=0")->fetchColumn();
$pagenav = cot_pagenav('admin', 'm=pfs&s=allpfs', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql_pfs = $db->query("SELECT DISTINCT p.pfs_userid, u.user_name, u.user_id, COUNT(*) FROM $db_pfs AS p
	LEFT JOIN $db_users AS u ON p.pfs_userid=u.user_id
	WHERE pfs_folderid>=0 GROUP BY p.pfs_userid ORDER BY u.user_name ASC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.pfs.allpfs.loop');
/* ===== */
foreach ($sql_pfs->fetchAll() as $row)
{
	$row['user_name'] = ($row['user_id'] == 0) ? $L['SFS'] : $row['user_name'];
	$row['user_id'] = ($row['user_id'] == 0) ? '0' : $row['user_id'];

	$t->assign(array(
		'ADMIN_ALLPFS_ROW_URL' => cot_url('pfs', 'userid='.$row['user_id']),
		'ADMIN_ALLPFS_ROW_USER' => cot_build_user($row['user_id'], htmlspecialchars($row['user_name'])),
		'ADMIN_ALLPFS_ROW_COUNT' => $row['COUNT(*)']
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.ALLPFS_ROW');
	$ii++;
}

$t->assign(array(
	'ADMIN_ALLPFS_PAGINATION_PREV' => $pagenav['prev'],
	'ADMIN_ALLPFS_PAGNAV' => $pagenav['main'],
	'ADMIN_ALLPFS_PAGINATION_NEXT' => $pagenav['next'],
	'ADMIN_ALLPFS_TOTALITEMS' => $totalitems,
	'ADMIN_ALLPFS_ON_PAGE' => $ii
));

/* === Hook  === */
foreach (cot_getextplugins('admin.pfs.allpfs.tags') as $pl)
{
	include $pl;
}
/* ===== */

?>