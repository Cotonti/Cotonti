<?php
/**
 * Administration panel - PFS manager
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.pfs.allpfs'));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=pfs'), $L['PFS']);
$adminpath[] = array(sed_url('admin', 'm=pfs&s=allpfs'), $L['adm_allpfs']);
$adminhelp = $L['adm_help_allpfs'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;

/* === Hook === */
$extp = sed_getextplugins('admin.pfs.allpfs.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

unset($disp_list);

$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(DISTINCT pfs_userid) FROM $db_pfs WHERE pfs_folderid>=0"), 0, "COUNT(DISTINCT pfs_userid)");
$pagenav = sed_pagenav('admin', 'm=pfs&s=allpfs', $d, $totalitems, $cfg['maxrowsperpage'], 'd', '', $cfg['jquery'] && $cfg['turnajax']);

$sql = sed_sql_query("SELECT DISTINCT p.pfs_userid, u.user_name, u.user_id, COUNT(*) FROM $db_pfs AS p
	LEFT JOIN $db_users AS u ON p.pfs_userid=u.user_id
	WHERE pfs_folderid>=0 GROUP BY p.pfs_userid ORDER BY u.user_name ASC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;
/* === Hook - Part1 : Set === */
$extp = sed_getextplugins('admin.pfs.allpfs.loop');
/* ===== */
while($row = sed_sql_fetcharray($sql))
{
	$row['user_name'] = ($row['user_id'] == 0) ? $L['SFS'] : $row['user_name'];
	$row['user_id'] = ($row['user_id'] == 0) ? "0" : $row['user_id'];

	$t->assign(array(
		"ADMIN_ALLPFS_ROW_URL" => sed_url('pfs', "userid=".$row['user_id']),
		"ADMIN_ALLPFS_ROW_USER" => sed_build_user($row['user_id'], htmlspecialchars($row['user_name'])),
		"ADMIN_ALLPFS_ROW_COUNT" => $row['COUNT(*)']
	));

	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse("ALLPFS.ALLPFS_ROW");
	$ii++;
}

$t->assign(array(
	"ADMIN_ALLPFS_PAGINATION_PREV" => $pagenav['prev'],
	"ADMIN_ALLPFS_PAGNAV" => $pagenav['main'],
	"ADMIN_ALLPFS_PAGINATION_NEXT" => $pagenav['next'],
	"ADMIN_ALLPFS_TOTALITEMS" => $totalitems,
	"ADMIN_ALLPFS_ON_PAGE" => $ii
));

/* === Hook  === */
$extp = sed_getextplugins('admin.pfs.allpfs.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('ALLPFS');
if (SED_AJAX)
{
	$t->out('ALLPFS');
}
else
{
	$adminmain = $t->text('ALLPFS');
}

?>