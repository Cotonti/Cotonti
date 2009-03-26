<?php
/**
 * Administration panel
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

$t = new XTemplate(sed_skinfile('admin.pfs.allpfs.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
$adminpath[] = array(sed_url('admin', 'm=pfs'), $L['PFS']);
$adminpath[] = array(sed_url('admin', 'm=pfs&s=allpfs'), $L['adm_allpfs']);
$adminhelp = $L['adm_help_allpfs'];

$d = sed_import('d', 'G', 'INT');
$d = empty($d) ? 0 : (int) $d;
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

unset ($disp_list);

$totalitems = sed_sql_result(sed_sql_query("SELECT COUNT(DISTINCT pfs_userid) FROM $db_pfs WHERE pfs_folderid>=0"), 0, "COUNT(DISTINCT pfs_userid)");
if($cfg['jquery'])
{
	$pagnav = sed_pagination(sed_url('admin','m=pfs&s=allpfs'), $d, $totalitems, $cfg['maxrowsperpage'], 'd', 'ajaxSend', "url: '".sed_url('admin','m=pfs&s=allpfs&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=pfs&s=allpfs'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE, 'd', 'ajaxSend', "url: '".sed_url('admin','m=pfs&s=allpfs&ajax=1')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'");
}
else
{
	$pagnav = sed_pagination(sed_url('admin','m=pfs&s=allpfs'), $d, $totalitems, $cfg['maxrowsperpage']);
	list($pagination_prev, $pagination_next) = sed_pagination_pn(sed_url('admin', 'm=pfs&s=allpfs'), $d, $totalitems, $cfg['maxrowsperpage'], TRUE);
}

$sql = sed_sql_query("SELECT DISTINCT p.pfs_userid, u.user_name, u.user_id, COUNT(*) FROM $db_pfs AS p
	LEFT JOIN $db_users AS u ON p.pfs_userid=u.user_id
	WHERE pfs_folderid>=0 GROUP BY p.pfs_userid ORDER BY u.user_name ASC LIMIT $d, ".$cfg['maxrowsperpage']);

$ii = 0;

while($row = sed_sql_fetcharray($sql))
{
	$row['user_name'] = ($row['user_id']==0) ? $L['SFS'] : $row['user_name'];
	$row['user_id'] = ($row['user_id']==0) ? "0" : $row['user_id'];

	$t -> assign(array(
		"ADMIN_ALLPFS_ROW_URL" => sed_url('pfs', "userid=".$row['user_id']),
		"ADMIN_ALLPFS_ROW_USER" => sed_build_user($row['user_id'], sed_cc($row['user_name'])),
		"ADMIN_ALLPFS_ROW_COUNT" => $row['COUNT(*)']
	));
	$t -> parse("ALLPFS.ALLPFS_ROW");
	$ii++;
}

$t -> assign(array(
	"ADMIN_ALLPFS_AJAX_OPENDIVID" => 'pagtab',
	"ADMIN_ALLPFS_PAGINATION_PREV" => $pagination_prev,
	"ADMIN_ALLPFS_PAGNAV" => $pagnav,
	"ADMIN_ALLPFS_PAGINATION_NEXT" => $pagination_next,
	"ADMIN_ALLPFS_TOTALITEMS" => $totalitems,
	"ADMIN_ALLPFS_ON_PAGE" => $ii
));
$t -> parse("ALLPFS");
$adminmain = $t -> text("ALLPFS");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>