<?php
/**
 * Administration panel - Users
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

$t = new XTemplate(sed_skinfile('admin.users.inc', false, true));

$adminpath[] = array(sed_url('admin', 'm=users'), $L['Users']);

$g = sed_import('g', 'G', 'INT');
$ajax = sed_import('ajax', 'G', 'INT');
$ajax = empty($ajax) ? 0 : (int) $ajax;

$lincif_extfld = sed_auth('admin', 'a', 'A');

/* === Hook === */
$extp = sed_getextplugins('admin.users.first');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

if($n == 'add')
{
	$ntitle = sed_import('ntitle', 'P', 'TXT');
	$ndesc = sed_import('ndesc', 'P', 'TXT');
	$nicon = sed_import('nicon', 'P', 'TXT');
	$nalias = sed_import('nalias', 'P', 'TXT');
	$nlevel = sed_import('nlevel', 'P', 'LVL');
	$nmaxsingle = min(sed_import('nmaxsingle', 'P', 'INT'), sed_get_uploadmax());
	$nmaxtotal = sed_import('nmaxtotal', 'P', 'INT');
	$ncopyrightsfrom = sed_import('ncopyrightsfrom', 'P', 'INT');
	$ndisabled = sed_import('ndisabled', 'P', 'BOL');
	$nhidden = sed_import('nhidden', 'P', 'BOL');
	$nmtmode = sed_import('nmtmode', 'P', 'BOL');

	$sql = (!empty($ntitle)) ? sed_sql_query("INSERT INTO $db_groups (grp_alias, grp_level, grp_disabled, grp_hidden,  grp_maintenance, grp_title, grp_desc, grp_icon, grp_pfs_maxfile, grp_pfs_maxtotal, grp_ownerid) VALUES ('".sed_sql_prep($nalias)."', ".(int)$nlevel.", ".(int)$ndisabled.", ".(int)$nhidden.",  ".(int)$nmtmode.", '".sed_sql_prep($ntitle)."', '".sed_sql_prep($ndesc)."', '".sed_sql_prep($nicon)."', ".(int)$nmaxsingle.", ".(int)$nmaxtotal.", ".(int)$usr['id'].")") : '';
	$grp_id = sed_sql_insertid();

	/* === Hook === */
	$extp = sed_getextplugins('admin.users.add');
	if(is_array($extp))
	{
		foreach($extp as $k => $pl)
		{
			include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
		}
	}
	/* ===== */

	$sql = sed_sql_query("SELECT * FROM $db_auth WHERE auth_groupid='".$ncopyrightsfrom."' order by auth_code ASC, auth_option ASC");
	while($row = sed_sql_fetcharray($sql))
	{
		$sql1 = sed_sql_query("INSERT into $db_auth (auth_groupid, auth_code, auth_option, auth_rights, auth_rights_lock, auth_setbyuserid) VALUES (".(int)$grp_id.", '".$row['auth_code']."', '".$row['auth_option']."', ".(int)$row['auth_rights'].", 0, ".(int)$usr['id'].")");
	}

	sed_auth_reorder();
	$cot_cache->db_clear('sed_groups', 'system');

	$adminwarnings = $L['Added'];
}
elseif($n == 'edit')
{
	if($a == 'update')
	{
		$rtitle = sed_import('rtitle', 'P', 'TXT');
		$rdesc = sed_import('rdesc', 'P', 'TXT');
		$ricon = sed_import('ricon', 'P', 'TXT');
		$ralias = sed_import('ralias', 'P', 'TXT');
		$rlevel = sed_import('rlevel', 'P', 'LVL');
		$rmaxfile = min(sed_import('rmaxfile', 'P', 'INT'), sed_get_uploadmax());
		$rmaxtotal = sed_import('rmaxtotal', 'P', 'INT');
		$rdisabled = ($g < 6) ? 0 : sed_import('rdisabled', 'P', 'BOL');
		$rhidden = ($g == 4) ? 0 : sed_import('rhidden', 'P', 'BOL');
		$rmtmode = sed_import('rmtmode', 'P', 'BOL');

		/* === Hook === */
		$extp = sed_getextplugins('admin.users.update');
		if(is_array($extp))
		{
			foreach($extp as $k => $pl)
			{
				include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
			}
		}
		/* ===== */

		$rtitle = sed_sql_prep($rtitle);
	   	$rdesc = sed_sql_prep($rdesc);
	   	$ricon = sed_sql_prep($ricon);
	   	$ralias = sed_sql_prep($ralias);

		$sql = (!empty($rtitle)) ? sed_sql_query("UPDATE $db_groups SET grp_title='$rtitle', grp_desc='$rdesc', grp_icon='$ricon', grp_alias='$ralias', grp_level='$rlevel', grp_pfs_maxfile='$rmaxfile', grp_pfs_maxtotal='$rmaxtotal', grp_disabled='$rdisabled', grp_hidden='$rhidden', grp_maintenance='$rmtmode' WHERE grp_id='$g'") : '';

		$cot_cache->db_remove('sed_groups', 'system');

		$adminwarnings = $L['Updated'];
	}
	elseif($a == 'delete' && $g > 5)
	{
		$sql = sed_sql_query("DELETE FROM $db_groups WHERE grp_id='$g'");
		$sql = sed_sql_query("DELETE FROM $db_auth WHERE auth_groupid='$g'");
		$sql = sed_sql_query("DELETE FROM $db_groups_users WHERE gru_groupid='$g'");

		/* === Hook === */
		$extp = sed_getextplugins('admin.users.delete');
		if(is_array($extp))
		{
			foreach($extp as $k => $pl)
			{
				include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
			}
		}
		/* ===== */
		sed_auth_clear('all');
		$cot_cache->db_remove('sed_groups', 'system');

		$adminwarnings = $L['Deleted'];
	}
	else
	{
       	$showdefault = false;

	    $sql = sed_sql_query("SELECT * FROM $db_groups WHERE grp_id='$g'");
		sed_die(sed_sql_numrows($sql) == 0);
		$row = sed_sql_fetcharray($sql);

		$sql1 = sed_sql_query("SELECT COUNT(*) FROM $db_groups_users WHERE gru_groupid='$g'");
		$row['grp_memberscount'] = sed_sql_result($sql1, 0, "COUNT(*)");

		$row['grp_title'] = htmlspecialchars($row['grp_title']);

		$adminpath[] = array (sed_url('admin', 'm=users&n=edit&g='.$g), $row['grp_title']);

		for($i = 1;$i < 100;$i++)
		{
			$t -> assign(array(
				"ADMIN_USERS_EDITFORM_RLEVEL_ITEM_SELECTED" => ($i == $row['grp_level']) ? ' selected="selected"' : '',
				"ADMIN_USERS_EDITFORM_RLEVEL_ITEM" => $i
			));
			$t -> parse("USERS.ADMIN_USERS_EDIT.SELECT_RLEVEL");
		}

		$t -> assign(array(
			"ADMIN_USERS_EDITFORM_URL" => sed_url('admin', "m=users&n=edit&a=update&g=".$g),
			"ADMIN_USERS_EDITFORM_URL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onsubmit=\"return ajaxSend({method: 'POST', formId: 'editlevel', url: '".sed_url('admin','m=users&ajax=1&n=edit&a=update&g='.$g)."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
			"ADMIN_USERS_EDITFORM_GRP_TITLE" => $row['grp_title'],
			"ADMIN_USERS_EDITFORM_GRP_DESC" => htmlspecialchars($row['grp_desc']),
			"ADMIN_USERS_EDITFORM_GRP_ICON" => htmlspecialchars($row['grp_icon']),
			"ADMIN_USERS_EDITFORM_GRP_ALIAS" => htmlspecialchars($row['grp_alias']),
			"ADMIN_USERS_EDITFORM_GRP_PFS_MAXFILE" => htmlspecialchars($row['grp_pfs_maxfile']),
			"ADMIN_USERS_EDITFORM_GRP_PFS_MAXTOTAL" => htmlspecialchars($row['grp_pfs_maxtotal']),
			"ADMIN_USERS_EDITFORM_GRP_PFS_MEMBERSCOUNT" => $row['grp_memberscount'],
			"ADMIN_USERS_EDITFORM_GRP_PFS_MEMBERSCOUNT_URL" => sed_url('users', "g=".$g),
			"ADMIN_USERS_EDITFORM_RIGHT_URL" => sed_url('admin', "m=rights&g=".$g),
			"ADMIN_USERS_EDITFORM_DEL_URL" => sed_url('admin', "m=users&n=edit&a=delete&g=".$g."&".sed_xg()),
			"ADMIN_USERS_EDITFORM_DEL_URL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onclick=\"return ajaxSend({method: 'POST', formId: 'editlevel', url: '".sed_url('admin','m=users&ajax=1&n=edit&a=delete&g='.$g.'&'.sed_xg())."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
		));
		/* === Hook === */
		$extp = sed_getextplugins('admin.users.edit.tags');
		if(is_array($extp))
		{
			foreach($extp as $k => $pl)
			{
				include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
			}
		}
		/* ===== */
		$t -> parse("USERS.ADMIN_USERS_EDIT");
	}
}

