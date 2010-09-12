<?php
/**
 * Edit User Profile
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

sed_require_api('auth');

$y = sed_import('y','P','TXT');
$id = sed_import('id','G','INT');
$s = sed_import('s','G','ALP',13);
$w = sed_import('w','G','ALP',4);
$d = sed_import('d','G','INT');
$f = sed_import('f','G','TXT');
$g = sed_import('g','G','INT');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('users', 'a');
sed_block($usr['isadmin']);

/* === Hook === */
foreach (sed_getextplugins('users.edit.first') as $pl)
{
	include $pl;
}
/* ===== */

$sql = sed_sql_query("SELECT user_name, user_password, user_maingrp, user_email  FROM $db_users WHERE user_id='$id' LIMIT 1");
sed_die(sed_sql_numrows($sql)==0);
$urr = sed_sql_fetcharray($sql);

$sql1 = sed_sql_query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid='$id' and gru_groupid='".COT_GROUP_SUPERADMINS."'");
$sys['edited_istopadmin'] = (sed_sql_numrows($sql1)>0) ? TRUE : FALSE;
$sys['user_istopadmin'] = sed_auth('admin', 'a', 'A');
$sys['protecttopadmin'] = $sys['edited_istopadmin'] && !$sys['user_istopadmin'];

if ($sys['protecttopadmin'])
{
	sed_redirect(sed_url('message', "msg=930", '', true));
}

