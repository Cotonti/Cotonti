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

sed_require_api('auth');
sed_require_api('uploads');

$t = new XTemplate(sed_skinfile('admin.users'));



$adminpath[] = array(sed_url('admin', 'm=users'), $L['Users']);

$g = sed_import('g', 'G', 'INT');

$lincif_extfld = sed_auth('admin', 'a', 'A');

/* === Hook === */
foreach (sed_getextplugins('admin.users.first') as $pl)
{
	include $pl;
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
	foreach (sed_getextplugins('admin.users.add') as $pl)
	{
		include $pl;
	}
	/* ===== */

	sed_auth_add_group($grp_id, $ncopyrightsfrom);

	$cot_cache->db->remove('sed_groups', 'system');

	sed_message('Added');
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
		foreach (sed_getextplugins('admin.users.update') as $pl)
		{
			include $pl;
		}
		/* ===== */

		$rtitle = sed_sql_prep($rtitle);
	   	$rdesc = sed_sql_prep($rdesc);
	   	$ricon = sed_sql_prep($ricon);
	   	$ralias = sed_sql_prep($ralias);

		$sql = (!empty($rtitle)) ? sed_sql_query("UPDATE $db_groups SET grp_title='$rtitle', grp_desc='$rdesc', grp_icon='$ricon', grp_alias='$ralias', grp_level='$rlevel', grp_pfs_maxfile='$rmaxfile', grp_pfs_maxtotal='$rmaxtotal', grp_disabled='$rdisabled', grp_hidden='$rhidden', grp_maintenance='$rmtmode' WHERE grp_id='$g'") : '';

		$cot_cache->db->remove('sed_groups', 'system');

		sed_message('Updated');
	}
	elseif($a == 'delete' && $g > 5)
	{
		$sql = sed_sql_query("DELETE FROM $db_groups WHERE grp_id='$g'");
		sed_auth_remove_group($g);
		$sql = sed_sql_query("DELETE FROM $db_groups_users WHERE gru_groupid='$g'");

		/* === Hook === */
		foreach (sed_getextplugins('admin.users.delete') as $pl)
		{
			include $pl;
		}
		/* ===== */
		sed_auth_clear('all');
		$cot_cache->db->remove('sed_groups', 'system');

		sed_message('Deleted');
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

		$t->assign(array(
			'ADMIN_USERS_EDITFORM_URL' => sed_url('admin', 'm=users&n=edit&a=update&g='.$g),
			'ADMIN_USERS_EDITFORM_GRP_TITLE' => sed_inputbox('text', 'rtitle', $row['grp_title'], 'size="40" maxlength="64"'),
			'ADMIN_USERS_EDITFORM_GRP_DESC' => sed_inputbox('text', 'rdesc', htmlspecialchars($row['grp_desc']), 'size="40" maxlength="64"'),
			'ADMIN_USERS_EDITFORM_GRP_ICON' => sed_inputbox('text', 'ricon', htmlspecialchars($row['grp_icon']), 'size="40" maxlength="128"'),
			'ADMIN_USERS_EDITFORM_GRP_ALIAS' => sed_inputbox('text', 'ralias', htmlspecialchars($row['grp_alias']), 'size="40" maxlength="24"'),
			'ADMIN_USERS_EDITFORM_GRP_PFS_MAXFILE' => sed_inputbox('text', 'rmaxfile', htmlspecialchars($row['grp_pfs_maxfile']), 'size="16" maxlength="16"'),
			'ADMIN_USERS_EDITFORM_GRP_PFS_MAXTOTAL' => sed_inputbox('text', 'rmaxtotal', htmlspecialchars($row['grp_pfs_maxtotal']), 'size="16" maxlength="16"'),
			'ADMIN_USERS_EDITFORM_GRP_DISABLED' => ($g <= 5) ? $L['No'] : sed_radiobox($row['grp_disabled'], 'rdisabled', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_USERS_EDITFORM_GRP_HIDDEN' => ($g == 4) ? $L['No'] : sed_radiobox($row['grp_hidden'], 'rhidden', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_USERS_EDITFORM_GRP_MAINTENANCE' => sed_radiobox($row['grp_maintenance'], 'rmtmode', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_USERS_EDITFORM_GRP_RLEVEL' => sed_selectbox($row['grp_level'], 'rlevel', range(0, 99), range(0, 99), false),
			'ADMIN_USERS_EDITFORM_GRP_MEMBERSCOUNT' => $row['grp_memberscount'],
			'ADMIN_USERS_EDITFORM_GRP_MEMBERSCOUNT_URL' => sed_url('users', 'g='.$g),
			'ADMIN_USERS_EDITFORM_RIGHT_URL' => sed_url('admin', 'm=rights&g='.$g),
			'ADMIN_USERS_EDITFORM_DEL_URL' => sed_url('admin', 'm=users&n=edit&a=delete&g='.$g.'&'.sed_xg()),
		));
		/* === Hook === */
		foreach (sed_getextplugins('admin.users.edit.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */
		$t->parse('MAIN.ADMIN_USERS_EDIT');
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
			$t->assign(array(
				'ADMIN_USERS_ROW_GRP_TITLE_URL' => sed_url('admin', 'm=users&n=edit&g='.$row['grp_id']),
				'ADMIN_USERS_ROW_GRP_TITLE' => htmlspecialchars($row['grp_title']),
				'ADMIN_USERS_ROW_GRP_ID' => $row['grp_id'],
				'ADMIN_USERS_ROW_GRP_COUNT_MEMBERS' => $members[$row['grp_id']],
				'ADMIN_USERS_ROW_GRP_DISABLED' => $sed_yesno[!$row['grp_disabled']],
				'ADMIN_USERS_ROW_GRP_HIDDEN' => $sed_yesno[$row['grp_hidden']],
				'ADMIN_USERS_ROW_GRP_RIGHTS_URL' => sed_url('admin', 'm=rights&g='.$row['grp_id']),
				'ADMIN_USERS_ROW_GRP_JUMPTO_URL' => sed_url('users', 'g='.$row['grp_id'])
			));
			$t->parse('MAIN.ADMIN_USERS_DEFAULT.USERS_ROW');
		}
	}

	$t->assign(array(
		'ADMIN_USERS_FORM_URL' => sed_url('admin', 'm=users&n=add'),
		'ADMIN_USERS_NGRP_TITLE' => sed_inputbox('text', 'ntitle', '', 'size="40" maxlength="64"'),
		'ADMIN_USERS_NGRP_DESC' => sed_inputbox('text', 'ndesc', '', 'size="40" maxlength="64"'),
		'ADMIN_USERS_NGRP_ICON' => sed_inputbox('text', 'nicon', '', 'size="40" maxlength="128"'),
		'ADMIN_USERS_NGRP_ALIAS' => sed_inputbox('text', 'nalias', '', 'size="40" maxlength="24"'),
		'ADMIN_USERS_NGRP_PFS_MAXFILE' => sed_inputbox('text', 'nmaxfile', '', 'size="16" maxlength="16"'),
		'ADMIN_USERS_NGRP_PFS_MAXTOTAL' => sed_inputbox('text', 'nmaxtotal', '', 'size="16" maxlength="16"'),
		'ADMIN_USERS_NGRP_DISABLED' => sed_radiobox(0, 'ndisabled', array(1, 0), array($L['Yes'], $L['No'])),
		'ADMIN_USERS_NGRP_HIDDEN' => sed_radiobox(0, 'nhidden', array(1, 0), array($L['Yes'], $L['No'])),
		'ADMIN_USERS_NGRP_MAINTENANCE' => sed_radiobox(0, 'nmtmode', array(1, 0), array($L['Yes'], $L['No'])),
		'ADMIN_USERS_NGRP_RLEVEL' => sed_selectbox(50, 'nlevel', range(0, 99), range(0, 99), false),
		'ADMIN_USERS_FORM_SELECTBOX_GROUPS' => sed_selectbox_groups(4, 'ncopyrightsfrom', array('5'))
	));
	$t->parse('MAIN.ADMIN_USERS_DEFAULT');
}

$t->assign(array(
	'ADMIN_USERS_URL' => sed_url('admin', 'm=config&n=edit&o=core&p=users'),
	'ADMIN_USERS_EXTRAFIELDS_URL' => sed_url('admin', 'm=extrafields&n=users')
));


if (sed_check_messages())
{
	$t->assign('MESSAGE_TEXT', sed_implode_messages());
	$t->parse('MAIN.MESSAGE');
	sed_clear_messages();
}

/* === Hook  === */
foreach (sed_getextplugins('admin.users.tags') as $pl)
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