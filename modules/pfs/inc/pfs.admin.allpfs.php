<?php
/**
 * Administration panel - PFS manager
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$t = new XTemplate(cot_tplfile('pfs.admin.allpfs'));

$adminPath[] = [cot_url('admin', 'm=other'), $L['Other']];
$adminPath[] = [cot_url('admin', 'm=pfs'), $L['PFS']];
$adminPath[] = [cot_url('admin', 'm=pfs&s=allpfs'), $L['adm_allpfs']];
$adminHelp = $L['adm_help_allpfs'];
$adminTitle = $L['adm_allpfs'];

list($pg, $d, $durl) = cot_import_pagenav('d', Cot::$cfg['maxrowsperpage']);

/* === Hook === */
foreach (cot_getextplugins('admin.pfs.allpfs.first') as $pl) {
	include $pl;
}
/* ===== */

unset($disp_list);

$totalitems = Cot::$db->query("SELECT COUNT(DISTINCT pfs_userid) FROM $db_pfs WHERE pfs_folderid >= 0")->fetchColumn();
$pagenav = cot_pagenav(
	'admin',
	'm=pfs&s=allpfs',
	$d,
	$totalitems,
	Cot::$cfg['maxrowsperpage'],
	'd',
	'',
	Cot::$cfg['jquery'] && Cot::$cfg['turnajax']
);

$sql_pfs = Cot::$db->query("SELECT DISTINCT p.pfs_userid, u.user_name, u.user_id, COUNT(*) FROM $db_pfs AS p
	LEFT JOIN $db_users AS u ON p.pfs_userid = u.user_id
	WHERE pfs_folderid >= 0 GROUP BY p.pfs_userid ORDER BY u.user_name ASC LIMIT $d, ".Cot::$cfg['maxrowsperpage']);

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('admin.pfs.allpfs.loop');
/* ===== */
foreach ($sql_pfs->fetchAll() as $row) {
	$row['user_name'] = ($row['user_id'] == 0) ? $L['SFS'] : $row['user_name'];
	$row['user_id'] = ($row['user_id'] == 0) ? '0' : $row['user_id'];

	$t->assign([
		'ADMIN_ALLPFS_ROW_URL' => cot_url('pfs', 'userid='.$row['user_id']),
		'ADMIN_ALLPFS_ROW_USER' => cot_build_user($row['user_id'], $row['user_name']),
		'ADMIN_ALLPFS_ROW_COUNT' => $row['COUNT(*)'],
	]);

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl) {
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.ALLPFS_ROW');
	$ii++;
}

$t->assign([
	'ADMIN_ALLPFS_ON_PAGE' => $ii,
]);

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
	// @deprecated in 0.9.25
	$t->assign([
		'ADMIN_ALLPFS_PAGINATION_PREV' => $pagenav['prev'],
		'ADMIN_ALLPFS_PAGNAV' => $pagenav['main'],
		'ADMIN_ALLPFS_PAGINATION_NEXT' => $pagenav['next'],
		'ADMIN_ALLPFS_TOTALITEMS' => $totalitems,
	]);
}

$t->assign(cot_generatePaginationTags($pagenav));

/* === Hook  === */
foreach (cot_getextplugins('admin.pfs.allpfs.tags') as $pl) {
	include $pl;
}
/* ===== */