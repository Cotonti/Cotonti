<?php
/**
 * Edit User Profile
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('auth');

$y = cot_import('y','P','TXT');
$id = cot_import('id','G','INT');
$s = cot_import('s','G','ALP',13);
$w = cot_import('w','G','ALP',4);
$d = cot_import('d','G','INT');
$f = cot_import('f','G','TXT');
$g = cot_import('g','G','INT');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
cot_block($usr['isadmin']);

/* === Hook === */
foreach (cot_getextplugins('users.edit.first') as $pl)
{
	include $pl;
}
/* ===== */

$sql = $db->query("SELECT user_name, user_password, user_maingrp, user_email  FROM $db_users WHERE user_id=$id LIMIT 1");
cot_die($sql->rowCount()==0);
$urr = $sql->fetch();

$sql1 = $db->query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid=$id and gru_groupid='".COT_GROUP_SUPERADMINS."'");
$sys['edited_istopadmin'] = ($sql1->rowCount()>0) ? TRUE : FALSE;
$sys['user_istopadmin'] = cot_auth('admin', 'a', 'A');
$sys['protecttopadmin'] = $sys['edited_istopadmin'] && !$sys['user_istopadmin'];

if ($sys['protecttopadmin'])
{
	cot_redirect(cot_url('message', "msg=930", '', true));
}