if ($a=='update')
{
	sed_check_xg();

	/* === Hook === */
	foreach (sed_getextplugins('users.edit.update.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$ruserdelete = sed_import('ruserdelete','P','BOL');
	if ($ruserdelete && $sys['user_istopadmin'] && !$sys['edited_istopadmin'])
	{
		if ($cfg['trash_user'])
		{
			$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$id'");
			$row = sed_sql_fetchassoc($sql);
			sed_trash_put('user', $L['User']." #".$id." ".$row['user_name'], $id, $row);
		}
		$sql = sed_sql_delete($db_users, "user_id='$id'");
		$sql = sed_sql_delete($db_groups_users, "gru_userid='$id'");

		if (sed_import('ruserdelpfs','P','BOL'))// TODO PFS SEPARATE
		{
			sed_pfs_deleteall($id);
		}

		/* === Hook === */
		foreach (sed_getextplugins('users.edit.update.delete') as $pl)
		{
			include $pl;
		}
		/* ===== */

		sed_log("Deleted user #".$id,'adm');
		sed_redirect(sed_url('message', "msg=109&rc=200&id=".$id, '', true));
	}
	elseif($ruserdelete)
	{
		sed_redirect(sed_url('message', "msg=930", '', true));
	}

	$ruser['name'] = sed_import('rusername','P','TXT');
	$ruser['maingrp'] = sed_import('rusermaingrp','P','INT');
	$ruser['banexpire'] = sed_import('ruserbanexpire','P','INT');
	$ruser['country'] = sed_import('rusercountry','P','ALP');
	$ruser['avatar'] = sed_import('ruseravatar','P','TXT');
	$ruser['photo'] = sed_import('ruserphoto','P','TXT');
	$ruser['signature'] = sed_import('rusersignature','P','TXT');
	$ruser['text'] = sed_import('rusertext','P','TXT');
	$ruser['email'] = sed_import('ruseremail','P','TXT');
	$ruser['hideemail'] = sed_import('ruserhideemail','P','INT');
	$ruser['pmnotify'] = sed_import('ruserpmnotify','P','INT');
	$ruser['skin'] = sed_import('ruserskin','P','TXT');
	$ruser['lang'] = sed_import('ruserlang','P','ALP');
	$ruser['gender'] = sed_import('rusergender','P','TXT');

	$ruser['birthdate'] = (int)sed_import_date('ruserbirthdate');

	$ruser['timezone'] = (float) sed_import('rusertimezone','P','TXT');
	$rusernewpass = sed_import('rusernewpass','P','TXT', 16);

	// Extra fields
	foreach($sed_extrafields['users'] as $row)
	{
		$ruser[$row['field_name']] = sed_import_extrafields('user', $row);
	}

	$rusergroupsms = sed_import('rusergroupsms', 'P', 'ARR');

	if (mb_strlen($ruser['name']) < 2 || mb_strpos($ruser['name'], ',') !== false || mb_strpos($ruser['name'], "'") !== false)
	{
		sed_error('aut_usernametooshort', 'rusername');
	}
	if (!empty($rusernewpass) && (mb_strlen($rusernewpass) < 4 || sed_alphaonly($rusernewpass) != $rusernewpass))
	{
		sed_error('aut_passwordtooshort', 'rusernewpass');
	}

	if (!$cot_error)
	{
		$ruser['password'] = (mb_strlen($rusernewpass)>0) ? md5($rusernewpass) : $urr['user_password'];

		$ruser['name'] = ($ruser['name']=='') ? $urr['user_name'] : $ruser['name'];

		$ruser['birthdate'] = ($ruser['birthdate'] > $sys['now_offset']) ? ($sys['now_offset'] - 31536000) : $ruser['birthdate'];
		$ruser['birthdate'] = ($ruser['birthdate'] == '0') ? '0000-00-00' : sed_stamp2date($ruser['birthdate']);

		if (!$ruserbanned)
		{
			$ruser['banexpire'] = 0;
		}
		if ($ruserbanned && $ruser['banexpire']>0)
		{
			$ruser['banexpire'] += $sys['now'];
		}

		if ($ruser['name'] != $urr['user_name'])
		{
			$oldname = sed_sql_prep($urr['user_name']);
			$newname = sed_sql_prep($ruser['name']);
			if ($cfg['module']['forums'])
			{
				sed_require('forums');
				sed_sql_update($db_forum_topics, array('ft_lastpostername' => $newname), 'ft_lastpostername="'.$oldname.'"');
				sed_sql_update($db_forum_topics, array('ft_firstpostername' => $newname), 'ft_firstpostername="'.$oldname.'"');
				sed_sql_update($db_forum_posts, array('fp_postername' => $newname), 'fp_postername="'.$oldname.'"');
			}
			if ($cfg['module']['page'])
			{
				sed_require('page');
				sed_sql_update($db_pages, array('page_author' => $newname), 'page_author="'.$oldname.'"');
			}
			if ($cfg['plugin']['comments'])
			{
				sed_require('comments', true);
				sed_sql_update($db_com, array('com_author' => $newname), 'com_author="'.$oldname.'"');
			}
			if ($cfg['module']['pm'])
			{
				sed_require('pm');
				sed_sql_update($db_pm, array('pm_fromuser' => $newname), 'pm_fromuser="'.$oldname.'"');
			}
			sed_sql_update($db_online, array('online_name' => $newname), 'online_name="'.$oldname.'"');
		}

		$ruser['auth'] = '';

		$sql = sed_sql_update($db_users, $ruser, 'user_id='.$id, 'user_');
		
		$ruser['maingrp'] = ($ruser['maingrp'] < COT_GROUP_MEMBERS && $id==1) ? COT_GROUP_SUPERADMINS : $ruser['maingrp'];

		if($usr['level'] >= $sed_groups[$ruser['maingrp']]['level'])
		{
			if (!$rusergroupsms[$ruser['maingrp']])
			{
				$rusergroupsms[$ruser['maingrp']] = 1;
			}
			sed_sql_update($db_users, array('user_maingrp' => $ruser['maingrp']), 'user_id='.$id);
		}

		foreach($sed_groups as $k => $i)
		{
			if (isset($rusergroupsms[$k]) && $usr['level'] >= $sed_groups[$k]['level'])
			{
				$sql = sed_sql_query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='$id' AND gru_groupid='$k'");
				if (sed_sql_numrows($sql) == 0 && !(($id == 1 && $k == COT_GROUP_BANNED) || ($id == 1 && $k == COT_GROUP_INACTIVE)))
				{
					$sql = sed_sql_insert($db_groups_users, array('gru_userid' => (int)$id, 'gru_groupid' => (int)$k));
				}
			}
			elseif (!($id == 1 && $k == COT_GROUP_SUPERADMINS))
			{
				$sql = sed_sql_delete($db_groups_users, "gru_userid='$id' AND gru_groupid='$k'");
			}
		}

		if ($ruser['maingrp'] == COT_GROUP_MEMBERS && $urr['user_maingrp'] == COT_GROUP_INACTIVE)
		{
			$rsubject = $cfg['maintitle']." - ".$L['useed_accountactivated'];
			$rbody = $L['Hi']." ".$urr['user_name'].",\n\n";
			$rbody .= $L['useed_email'];
			$rbody .= $L['auth_contactadmin'];
			sed_mail($urr['user_email'], $rsubject, $rbody);
		}

		/* === Hook === */
		foreach (sed_getextplugins('users.edit.update.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		sed_auth_clear($id);
		sed_log("Edited user #".$id,'adm');
		sed_redirect(sed_url('users', "m=edit&id=".$id, '', true));
	}
	else
	{
		sed_redirect(sed_url('users', "m=edit&id=$id", '', true));
	}
}

$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$id' LIMIT 1");
$urr = sed_sql_fetcharray($sql);

$title_params = array(
	'EDIT' => $L['Edit'],
	'NAME' => $urr['user_name']
);
$out['subtitle'] = sed_title('title_users_edit', $title_params);
$out['head'] .= $R['code_noindex'];

/* === Hook === */
foreach (sed_getextplugins('users.edit.main') as $pl)
{
	include $pl;
}
/* ===== */


require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('users', 'edit', $usr['maingrp']));
$t = new XTemplate($mskin);

sed_require_api('forms');

$bhome = $cfg['homebreadcrumb'] ?
	sed_rc_link($cfg['mainurl'], htmlspecialchars($cfg['maintitle'])).' '.$cfg['separator'].' ' : '';

$protected = $sys['protecttopadmin'] ? array('disabled' => 'disabled') : array();

$useredit_array = array(
	"USERS_EDIT_TITLE" => $bhome.sed_rc_link(sed_url('users'), $L['Users']).' '.$cfg['separator'].' '
		.sed_build_user($urr['user_id'], htmlspecialchars($urr['user_name'])).' '.$cfg['separator']
		.sed_rc_link(sed_url('users', 'm=edit&id='.$urr['user_id']), $L['Edit']),
	"USERS_EDIT_SUBTITLE" => $L['useed_subtitle'],
	"USERS_EDIT_SEND" => sed_url('users', 'm=edit&a=update&'.sed_xg().'&id='.$urr['user_id']),
	"USERS_EDIT_ID" => $urr['user_id'],
	"USERS_EDIT_NAME" => sed_inputbox('text', 'rusername', $urr['user_name'], array('size' => 32, 'maxlength' => 100) + $protected),
	"USERS_EDIT_ACTIVE" => $user_form_active,
	"USERS_EDIT_BANNED" => $user_form_banned,
	"USERS_EDIT_SKIN" => sed_inputbox('text', 'ruserskin', $urr['user_skin'], array('size' => 32, 'maxlength' => 32)),
	"USERS_EDIT_LANG" => sed_inputbox('text', 'ruserlang', $urr['user_lang'], array('size' => 32, 'maxlength' => 32)),
	"USERS_EDIT_NEWPASS" => sed_inputbox('password', 'rusernewpass', '', array('size' => 16, 'maxlength' => 16) + $protected),
	"USERS_EDIT_MAINGRP" => sed_build_group($urr['user_maingrp']),
	"USERS_EDIT_GROUPS" => sed_build_groupsms($urr['user_id'], $usr['isadmin'], $urr['user_maingrp']),
	"USERS_EDIT_COUNTRY" => sed_selectbox_countries($urr['user_country'], 'rusercountry'),
	"USERS_EDIT_EMAIL" => sed_inputbox('text', 'ruseremail', $urr['user_email'], array('size' => 32, 'maxlength' => 64)),
	"USERS_EDIT_HIDEEMAIL" => sed_radiobox($urr['user_hideemail'], 'ruserhideemail', array(1, 0), array($L['Yes'], $L['No'])),
	"USERS_EDIT_PMNOTIFY" => sed_radiobox($urr['user_pmnotify'], 'ruserpmnotify', array(1, 0), array($L['Yes'], $L['No'])),
	"USERS_EDIT_TEXT" => sed_textarea('rusertext', $urr['user_text'], 4, 56, '', 'input_textarea_editor'),
	"USERS_EDIT_TEXTBOXER" => sed_textarea('rusertext', $urr['user_text'], 4, 56, '', 'input_textarea_editor'),
	"USERS_EDIT_AVATAR" => sed_inputbox('text', 'ruseravatar', $urr['user_avatar'], array('size' => 32, 'maxlength' => 255)),
	"USERS_EDIT_PHOTO" => sed_inputbox('text', 'ruserphoto', $urr['user_photo'], array('size' => 32, 'maxlength' => 255)),
	"USERS_EDIT_SIGNATURE" => sed_inputbox('text', 'rusersignature', $urr['user_signature'], array('size' => 32, 'maxlength' => 255)),
	"USERS_EDIT_GENDER" => sed_selectbox_gender($urr['user_gender'], 'rusergender'),
	"USERS_EDIT_BIRTHDATE" => sed_selectbox_date(sed_date2stamp($urr['user_birthdate']), 'short', 'ruserbirthdate', date('Y', $sys['now_offset']), 1910),
	"USERS_EDIT_TIMEZONE" => sed_inputbox('text', 'rusertimezone', $urr['user_timezone'], array('size' => 32, 'maxlength' => 16)),
	"USERS_EDIT_REGDATE" => @date($cfg['dateformat'], $urr['user_regdate'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_EDIT_LASTLOG" => @date($cfg['dateformat'], $urr['user_lastlog'] + $usr['timezone']*3600)." ".$usr['timetext'],
	"USERS_EDIT_LOGCOUNT" => $urr['user_logcount'],
	"USERS_EDIT_LASTIP" => sed_build_ipsearch($urr['user_lastip']),
	"USERS_EDIT_DELETE" => ($sys['user_istopadmin']) ? sed_radiobox(0, 'ruserdelete', array(1, 0), array($L['Yes'], $L['No'])) . sed_checkbox(false, 'ruserdelpfs', $L['PFS']) : $L['na'],
);
$t->assign($useredit_array);

// Extra fields
$extra_array = sed_build_extrafields('user', 'USERS_EDIT', $sed_extrafields['users'], $urr);
foreach($sed_extrafields['users'] as $i => $row)
{
	$t->assign('USERS_EDIT_'.strtoupper($row['field_name']), sed_build_extrafields('user',  $row, $urr['user_'.$row['field_name']]));
	$t->assign('USERS_EDIT_'.strtoupper($row['field_name']).'_TITLE', isset($L['user_'.$row['field_name'].'_title']) ? $L['user_'.$row['field_name'].'_title'] : $row['field_description']);
}

// Error and message reporting
if (sed_check_messages())
{
	$t->assign('USERS_EDIT_ERROR_BODY', sed_implode_messages());
	$t->parse('MAIN.USERS_EDIT_ERROR');
	sed_clear_messages();
}


/* === Hook === */
foreach (sed_getextplugins('users.edit.tags') as $pl)
{
	include $pl;
}
/* ===== */


$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>