<?php
/**
 * Administration panel - Users
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
if ($usr['maingrp'] == COT_GROUP_SUPERADMINS)
{
	$usr['auth_read'] = true;
	$usr['auth_write'] = true;
	$usr['isadmin'] = true;
}
cot_block($usr['isadmin']);

require_once cot_incfile('auth');
require_once cot_incfile('uploads');

$t = new XTemplate(cot_tplfile('admin.users', 'core'));

$adminpath[] = array(cot_url('admin', 'm=users'), $L['Users']);
$adminsubtitle = $L['Users'];

$g = cot_import('g', 'G', 'INT');

$lincif_extfld = cot_auth('admin', 'a', 'A');

/* === Hook === */
foreach (cot_getextplugins('admin.users.first') as $pl)
{
	include $pl;
}
/* ===== */

if($n == 'add')
{
	$rgroups['grp_name'] = cot_import('rname', 'P', 'TXT');
	$rgroups['grp_title'] = cot_import('rtitle', 'P', 'TXT');
	$rgroups['grp_desc'] = cot_import('rdesc', 'P', 'TXT');
	$rgroups['grp_icon'] = cot_import('ricon', 'P', 'TXT');
	$rgroups['grp_alias'] = cot_import('ralias', 'P', 'TXT');
	$rgroups['grp_level'] = (int)cot_import('rlevel', 'P', 'INT');
	$rgroups['grp_disabled'] = cot_import('rdisabled', 'P', 'BOL') ? 1 : 0;
	$rgroups['grp_maintenance'] = cot_import('rmtmode', 'P', 'BOL') ? 1 : 0;
	$rgroups['grp_skiprights'] = cot_import('rskiprights', 'P', 'BOL') ? 1 : 0;
	$rgroups['grp_ownerid'] = (int)$usr['id'];

	$rcopyrightsfrom = cot_import('rcopyrightsfrom', 'P', 'INT');

	/* === Hook === */
	foreach (cot_getextplugins('admin.users.add.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	cot_check(empty($rgroups['grp_name']), 'adm_groups_name_empty', 'rname');
	cot_check(empty($rgroups['grp_title']), 'adm_groups_title_empty', 'rtitle');

	if (!cot_error_found())
	{
		$db->insert($db_groups, $rgroups);

		$grp_id = $db->lastInsertId();

		/* === Hook === */
		foreach (cot_getextplugins('admin.users.add') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if (!$rgroups['grp_skiprights'])
		{
			cot_auth_add_group($grp_id, $rcopyrightsfrom);
		}

		$cache && $cache->db->remove('cot_groups', 'system');

		cot_message('Added');
	}
	cot_redirect(cot_url('admin', 'm=users', '', true));
}
elseif($n == 'edit')
{
	if($a == 'update')
	{
		$rgroups['grp_name'] = cot_import('rname', 'P', 'TXT');
		$rgroups['grp_title'] = cot_import('rtitle', 'P', 'TXT');
		$rgroups['grp_desc'] = cot_import('rdesc', 'P', 'TXT');
		$rgroups['grp_icon'] = cot_import('ricon', 'P', 'TXT');
		$rgroups['grp_alias'] = cot_import('ralias', 'P', 'TXT');
		$rgroups['grp_level'] = (int)cot_import('rlevel', 'P', 'INT');
		$rgroups['grp_disabled'] = cot_import('rdisabled', 'P', 'BOL') ? 1 : 0;
		$rgroups['grp_maintenance'] = cot_import('rmtmode', 'P', 'BOL') ? 1 : 0;
		$rgroups['grp_skiprights'] = cot_import('rskiprights', 'P', 'BOL') ? 1 : 0;

		/* === Hook === */
		foreach (cot_getextplugins('admin.users.update') as $pl)
		{
			include $pl;
		}
		/* ===== */

		cot_check(empty($rgroups['grp_name']), 'adm_groups_name_empty', 'rname');
		cot_check(empty($rgroups['grp_title']), 'adm_groups_title_empty', 'rtitle');

		if (!cot_error_found())
		{
			$db->update($db_groups, $rgroups, "grp_id=$g");

			$was_rightless = $db->query("SELECT grp_skiprights FROM $db_groups WHERE grp_id = $g")->fetchColumn();
			if ($was_rightless && !$rgroups['grp_skiprights'])
			{
				// Add missing rights from default group
				cot_auth_add_group($grp_id, COT_GROUP_MEMBERS);
			}
			elseif (!$was_rightless && $rgroups['grp_skiprights'])
			{
				// Remove rights
				cot_auth_remove_group($g);
			}

			$cache && $cache->db->remove('cot_groups', 'system');

			cot_message('Updated');
		}
        cot_redirect(cot_url('admin', array('m' => 'users', 'n'=>'edit', 'g'=>$g), '', true));

	}
	elseif($a == 'delete' && $g > 5)
	{
		$sql = $db->delete($db_groups, "grp_id='$g'");
		$sql = $db->delete($db_groups_users, "gru_groupid='$g'");
		cot_auth_remove_group($g);

		/* === Hook === */
		foreach (cot_getextplugins('admin.users.delete') as $pl)
		{
			include $pl;
		}
		/* ===== */
		cot_auth_clear('all');
		$cache && $cache->db->remove('cot_groups', 'system');

		cot_message('Deleted');

        cot_redirect(cot_url('admin', 'm=users', '', true));
	}
	else
	{
       	$showdefault = false;

	    $sql = $db->query("SELECT * FROM $db_groups WHERE grp_id='$g'");
		cot_die($sql->rowCount() == 0);
		$row = $sql->fetch();

		$sql1 = $db->query("SELECT COUNT(*) FROM $db_groups_users WHERE gru_groupid='$g'");
		$row['grp_memberscount'] = $sql1->fetchColumn();

		$row['grp_name'] = htmlspecialchars($row['grp_name']);
		$row['grp_title'] = htmlspecialchars($row['grp_title']);

		$adminpath[] = array (cot_url('admin', 'm=users&n=edit&g='.$g), $row['grp_name']);

		$t->assign(array(
            'ADMIN_USERS_GRP_NAME' => $row['grp_name'],
            'ADMIN_USERS_GRP_TITLE' => $row['grp_title'],
			'ADMIN_USERS_EDITFORM_URL' => cot_url('admin', 'm=users&n=edit&a=update&g='.$g),
			'ADMIN_USERS_EDITFORM_GRP_NAME' => cot_inputbox('text', 'rname', $row['grp_name'], 'size="40" maxlength="64"'),
			'ADMIN_USERS_EDITFORM_GRP_TITLE' => cot_inputbox('text', 'rtitle', $row['grp_title'], 'size="40" maxlength="64"'),
			'ADMIN_USERS_EDITFORM_GRP_DESC' => cot_inputbox('text', 'rdesc', htmlspecialchars($row['grp_desc']), 'size="40" maxlength="64"'),
			'ADMIN_USERS_EDITFORM_GRP_ICON' => cot_inputbox('text', 'ricon', htmlspecialchars($row['grp_icon']), 'size="40" maxlength="128"'),
			'ADMIN_USERS_EDITFORM_GRP_ALIAS' => cot_inputbox('text', 'ralias', htmlspecialchars($row['grp_alias']), 'size="40" maxlength="24"'),
			'ADMIN_USERS_EDITFORM_GRP_DISABLED' => ($g <= 5) ? $L['No'] : cot_radiobox($row['grp_disabled'], 'rdisabled', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_USERS_EDITFORM_GRP_MAINTENANCE' => cot_radiobox($row['grp_maintenance'], 'rmtmode', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_USERS_EDITFORM_GRP_SKIPRIGHTS' => cot_radiobox($row['grp_skiprights'], 'rskiprights', array(1, 0), array($L['Yes'], $L['No'])),
			'ADMIN_USERS_EDITFORM_GRP_RLEVEL' => cot_selectbox($row['grp_level'], 'rlevel', range(0, 99), range(0, 99), false),
			'ADMIN_USERS_EDITFORM_GRP_MEMBERSCOUNT' => $row['grp_memberscount'],
			'ADMIN_USERS_EDITFORM_GRP_MEMBERSCOUNT_URL' => cot_url('users', 'g='.$g),
			'ADMIN_USERS_EDITFORM_SKIPRIGHTS' => $row['grp_skiprights'],
			'ADMIN_USERS_EDITFORM_RIGHT_URL' => cot_url('admin', 'm=rights&g='.$g),
			'ADMIN_USERS_EDITFORM_DEL_URL' => cot_url('admin', 'm=users&n=edit&a=delete&g='.$g.'&'.cot_xg()),
            'ADMIN_USERS_EDITFORM_DEL_CONFIRM_URL' => cot_confirm_url(cot_url('admin', 'm=users&n=edit&a=delete&g='.$g.'&'.cot_xg())),
		));

		/* === Hook === */
		foreach (cot_getextplugins('admin.users.edit.tags') as $pl)
		{
			include $pl;
		}
		/* ===== */
		$t->parse('MAIN.ADMIN_USERS_EDIT');
	}
}

if(!isset($showdefault) || $showdefault == true)
{
	$sql = $db->query("SELECT DISTINCT(gru_groupid), COUNT(*) FROM $db_groups_users WHERE 1 GROUP BY gru_groupid");
	while($row = $sql->fetch())
	{
		$members[$row['gru_groupid']] = $row['COUNT(*)'];
	}
	$sql->closeCursor();

	$sql = $db->query("SELECT * FROM $db_groups WHERE 1 ORDER BY grp_level DESC, grp_id DESC");

	if($sql->rowCount() > 0)
	{
		/* === Hook - Part1 : Set === */
		$extp = cot_getextplugins('admin.users.row.tags');
		/* ===== */
		foreach ($sql->fetchAll() as $row)
		{
			$members[$row['grp_id']] = (empty($members[$row['grp_id']])) ? '0' : $members[$row['grp_id']];
			$grp_title = isset($L['users_grp_' . $row['grp_id'] . '_title']) ? $L['users_grp_' . $row['grp_id'] . '_title'] : htmlspecialchars($row['grp_title']);
			$grp_desc = isset($L['users_grp_' . $row['grp_id'] . '_desc']) ? $L['users_grp_' . $row['grp_id'] . '_desc'] : htmlspecialchars($row['grp_desc']);
			$t->assign(array(
				'ADMIN_USERS_ROW_GRP_TITLE_URL' => cot_url('admin', 'm=users&n=edit&g='.$row['grp_id']),
				'ADMIN_USERS_ROW_GRP_NAME' => htmlspecialchars($row['grp_name']),
				'ADMIN_USERS_ROW_GRP_TITLE' => $grp_title,
				'ADMIN_USERS_ROW_GRP_DESC' => $grp_desc,
				'ADMIN_USERS_ROW_GRP_ID' => $row['grp_id'],
				'ADMIN_USERS_ROW_GRP_COUNT_MEMBERS' => $members[$row['grp_id']],
				'ADMIN_USERS_ROW_GRP_DISABLED' => $cot_yesno[!$row['grp_disabled']],
				'ADMIN_USERS_ROW_GRP_SKIPRIGHTS' => $row['grp_skiprights'],
				'ADMIN_USERS_ROW_GRP_RIGHTS_URL' => cot_url('admin', 'm=rights&g='.$row['grp_id']),
				'ADMIN_USERS_ROW_GRP_JUMPTO_URL' => cot_url('users', 'g='.$row['grp_id'])
			));
			/* === Hook - Part2 : Include === */
			foreach ($extp as $pl)
			{
				include $pl;
			}
			/* ===== */
			$t->parse('MAIN.ADMIN_USERS_DEFAULT.USERS_ROW');
		}
	}

	$t->assign(array(
		'ADMIN_USERS_FORM_URL' => cot_url('admin', 'm=users&n=add'),
		'ADMIN_USERS_NGRP_NAME' => cot_inputbox('text', 'rname', '', 'size="40" maxlength="64"'),
		'ADMIN_USERS_NGRP_TITLE' => cot_inputbox('text', 'rtitle', '', 'size="40" maxlength="64"'),
		'ADMIN_USERS_NGRP_DESC' => cot_inputbox('text', 'rdesc', '', 'size="40" maxlength="64"'),
		'ADMIN_USERS_NGRP_ICON' => cot_inputbox('text', 'ricon', '', 'size="40" maxlength="128"'),
		'ADMIN_USERS_NGRP_ALIAS' => cot_inputbox('text', 'ralias', '', 'size="40" maxlength="24"'),
		'ADMIN_USERS_NGRP_DISABLED' => cot_radiobox(0, 'rdisabled', array(1, 0), array($L['Yes'], $L['No'])),
		'ADMIN_USERS_NGRP_MAINTENANCE' => cot_radiobox(0, 'rmtmode', array(1, 0), array($L['Yes'], $L['No'])),
		'ADMIN_USERS_NGRP_SKIPRIGHTS' => cot_radiobox(0, 'rskiprights', array(1, 0), array($L['Yes'], $L['No'])),
		'ADMIN_USERS_NGRP_RLEVEL' => cot_selectbox(50, 'rlevel', range(0, 99), range(0, 99), false),
		'ADMIN_USERS_FORM_SELECTBOX_GROUPS' => cot_selectbox_groups(4, 'rcopyrightsfrom', array('5'))
	));

	/* === Hook === */
	foreach (cot_getextplugins('admin.users.add.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.ADMIN_USERS_DEFAULT');
}

$t->assign(array(
	'ADMIN_USERS_URL' => cot_url('admin', 'm=config&n=edit&o=module&p=users'),
	'ADMIN_USERS_EXTRAFIELDS_URL' => cot_url('admin', 'm=extrafields&n='.$db_users)
));


cot_display_messages($t);

/* === Hook  === */
foreach (cot_getextplugins('admin.users.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');
