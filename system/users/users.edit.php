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
$extp = sed_getextplugins('users.edit.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$id' LIMIT 1");
sed_die(sed_sql_numrows($sql)==0);
$urr = sed_sql_fetcharray($sql);

$urr['user_birthdate'] = sed_date2stamp($urr['user_birthdate']);

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
	$extp = sed_getextplugins('users.edit.update.first');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$rusername = sed_import('rusername','P','TXT');
	$rusermaingrp = sed_import('rusermaingrp','P','INT');
	$ruserbanexpire = sed_import('ruserbanexpire','P','INT');
	$rusercountry = sed_import('rusercountry','P','ALP');
	$ruseravatar = sed_import('ruseravatar','P','TXT');
	$ruserphoto = sed_import('ruserphoto','P','TXT');
	$rusersignature = sed_import('rusersignature','P','TXT');
	$rusertext = sed_import('rusertext','P','TXT');
	$ruseremail = sed_import('ruseremail','P','TXT');
	$ruserhideemail = sed_import('ruserhideemail','P','INT');
	$ruserpmnotify = sed_import('ruserpmnotify','P','INT');
	$ruserskin = sed_import('ruserskin','P','TXT');
	$ruserlang = sed_import('ruserlang','P','ALP');
	$ruserwebsite = sed_import('ruserwebsite','P','TXT');
	$rusericq = sed_import('rusericq','P','TXT');
	$ruserirc = sed_import('ruserirc','P','TXT');
	$rusermsn = sed_import('rusermsn','P','TXT');
	$rusergender = sed_import('rusergender','P','TXT');
	$ryear = sed_import('ryear','P','INT');
	$rmonth = sed_import('rmonth','P','INT');
	$rday = sed_import('rday','P','INT');
	$rhour = sed_import('rhour','P','INT');
	$rminute = sed_import('rminute','P','INT');
	$rusertimezone = (float) sed_import('rusertimezone','P','TXT');
	$ruserlocation = sed_import('ruserlocation','P','TXT');
	$ruseroccupation = sed_import('ruseroccupation','P','TXT');
	$ruserdelete = sed_import('ruserdelete','P','BOL');
	$ruserdelpfs = sed_import('ruserdelpfs','P','BOL');
	$rusernewpass = sed_import('rusernewpass','P','TXT', 16);
	$rusergroupsms = sed_import('rusergroupsms', 'P', 'ARR');

	if (mb_strlen($rusername) < 2 || mb_strpos($rusername, ',') !== false || mb_strpos($rusername, "'") !== false)
		sed_error('aut_usernametooshort', 'rusername');
	if (!empty($rusernewpass) && (mb_strlen($rusernewpass) < 4 || sed_alphaonly($rusernewpass) != $rusernewpass))
		sed_error('aut_passwordtooshort', 'rusernewpass');

	// Extra fields
	foreach($sed_extrafields['users'] as $row)
	{
		$import = sed_import('ruser'.$row['field_name'],'P','HTM');
		if($row['field_type'] == 'checkbox' && !is_null($import))
		{
			$import = $import != '';
		}
		$ruserextrafields[$row['field_name']] = $import;
		$urr['user_'.$row[ 'field_name']] = $import;
	}
	
	if ($ruserdelete)
	{
		if ($sys['user_istopadmin'] && !$sys['edited_istopadmin'])
		{
			$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$id'");

			if ($row = sed_sql_fetchassoc($sql))
			{
				if ($cfg['trash_user'])
				{ sed_trash_put('user', $L['User']." #".$id." ".$row['user_name'], $id, $row); }
				$sql = sed_sql_query("DELETE FROM $db_users WHERE user_id='$id'");
				$sql = sed_sql_query("DELETE FROM $db_groups_users WHERE gru_userid='$id'");
				if ($ruserdelpfs) { sed_pfs_deleteall($id); }
				sed_log("Deleted user #".$id,'adm');
				sed_redirect(sed_url('message', "msg=109&rc=200&id=".$id, '', true));
			}
		}
		else
		{
			sed_redirect(sed_url('message', "msg=930", '', true));
		}
	}

	if (!$cot_error)
	{
		$ruserpassword = (mb_strlen($rusernewpass)>0) ? md5($rusernewpass) : $urr['user_password'];

		if ($rusername=='')
		{ $rusername = $urr['user_name']; }
		if ($ruserhideemail=='')
		{ $ruserhideemail = $urr['user_hideemail']; }
		if ($ruserpmnotify=='')
		{ $ruserpmnotify = $urr['user_pmnotify']; }

		if ($rmonth=='x' || $rday=='x' || $ryear=='x' || empty($rmonth) || empty($rday) || empty($ryear))
		{
			$ruserbirthdate = '0000-00-00';
		}
		else
		{
			$bdate = sed_mktime(1, 0, 0, $rmonth, $rday, $ryear);
			if ($bdate > $sys['now_offset'])
			{
				$bdate = sed_mktime(1, 0, 0, $rmonth, $rday, date('Y', $sys['now_offset']) - 1);
			}
			$ruserbirthdate = sed_stamp2date($bdate);
		}

		if (!$ruserbanned)
		{ $rbanexpire = 0; }
		if ($ruserbanned && $rbanexpire>0)
		{ $rbanexpire += $sys['now']; }

		if ($rusername!=$urr['user_name'])
		{
			$oldname = sed_sql_prep($urr['user_name']);
			$newname = sed_sql_prep($rusername);
			if ($cfg['module']['forums'])
			{
				sed_require('forums');
				sed_sql_query("UPDATE $db_forum_topics SET ft_lastpostername='$newname' WHERE ft_lastpostername='$oldname'");
				sed_sql_query("UPDATE $db_forum_topics SET ft_firstpostername='$newname' WHERE ft_firstpostername='$oldname'");
				sed_sql_query("UPDATE $db_forum_posts SET fp_postername='$newname' WHERE fp_postername='$oldname'");
			}
			if ($cfg['module']['page'])
			{
				sed_require('page');
				sed_sql_query("UPDATE $db_pages SET page_author='$newname' WHERE page_author='$oldname'");
			}
			if ($cfg['plugin']['comments'])
			{
				sed_require('comments', true);
				sed_sql_query("UPDATE $db_com SET com_author='$newname' WHERE com_author='$oldname'");
			}
			if ($cfg['module']['pm'])
			{
				sed_require('pm');
				sed_sql_query("UPDATE $db_pm SET pm_fromuser='$newname' WHERE pm_fromuser='$oldname'");
			}
			sed_sql_query("UPDATE $db_online SET online_name='$newname' WHERE online_name='$oldname'");
		}
		// Extra fields
		foreach($sed_extrafields['users'] as $i=>$row)
		{
			if(!is_null($ruserextrafields[$i]))
			{
				$ssql_extra .= "user_".$row['field_name']." = '".sed_sql_prep($ruserextrafields[$i])."',";
			}
		}

		$ssql = "UPDATE $db_users SET
			user_banexpire='$rbanexpire',
			user_name='".sed_sql_prep($rusername)."',
			user_password='".sed_sql_prep($ruserpassword)."',
			user_country='".sed_sql_prep($rusercountry)."',
			user_text='".sed_sql_prep($rusertext)."',
			user_avatar='".sed_sql_prep($ruseravatar)."',
			user_signature='".sed_sql_prep($rusersignature)."',
			user_photo='".sed_sql_prep($ruserphoto)."',
			user_email='".sed_sql_prep($ruseremail)."',
			user_hideemail='$ruserhideemail',
			user_pmnotify='$ruserpmnotify',
			user_skin='".sed_sql_prep($ruserskin)."',
			user_lang='".sed_sql_prep($ruserlang)."',
			user_website='".sed_sql_prep($ruserwebsite)."',
			user_icq='".sed_sql_prep($rusericq)."',
			user_msn='".sed_sql_prep($rusermsn)."',
			user_irc='".sed_sql_prep($ruserirc)."',
			user_gender='".sed_sql_prep($rusergender)."',
			user_birthdate='".sed_sql_prep($ruserbirthdate)."',
			user_timezone='".sed_sql_prep($rusertimezone)."',
			user_location='".sed_sql_prep($ruserlocation)."',
			user_occupation='".sed_sql_prep($ruseroccupation)."',
			".$ssql_extra."
			user_auth=''
			WHERE user_id='$id'";
		$sql = sed_sql_query($ssql);

		$rusermaingrp = ($rusermaingrp < COT_GROUP_MEMBERS && $id==1) ? COT_GROUP_SUPERADMINS : $rusermaingrp;

		if($usr['level'] >= $sed_groups[$rusermaingrp]['level'])
		{
			if (!$rusergroupsms[$rusermaingrp])
			{
				$rusergroupsms[$rusermaingrp] = 1;
			}
			$sql = sed_sql_query("UPDATE $db_users SET user_maingrp='$rusermaingrp' WHERE user_id='$id'");
		}

		foreach($sed_groups as $k => $i)
		{
			if (isset($rusergroupsms[$k]) && $usr['level'] >= $sed_groups[$k]['level'])
			{
				$sql = sed_sql_query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='$id' AND gru_groupid='$k'");
				if (sed_sql_numrows($sql)==0 && !(($id==1 && $k==COT_GROUP_BANNED) || ($id==1 && $k==COT_GROUP_INACTIVE)))
				{ $sql = sed_sql_query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (".(int)$id.", ".(int)$k.")"); }
			}
			elseif (!($id==1 && $k==COT_GROUP_SUPERADMINS))
			{ $sql = sed_sql_query("DELETE FROM $db_groups_users WHERE gru_userid='$id' AND gru_groupid='$k'"); }
		}

		if ($rusermaingrp==COT_GROUP_MEMBERS && $urr['user_maingrp']==COT_GROUP_INACTIVE)
		{
			$rsubject = $cfg['maintitle']." - ".$L['useed_accountactivated'];
			$rbody = $L['Hi']." ".$urr['user_name'].",\n\n";
			$rbody .= $L['useed_email'];
			$rbody .= $L['auth_contactadmin'];
			sed_mail($urr['user_email'], $rsubject, $rbody);
		}

		/* === Hook === */
		$extp = sed_getextplugins('users.edit.update.done');
		foreach ($extp as $pl)
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

$title_params = array(
	'EDIT' => $L['Edit'],
	'NAME' => $urr['user_name']
);
$out['subtitle'] = sed_title('title_users_edit', $title_params);
$out['head'] .= $R['code_noindex'];

/* === Hook === */
$extp = sed_getextplugins('users.edit.main');
foreach ($extp as $pl)
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

$user_form_delete = ($sys['user_istopadmin']) ? sed_radiobox(0, 'ruserdelete', array(1, 0), array($L['Yes'], $L['No']))
	. sed_checkbox(false, 'ruserdelpfs', $L['PFS']) : $L['na'];
$user_form_hideemail = sed_radiobox($urr['user_hideemail'], 'ruserhideemail', array(1, 0), array($L['Yes'], $L['No']));
$user_form_pmnotify = sed_radiobox($urr['user_pmnotify'], 'ruserpmnotify', array(1, 0), array($L['Yes'], $L['No']));
$protected = $sys['protecttopadmin'] ? array('disabled' => 'disabled') : array();
$user_form_pass = sed_inputbox('password', 'rusernewpass', '', array('size' => 16, 'maxlength' => 16) + $protected);
$user_form_username = sed_inputbox('text', 'rusername', $urr['user_name'], array('size' => 32, 'maxlength' => 100)
	+ $protected);

$user_form_countries = sed_selectbox_countries($urr['user_country'], 'rusercountry');
$user_form_gender = sed_selectbox_gender($urr['user_gender'], 'rusergender');
$user_form_birthdate = sed_selectbox_date($urr['user_birthdate'], 'short', '', date('Y', $sys['now_offset']));
$urr['user_lastip'] = sed_build_ipsearch($urr['user_lastip']);

$useredit_array = array(
	"USERS_EDIT_TITLE" => $bhome.sed_rc_link(sed_url('users'), $L['Users']).' '.$cfg['separator'].' '
		.sed_build_user($urr['user_id'], htmlspecialchars($urr['user_name'])).' '.$cfg['separator']
		.sed_rc_link(sed_url('users', 'm=edit&id='.$urr['user_id']), $L['Edit']),
	"USERS_EDIT_SUBTITLE" => $L['useed_subtitle'],
	"USERS_EDIT_SEND" => sed_url('users', 'm=edit&a=update&'.sed_xg().'&id='.$urr['user_id']),
	"USERS_EDIT_ID" => $urr['user_id'],
	"USERS_EDIT_NAME" => $user_form_username,
	"USERS_EDIT_ACTIVE" => $user_form_active,
	"USERS_EDIT_BANNED" => $user_form_banned,
	"USERS_EDIT_SKIN" => sed_inputbox('text', 'ruserskin', $urr['user_skin'], array('size' => 32, 'maxlength' => 32)),
	"USERS_EDIT_LANG" => sed_inputbox('text', 'ruserlang', $urr['user_lang'], array('size' => 32, 'maxlength' => 32)),
	"USERS_EDIT_NEWPASS" => $user_form_pass,
	"USERS_EDIT_MAINGRP" => sed_build_group($urr['user_maingrp']),
	"USERS_EDIT_GROUPS" => sed_build_groupsms($urr['user_id'], $usr['isadmin'], $urr['user_maingrp']),
	"USERS_EDIT_COUNTRY" => $user_form_countries,
	"USERS_EDIT_EMAIL" => sed_inputbox('text', 'ruseremail', $urr['user_email'], array('size' => 32, 'maxlength' => 64)),
	"USERS_EDIT_HIDEEMAIL" => $user_form_hideemail,
	"USERS_EDIT_PMNOTIFY" => $user_form_pmnotify,
	"USERS_EDIT_TEXT" => sed_textarea('rusertext', $urr['user_text'], 4, 56, '', 'input_textarea_editor'),
	"USERS_EDIT_TEXTBOXER" => sed_textarea('rusertext', $urr['user_text'], 4, 56, '', 'input_textarea_editor'),
	"USERS_EDIT_AVATAR" => sed_inputbox('text', 'ruseravatar', $urr['user_avatar'], array('size' => 32, 'maxlength' => 255)),
	"USERS_EDIT_PHOTO" => sed_inputbox('text', 'ruserphoto', $urr['user_photo'], array('size' => 32, 'maxlength' => 255)),
	"USERS_EDIT_SIGNATURE" => sed_inputbox('text', 'rusersignature', $urr['user_signature'], array('size' => 32, 'maxlength' => 255)),
	"USERS_EDIT_WEBSITE" => sed_inputbox('text', 'ruserwebsite', $urr['user_website'], array('size' => 56, 'maxlength' => 128)),
	"USERS_EDIT_ICQ" => sed_inputbox('text', 'rusericq', $urr['user_icq'], array('size' => 32, 'maxlength' => 16)),
	"USERS_EDIT_MSN" => sed_inputbox('text', 'rusermsn', $urr['user_msn'], array('size' => 32, 'maxlength' => 64)),
	"USERS_EDIT_IRC" => sed_inputbox('text', 'ruserirc', $urr['user_irc'], array('size' => 56, 'maxlength' => 255)),
	"USERS_EDIT_GENDER" => $user_form_gender,
	"USERS_EDIT_BIRTHDATE" => $user_form_birthdate,
	"USERS_EDIT_TIMEZONE" => sed_inputbox('text', 'rusertimezone', $urr['user_timezone'], array('size' => 32, 'maxlength' => 16)),
	"USERS_EDIT_LOCATION" => sed_inputbox('text', 'ruserlocation', $urr['user_location'], array('size' => 32, 'maxlength' => 64)),
	"USERS_EDIT_OCCUPATION" => sed_inputbox('text', 'ruseroccupation', $urr['user_occupation'], array('size' => 32, 'maxlength' => 64)),
	"USERS_EDIT_REGDATE" => @date($cfg['dateformat'], $urr['user_regdate'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_EDIT_LASTLOG" => @date($cfg['dateformat'], $urr['user_lastlog'] + $usr['timezone']*3600)." ".$usr['timetext'],
	"USERS_EDIT_LOGCOUNT" => $urr['user_logcount'],
	"USERS_EDIT_LASTIP" => $urr['user_lastip'],
	"USERS_EDIT_DELETE" => $user_form_delete,
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
$extp = sed_getextplugins('users.edit.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */


$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>