if ($a=='update')
{
	cot_check_xg();
	
	$row1 = $db->query("SELECT * FROM $db_users WHERE user_id=$id")->fetch();
	
	/* === Hook === */
	foreach (cot_getextplugins('users.edit.update.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$ruserdelete = cot_import('ruserdelete','P','BOL');
	if ($ruserdelete && $sys['user_istopadmin'] && !$sys['edited_istopadmin'])
	{
		
		$sql = $db->delete($db_users, "user_id=$id");
		$sql = $db->delete($db_groups_users, "gru_userid=$id");

		foreach($cot_extrafields[$db_users] as $i => $row_extf) 
		{ 
			if ($row_extf['field_type']=='file')
			{
				 @unlink($cfg['extrafield_files_dir']."/".$row1['user_'.$row_extf['field_name']]); 
			}
		}
		
		if (cot_import('ruserdelpfs','P','BOL'))// TODO PFS SEPARATE
		{
			cot_pfs_deleteall($id);
		}

		/* === Hook === */
		foreach (cot_getextplugins('users.edit.update.delete') as $pl)
		{
			include $pl;
		}
		/* ===== */

		cot_log("Deleted user #".$id,'adm');
		cot_redirect(cot_url('message', "msg=109&rc=200&id=".$id, '', true));
	}
	elseif($ruserdelete)
	{
		cot_redirect(cot_url('message', "msg=930", '', true));
	}

	$ruser['user_name'] = cot_import('rusername','P','TXT');
	$ruser['user_maingrp'] = cot_import('rusermaingrp','P','INT');
	$ruser['user_banexpire'] = cot_import('ruserbanexpire','P','INT');
	$ruser['user_country'] = cot_import('rusercountry','P','ALP');
	$ruser['user_text'] = cot_import('rusertext','P','HTM');
	$ruser['user_email'] = cot_import('ruseremail','P','TXT');
	$ruser['user_hideemail'] = cot_import('ruserhideemail','P','INT');
	$ruser['user_theme'] = cot_import('rusertheme','P','TXT');
	$ruser['user_lang'] = cot_import('ruserlang','P','ALP');
	$ruser['user_gender'] = cot_import('rusergender','P','TXT');

	$ruser['user_birthdate'] = (int)cot_import_date('ruserbirthdate', false);

	$ruser['user_timezone'] = (float) cot_import('rusertimezone','P','TXT');
	$rusernewpass = cot_import('rusernewpass','P','TXT', 16);

	// Extra fields
	foreach($cot_extrafields[$db_users] as $row)
	{
		$ruser['user_'.$row['field_name']] = cot_import_extrafields('ruser'.$row['field_name'], $row, 'P', $row1['user_'.$row['field_name']]);
	}

	$rusergroupsms = cot_import('rusergroupsms', 'P', 'ARR');

	if (mb_strlen($ruser['user_name']) < 2 || mb_strpos($ruser['user_name'], ',') !== false || mb_strpos($ruser['user_name'], "'") !== false)
	{
		cot_error('aut_usernametooshort', 'rusername');
	}
	if (!empty($rusernewpass) && (mb_strlen($rusernewpass) < 4 || cot_alphaonly($rusernewpass) != $rusernewpass))
	{
		cot_error('aut_passwordtooshort', 'rusernewpass');
	}

	if (!cot_error_found())
	{
		$ruser['user_password'] = (mb_strlen($rusernewpass)>0) ? md5($rusernewpass) : $urr['user_password'];

		$ruser['user_name'] = ($ruser['user_name']=='') ? $urr['user_name'] : $ruser['user_name'];

		$ruser['user_birthdate'] = ($ruser['user_birthdate'] > $sys['now_offset']) ? ($sys['now_offset'] - 31536000) : $ruser['user_birthdate'];
		$ruser['user_birthdate'] = ($ruser['user_birthdate'] == '0') ? '0000-00-00' : cot_stamp2date($ruser['user_birthdate']);

		if (!$ruserbanned)
		{
			$ruser['user_banexpire'] = 0;
		}
		if ($ruserbanned && $ruser['user_banexpire']>0)
		{
			$ruser['user_banexpire'] += $sys['now'];
		}

		if ($ruser['user_name'] != $urr['user_name'])
		{
			$oldname = $db->prep($urr['user_name']);
			if (cot_module_active('forums'))
			{
				require_once cot_incfile('forums', 'module');
				$db->update($db_forum_topics, array('ft_lastpostername' => $newname), 'ft_lastpostername="'.$oldname.'"');
				$db->update($db_forum_topics, array('ft_firstpostername' => $newname), 'ft_firstpostername="'.$oldname.'"');
				$db->update($db_forum_posts, array('fp_postername' => $newname), 'fp_postername="'.$oldname.'"');
			}
			if (cot_module_active('page'))
			{
				require_once cot_incfile('page', 'module');
				$db->update($db_pages, array('page_author' => $newname), 'page_author="'.$oldname.'"');
			}
			if (cot_plugin_active('comments'))
			{
				require_once cot_incfile('comments', 'plug');
				$db->update($db_com, array('com_author' => $newname), 'com_author="'.$oldname.'"');
			}
			if (cot_module_active('pm'))
			{
				require_once cot_incfile('pm', 'module');
				$db->update($db_pm, array('pm_fromuser' => $newname), 'pm_fromuser="'.$oldname.'"');
			}
			$db->update($db_online, array('online_name' => $newname), 'online_name="'.$oldname.'"');
		}

		$ruser['user_auth'] = '';

		$sql = $db->update($db_users, $ruser, 'user_id='.$id);
		cot_extrafield_movefiles();
		
		$ruser['user_maingrp'] = ($ruser['user_maingrp'] < COT_GROUP_MEMBERS && $id==1) ? COT_GROUP_SUPERADMINS : $ruser['user_maingrp'];

		if($usr['level'] >= $cot_groups[$ruser['user_maingrp']]['level'])
		{
			if (!$rusergroupsms[$ruser['user_maingrp']])
			{
				$rusergroupsms[$ruser['user_maingrp']] = 1;
			}
			$db->update($db_users, array('user_maingrp' => $ruser['user_maingrp']), 'user_id='.$id);
		}

		foreach($cot_groups as $k => $i)
		{
			if (isset($rusergroupsms[$k]) && $usr['level'] >= $cot_groups[$k]['level'])
			{
				$sql = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid=$id AND gru_groupid=$k");
				if ($sql->rowCount() == 0 && !(($id == 1 && $k == COT_GROUP_BANNED) || ($id == 1 && $k == COT_GROUP_INACTIVE)))
				{
					$sql = $db->insert($db_groups_users, array('gru_userid' => (int)$id, 'gru_groupid' => (int)$k));
				}
			}
			elseif (!($id == 1 && $k == COT_GROUP_SUPERADMINS))
			{
				$sql = $db->delete($db_groups_users, "gru_userid=$id AND gru_groupid=$k");
			}
		}

		if ($ruser['user_maingrp'] == COT_GROUP_MEMBERS && $urr['user_maingrp'] == COT_GROUP_INACTIVE)
		{
			$rsubject = $L['useed_accountactivated'];
			$rbody = $L['Hi']." ".$urr['user_name'].",\n\n";
			$rbody .= $L['useed_email'];
			$rbody .= $L['auth_contactadmin'];
			cot_mail($urr['user_email'], $rsubject, $rbody);
		}

		/* === Hook === */
		foreach (cot_getextplugins('users.edit.update.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		cot_auth_clear($id);
		cot_log("Edited user #".$id,'adm');
		cot_redirect(cot_url('users', "m=edit&id=".$id, '', true));
	}
	else
	{
		cot_redirect(cot_url('users', "m=edit&id=$id", '', true));
	}
}

$sql = $db->query("SELECT * FROM $db_users WHERE user_id=$id LIMIT 1");
$urr = $sql->fetch();

$title_params = array(
	'EDIT' => $L['Edit'],
	'NAME' => $urr['user_name']
);
$out['subtitle'] = cot_title('{EDIT} - {NAME}', $title_params);
$out['head'] .= $R['code_noindex'];

/* === Hook === */
foreach (cot_getextplugins('users.edit.main') as $pl)
{
	include $pl;
}
/* ===== */


require_once $cfg['system_dir'] . '/header.php';

$mskin = cot_tplfile(array('users', 'edit', $usr['maingrp']), 'core');
$t = new XTemplate($mskin);

require_once cot_incfile('forms');

$protected = $sys['protecttopadmin'] ? array('disabled' => 'disabled') : array();

$editor_class = $cfg['usertextimg'] ? 'minieditor' : '';

$t->assign(array(
	'USERS_EDIT_TITLE' => cot_breadcrumbs(array(array(cot_url('users'), $L['Users']), array(cot_url('users', 'm=details&id='.$urr['user_id']), $urr['user_name']), array(cot_url('users', 'm=edit&id='.$urr['user_id']), $L['Edit'])), $cfg['homebreadcrumb']),
	'USERS_EDIT_DETAILSLINK' => cot_url('users', 'm=details&id='.$urr['user_id']),
	'USERS_EDIT_EDITLINK' => cot_url('users', 'm=edit&id='.$urr['user_id']),
	'USERS_EDIT_SUBTITLE' => $L['useed_subtitle'],
	'USERS_EDIT_SEND' => cot_url('users', 'm=edit&a=update&'.cot_xg().'&id='.$urr['user_id']),
	'USERS_EDIT_ID' => $urr['user_id'],
	'USERS_EDIT_NAME' => cot_inputbox('text', 'rusername', $urr['user_name'], array('size' => 32, 'maxlength' => 100) + $protected),
	'USERS_EDIT_ACTIVE' => $user_form_active,
	'USERS_EDIT_BANNED' => $user_form_banned,
	'USERS_EDIT_THEME' => cot_inputbox('text', 'rusertheme', $urr['user_theme'], array('size' => 32, 'maxlength' => 32)),
	'USERS_EDIT_LANG' => cot_inputbox('text', 'ruserlang', $urr['user_lang'], array('size' => 32, 'maxlength' => 32)),
	'USERS_EDIT_NEWPASS' => cot_inputbox('password', 'rusernewpass', '', array('size' => 16, 'maxlength' => 16) + $protected),
	'USERS_EDIT_MAINGRP' => cot_build_group($urr['user_maingrp']),
	'USERS_EDIT_GROUPS' => cot_build_groupsms($urr['user_id'], $usr['isadmin'], $urr['user_maingrp']),
	'USERS_EDIT_COUNTRY' => cot_selectbox_countries($urr['user_country'], 'rusercountry'),
	'USERS_EDIT_EMAIL' => cot_inputbox('text', 'ruseremail', $urr['user_email'], array('size' => 32, 'maxlength' => 64)),
	'USERS_EDIT_HIDEEMAIL' => cot_radiobox($urr['user_hideemail'], 'ruserhideemail', array(1, 0), array($L['Yes'], $L['No'])),
	'USERS_EDIT_TEXT' => cot_textarea('rusertext', $urr['user_text'], 4, 56, array('class' => $editor_class)),
	'USERS_EDIT_GENDER' => cot_selectbox_gender($urr['user_gender'], 'rusergender'),
	'USERS_EDIT_BIRTHDATE' => cot_selectbox_date(cot_date2stamp($urr['user_birthdate']), 'short', 'ruserbirthdate', cot_date('Y', $sys['now_offset']), cot_date('Y', $sys['now_offset']) - 100, false),
	'USERS_EDIT_TIMEZONE' => cot_inputbox('text', 'rusertimezone', $urr['user_timezone'], array('size' => 32, 'maxlength' => 16)),
	'USERS_EDIT_REGDATE' => cot_date('datetime_medium', $urr['user_regdate'] + $usr['timezone'] * 3600),
	'USERS_EDIT_REGDATE_STAMP' => $urr['user_regdate'] + $usr['timezone'] * 3600,
	'USERS_EDIT_LASTLOG' => cot_date('datetime_medium', $urr['user_lastlog'] + $usr['timezone'] * 3600),
	'USERS_EDIT_LASTLOG_STAMP' => $urr['user_lastlog'] + $usr['timezone'] * 3600,
	'USERS_EDIT_LOGCOUNT' => $urr['user_logcount'],
	'USERS_EDIT_LASTIP' => cot_build_ipsearch($urr['user_lastip']),
	'USERS_EDIT_DELETE' => ($sys['user_istopadmin']) ? cot_radiobox(0, 'ruserdelete', array(1, 0), array($L['Yes'], $L['No'])) . cot_checkbox(false, 'ruserdelpfs', $L['PFS']) : $L['na'],
));

// Extra fields
$extra_array = cot_build_extrafields('user', 'USERS_EDIT', $cot_extrafields[$db_users], $urr);
foreach($cot_extrafields[$db_users] as $i => $row)
{
	$t->assign('USERS_EDIT_'.strtoupper($row['field_name']), cot_build_extrafields('ruser'.$row['field_name'],  $row, $urr['user_'.$row['field_name']]));
	$t->assign('USERS_EDIT_'.strtoupper($row['field_name']).'_TITLE', isset($L['user_'.$row['field_name'].'_title']) ? $L['user_'.$row['field_name'].'_title'] : $row['field_description']);
}

// Error and message reporting
cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('users.edit.tags') as $pl)
{
	include $pl;
}
/* ===== */


$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';

?>