if(!isset($showdefault) OR $showdefault == true)
{
	$sql = sed_sql_query("SELECT DISTINCT(gru_groupid), COUNT(*) FROM $db_groups_users WHERE 1 GROUP BY gru_groupid");
	while($row = sed_sql_fetcharray($sql))
	{
		$members[$row['gru_groupid']] = $row['COUNT(*)'];
	}

	$sql = sed_sql_query("SELECT grp_id, grp_title, grp_disabled, grp_hidden FROM $db_groups WHERE 1 order by grp_level DESC, grp_id DESC");

	if(sed_sql_numrows($sql) > 0)
	{
		while($row = sed_sql_fetcharray($sql))
		{
			$row['grp_hidden'] = ($row['grp_hidden']) ? '1' : '0';
			$members[$row['grp_id']] = (empty($members[$row['grp_id']])) ? '0' : $members[$row['grp_id']];
			$t -> assign(array(
				"ADMIN_USERS_ROW_GRP_TITLE_URL" => sed_url('admin', "m=users&n=edit&g=".$row['grp_id']),
				"ADMIN_USERS_ROW_GRP_TITLE_URL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onclick=\"return ajaxSend({url: '".sed_url('admin','m=users&ajax=1&n=edit&g='.$row['grp_id'])."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
				"ADMIN_USERS_ROW_GRP_TITLE" => htmlspecialchars($row['grp_title']),
				"ADMIN_USERS_ROW_GRP_ID" => $row['grp_id'],
				"ADMIN_USERS_ROW_GRP_COUNT_MEMBERS" => $members[$row['grp_id']],
				"ADMIN_USERS_ROW_GRP_DISABLED" => $sed_yesno[!$row['grp_disabled']],
				"ADMIN_USERS_ROW_GRP_HIDDEN" => $sed_yesno[$row['grp_hidden']],
				"ADMIN_USERS_ROW_GRP_RIGHTS_URL" => sed_url('admin', "m=rights&g=".$row['grp_id']),
				"ADMIN_USERS_ROW_GRP_JUMPTO_URL" => sed_url('users', "g=".$row['grp_id'])
			));
			$t -> parse("USERS.ADMIN_USERS_DEFAULT.USERS_ROW");
		}
	}

	for($i = 1;$i < 100;$i++)
	{
		$t -> assign(array(
			"ADMIN_USERS_FORM_SELECT_VALUE" => $i
		));
		$t -> parse("USERS.ADMIN_USERS_DEFAULT.USERS_FORM_SELECT_NLEVEL");
	}

	$t -> assign(array(
		"ADMIN_USERS_FORM_URL" => sed_url('admin', "m=users&n=add"),
		"ADMIN_USERS_FORM_URL_AJAX" => ($cfg['jquery'] AND $cfg['turnajax']) ? " onsubmit=\"return ajaxSend({method: 'POST', formId: 'addlevel', url: '".sed_url('admin','m=users&ajax=1&n=add')."', divId: 'pagtab', errMsg: '".$L['ajaxSenderror']."'});\"" : "",
		"ADMIN_USERS_FORM_SELECTBOX_GROUPS" => sed_selectbox_groups(4, 'ncopyrightsfrom', array('5'))
	));
	$t -> parse("USERS.ADMIN_USERS_DEFAULT");
}

$is_adminwarnings = isset($adminwarnings);

$t -> assign(array(
	"ADMIN_USERS_AJAX_OPENDIVID" => 'pagtab',
	"ADMIN_USERS_URL" => sed_url('admin', "m=config&n=edit&o=core&p=users"),
	"ADMIN_USERS_EXTRAFIELDS_URL" => sed_url('admin', 'm=users&s=extrafields'),
	"ADMIN_USERS_ADMINWARNINGS" => $adminwarnings
));

/* === Hook  === */
$extp = sed_getextplugins('admin.users.tags');
if(is_array($extp))
{
	foreach($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

$t -> parse("USERS");
$adminmain = $t -> text("USERS");

if($ajax)
{
	sed_sendheaders();
	echo $adminmain;
	exit;
}

?>