<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=users.php
Version=122
Updated=2007-oct-10
Type=Core
Author=Neocrome
Description=Users
[END_SED]
==================== */

/**
 * @package Seditio-N
 * @version 0.0.1
 * @copyright Partial copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

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
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$sql = sed_sql_query("SELECT * FROM $db_users WHERE user_id='$id' LIMIT 1");
sed_die(sed_sql_numrows($sql)==0);
$urr = sed_sql_fetcharray($sql);

$sql1 = sed_sql_query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid='$id' and gru_groupid='".SED_GROUP_TOPADMINS."'");
$sys['edited_istopadmin'] = (sed_sql_numrows($sql1)>0) ? TRUE : FALSE;
$sys['user_istopadmin'] = sed_auth('admin', 'a', 'A');
$sys['protecttopadmin'] = $sys['edited_istopadmin'] && !$sys['user_istopadmin'];

if ($sys['protecttopadmin'])
{
	header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=930", '', true));
	exit;
}

// Extra fields - getting
$extrafields = array();
$fieldsres = sed_sql_query("SELECT * FROM $db_extra_fields WHERE field_location='users'");
while($row = sed_sql_fetchassoc($fieldsres)) $extrafields[] = $row;

if ($a=='update')
{
	sed_check_xg();

	/* === Hook === */
	$extp = sed_getextplugins('users.edit.update.first');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
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
	$rusertimezone = sed_import('rusertimezone','P','TXT');
	$ruserlocation = sed_import('ruserlocation','P','TXT');
	$ruseroccupation = sed_import('ruseroccupation','P','TXT');
	$ruserdelete = sed_import('ruserdelete','P','BOL');
	$ruserdelpfs = sed_import('ruserdelpfs','P','BOL');
	$ruserextra1 = sed_import('ruserextra1','P','TXT');
	$ruserextra2 = sed_import('ruserextra2','P','TXT');
	$ruserextra3 = sed_import('ruserextra3','P','TXT');
	$ruserextra4 = sed_import('ruserextra4','P','TXT');
	$ruserextra5 = sed_import('ruserextra5','P','TXT');
	$ruserextra6 = sed_import('ruserextra6','P','HTM');
	$ruserextra7 = sed_import('ruserextra7','P','HTM');
	$ruserextra8 = sed_import('ruserextra8','P','HTM');
	$ruserextra9 = sed_import('ruserextra9','P','HTM');
	$ruserextra1_p = sed_import('ruserextra1_p','P','BOL');
	$ruserextra2_p = sed_import('ruserextra2_p','P','BOL');
	$ruserextra3_p = sed_import('ruserextra3_p','P','BOL');
	$ruserextra4_p = sed_import('ruserextra4_p','P','BOL');
	$ruserextra5_p = sed_import('ruserextra5_p','P','BOL');
	$ruserextra6_p = sed_import('ruserextra6_p','P','BOL');
	$ruserextra7_p = sed_import('ruserextra7_p','P','BOL');
	$ruserextra8_p = sed_import('ruserextra8_p','P','BOL');
	$ruserextra9_p = sed_import('ruserextra9_p','P','BOL');
	$rusernewpass = sed_import('rusernewpass','P','TXT', 16);
	$rusergroupsms = sed_import('rusergroupsms', 'P', 'ARR');

	$error_string .= (mb_strlen($rusername)<2 || mb_ereg(",", $rusername) || mb_ereg("'", $rusername)) ? $L['aut_usernametooshort']."<br />" : '';
	$error_string .= (!empty($rusernewpass) && (mb_strlen($rusernewpass)<4 || sed_alphaonly($rusernewpass)!=$rusernewpass)) ? $L['aut_passwordtooshort']."<br />" : '';

	// Extra fields
	if(count($extrafields)>0)
	foreach($extrafields as $row)
	{
		$import = sed_import('ruser'.$row['field_name'],'P','HTM');
		if($row['field_type']=="checkbox")
		{
			if ($import == "0") $import = 1;
			if ($import == "") $import = 0;
		}
		$ruserextrafields[] = $import;
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
				header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=109&rc=200&id=".$id, '', true));
				exit;
			}
		}
		else
		{
			header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=930", '', true));
			exit;
		}
	}

	if (empty($error_string))
	{
		$ruserpassword = (mb_strlen($rusernewpass)>0) ? md5($rusernewpass) : $urr['user_password'];

		if ($rusername=='')
		{ $rusername = $urr['user_name']; }
		if ($ruserhideemail=='')
		{ $ruserhideemail = $urr['user_hideemail']; }
		if ($ruserpmnotify=='')
		{ $ruserpmnotify = $urr['user_pmnotify']; }

		$ruserextra1 = ($ruserextra1_p) ? mb_substr($ruserextra1,0,$cfg['extra1tsetting']) : $urr['user_extra1'];
		$ruserextra2 = ($ruserextra2_p) ? mb_substr($ruserextra2,0,$cfg['extra2tsetting']) : $urr['user_extra2'];
		$ruserextra3 = ($ruserextra3_p) ? mb_substr($ruserextra3,0,$cfg['extra3tsetting']) : $urr['user_extra3'];
		$ruserextra4 = ($ruserextra4_p) ? mb_substr($ruserextra4,0,$cfg['extra4tsetting']) : $urr['user_extra4'];
		$ruserextra5 = ($ruserextra5_p) ? mb_substr($ruserextra5,0,$cfg['extra5tsetting']) : $urr['user_extra5'];
		$ruserextra6 = ($ruserextra6_p) ? $ruserextra6 : $urr['user_extra6'];
		$ruserextra7 = ($ruserextra7_p) ? $ruserextra7 : $urr['user_extra7'];
		$ruserextra8 = ($ruserextra8_p) ? $ruserextra8 : $urr['user_extra8'];
		$ruserextra9 = ($ruserextra9_p) ? $ruserextra9 : $urr['user_extra9'];

		$ruserbirthdate = ($rmonth==0 || $rday ==0 || $ryear==0) ? 0 : sed_mktime(1, 0, 0, $rmonth, $rday, $ryear);

		if (!$ruserbanned)
		{ $rbanexpire = 0; }
		if ($ruserbanned && $rbanexpire>0)
		{ $rbanexpire += $sys['now']; }

		if ($rusername!=$urr['user_name'])
		{
			$oldname = sed_sql_prep($urr['user_name']);
			$newname = sed_sql_prep($rusername);
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_lastpostername='$newname' WHERE ft_lastpostername='$oldname'");
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_firstpostername='$newname' WHERE ft_firstpostername='$oldname'");
			$sql = sed_sql_query("UPDATE $db_forum_posts SET fp_postername='$newname' WHERE fp_postername='$oldname'");
			$sql = sed_sql_query("UPDATE $db_pages SET page_author='$newname' WHERE page_author='$oldname'");
			$sql = sed_sql_query("UPDATE $db_com SET com_author='$newname' WHERE com_author='$oldname'");
			$sql = sed_sql_query("UPDATE $db_online SET online_name='$newname' WHERE online_name='$oldname'");
			$sql = sed_sql_query("UPDATE $db_pm SET pm_fromuser='$newname' WHERE pm_fromuser='$oldname'");
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
			user_extra1='".sed_sql_prep($ruserextra1)."',
			user_extra2='".sed_sql_prep($ruserextra2)."',
			user_extra3='".sed_sql_prep($ruserextra3)."',
			user_extra4='".sed_sql_prep($ruserextra4)."',
			user_extra5='".sed_sql_prep($ruserextra5)."',
			user_extra6='".sed_sql_prep($ruserextra6)."',
			user_extra7='".sed_sql_prep($ruserextra7)."',
			user_extra8='".sed_sql_prep($ruserextra8)."',
			user_extra9='".sed_sql_prep($ruserextra9)."',
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
			";
		if(count($extrafields)>0) foreach($extrafields as $i=>$row) $ssql .= "user_".$row['field_name']." = '".sed_sql_prep($ruserextrafields[$i])."',"; // Extra fields
		$ssql .= " user_auth='' WHERE user_id='$id'";
		$sql = sed_sql_query($ssql);

		$rusermaingrp = ($rusermaingrp < SED_GROUP_MEMBERS && $id==1) ? SED_GROUP_TOPADMINS : $rusermaingrp;

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
				if (sed_sql_numrows($sql)==0 && !(($id==1 && $k==SED_GROUP_BANNED) || ($id==1 && $k==SED_GROUP_INACTIVE)))
				{ $sql = sed_sql_query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (".(int)$id.", ".(int)$k.")"); }
			}
			elseif (!($id==1 && $k==SED_GROUP_TOPADMINS))
			{ $sql = sed_sql_query("DELETE FROM $db_groups_users WHERE gru_userid='$id' AND gru_groupid='$k'"); }
		}

		if ($rusermaingrp==SED_GROUP_MEMBERS && $urr['user_maingrp']==SED_GROUP_INACTIVE)
		{
			$rsubject = $cfg['maintitle']." - ".$L['useed_accountactivated'];
			$rbody = $L['Hi']." ".$urr['user_name'].",\n\n";
			$rbody .= $L['useed_email'];
			$rbody .= $L['auth_contactadmin'];
			sed_mail($urr['user_email'], $rsubject, $rbody);
		}

		/* === Hook === */
		$extp = sed_getextplugins('users.edit.update.done');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		sed_auth_clear($id);
		sed_log("Edited user #".$id,'adm');
		header("Location: " . SED_ABSOLUTE_URL . sed_url('users', "m=edit&id=".$id, '', true));
		exit;
	}
}

$user_form_delete = ($sys['user_istopadmin']) ? "<input type=\"radio\" class=\"radio\" name=\"ruserdelete\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserdelete\" value=\"0\" checked=\"checked\" />".$L['No']."<br />+ ".$L['PFS'].":<input type=\"checkbox\" class=\"checkbox\" name=\"ruserdelpfs\" />" : $L['na'];

$user_form_hideemail = ($urr['user_hideemail']) ? "<input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"0\" checked=\"checked\" />".$L['No'];

$user_form_pmnotify = ($urr['user_pmnotify']) ? "<input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"0\" checked=\"checked\" />".$L['No'];

$user_form_pass = $sys['protecttopadmin'] ? "<input type=\"text\" class=\"text\" name=\"rusernewpass\" value=\"\" size=\"16\" maxlength=\"16\" disabled=\"disabled\" />" : "<input type=\"text\" class=\"text\" name=\"rusernewpass\" value=\"\" size=\"16\" maxlength=\"16\" />";

$user_form_username = $sys['protecttopadmin'] ? "<input type=\"text\" class=\"text\" name=\"rusername\" value=\"".sed_cc($urr['user_name'])."\" size=\"32\" maxlength=\"24\"  disabled=\"disabled\" />" : "<input type=\"text\" class=\"text\" name=\"rusername\" value=\"".sed_cc($urr['user_name'])."\" size=\"32\" maxlength=\"24\" />";

$user_form_countries = sed_selectbox_countries($urr['user_country'], 'rusercountry');
$user_form_gender = sed_selectbox_gender($urr['user_gender'], 'rusergender');
$user_form_birthdate = sed_selectbox_date($urr['user_birthdate'], 'short');
$urr['user_lastip'] = sed_build_ipsearch($urr['user_lastip']);

$useredit_form_extra1 = "<input type=\"text\" class=\"text\" name=\"ruserextra1\" value=\"".sed_cc($urr['user_extra1'])."\" size=\"32\" maxlength=\"".$cfg['extra1tsetting']."\" /><input type=\"hidden\" name=\"ruserextra1_p\" value=\"1\" />";
$useredit_form_extra2 = "<input type=\"text\" class=\"text\" name=\"ruserextra2\" value=\"".sed_cc($urr['user_extra2'])."\" size=\"32\" maxlength=\"".$cfg['extra2tsetting']."\" /><input type=\"hidden\" name=\"ruserextra2_p\" value=\"1\" />";
$useredit_form_extra3 = "<input type=\"text\" class=\"text\" name=\"ruserextra3\" value=\"".sed_cc($urr['user_extra3'])."\" size=\"32\" maxlength=\"".$cfg['extra3tsetting']."\" /><input type=\"hidden\" name=\"ruserextra3_p\" value=\"1\" />";
$useredit_form_extra4 = "<input type=\"text\" class=\"text\" name=\"ruserextra4\" value=\"".sed_cc($urr['user_extra4'])."\" size=\"32\" maxlength=\"".$cfg['extra4tsetting']."\" /><input type=\"hidden\" name=\"ruserextra4_p\" value=\"1\" />";
$useredit_form_extra5 = "<input type=\"text\" class=\"text\" name=\"ruserextra5\" value=\"".sed_cc($urr['user_extra5'])."\" size=\"32\" maxlength=\"".$cfg['extra5tsetting']."\" /><input type=\"hidden\" name=\"ruserextra5_p\" value=\"1\" />";
$useredit_form_extra6 = sed_selectbox($urr['user_extra6'], 'ruserextra6', $cfg['extra6tsetting'])."<input type=\"hidden\" name=\"ruserextra6_p\" value=\"1\" />";
$useredit_form_extra7 = sed_selectbox($urr['user_extra7'], 'ruserextra7', $cfg['extra7tsetting'])."<input type=\"hidden\" name=\"ruserextra7_p\" value=\"1\" />";
$useredit_form_extra8 = sed_selectbox($urr['user_extra8'], 'ruserextra8', $cfg['extra8tsetting'])."<input type=\"hidden\" name=\"ruserextra8_p\" value=\"1\" />";
$useredit_form_extra9 = "<textarea name=\"ruserextra9\" rows=\"4\" cols=\"56\">".sed_cc($urr['user_extra9'])."</textarea><input type=\"hidden\" name=\"ruserextra9_p\" value=\"1\" />";

$title_tags[] = array('{EDIT}', '{NAME}');
$title_tags[] = array('%1$s', '%2$s');
$title_data = array($L['Edit'], sed_cc($urr['user_name']));
$out['subtitle'] = sed_title('title_users_edit', $title_tags, $title_data);

/* === Hook === */
$extp = sed_getextplugins('users.edit.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */


require_once $cfg['system_dir'] . '/header.php';

$mskin = sed_skinfile(array('users', 'edit', $usr['maingrp']));
$t = new XTemplate($mskin);

if (!empty($error_string))
{
	$t->assign("USERS_EDIT_ERROR_BODY",$error_string);
	$t->parse("MAIN.USERS_EDIT_ERROR");
}

$bhome = $cfg['homebreadcrumb'] ? '<a href="'.$cfg['mainurl'].'">'.sed_cc($cfg['maintitle']).'</a> '.$cfg['separator'].' ' : '';

$useredit_array = array(
	"USERS_EDIT_TITLE" => $bhome . "<a href=\"".sed_url('users')."\">".$L['Users']."</a> ".$cfg['separator']." ".sed_build_user($urr['user_id'], sed_cc($urr['user_name']))." ".$cfg['separator']." <a href=\"users.php?m=edit&amp;id=".$urr['user_id']."\">".$L['Edit']."</a>",
	"USERS_EDIT_SUBTITLE" => $L['useed_subtitle'],
	"USERS_EDIT_SEND" => sed_url('users', 'm=edit&a=update&'.sed_xg().'&id='.$urr['user_id']),
	"USERS_EDIT_ID" => $urr['user_id'],
	"USERS_EDIT_NAME" => $user_form_username,
	"USERS_EDIT_ACTIVE" => $user_form_active,
	"USERS_EDIT_BANNED" => $user_form_banned,
	"USERS_EDIT_SKIN" => "<input type=\"text\" class=\"text\" name=\"ruserskin\" value=\"".$urr['user_skin']."\" size=\"32\" maxlength=\"16\" />",
	"USERS_EDIT_LANG" => "<input type=\"text\" class=\"text\" name=\"ruserlang\" value=\"".$urr['user_lang']."\" size=\"32\" maxlength=\"16\" />",
	"USERS_EDIT_NEWPASS" => $user_form_pass,
	"USERS_EDIT_MAINGRP" => sed_build_group($urr['user_maingrp']),
	"USERS_EDIT_GROUPS" => sed_build_groupsms($urr['user_id'], $usr['isadmin'], $urr['user_maingrp']),
	"USERS_EDIT_COUNTRY" => $user_form_countries,
	"USERS_EDIT_EMAIL" => "<input type=\"text\" class=\"text\" name=\"ruseremail\" value=\"".sed_cc($urr['user_email'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_EDIT_HIDEEMAIL" => $user_form_hideemail,
	"USERS_EDIT_PMNOTIFY" => $user_form_pmnotify,
	"USERS_EDIT_TEXT" => "<textarea class=\"editor\" name=\"rusertext\" rows=\"4\" cols=\"56\">".sed_cc($urr['user_text'])."</textarea>",
	"USERS_EDIT_TEXTBOXER" => "<textarea class=\"editor\" name=\"rusertext\" rows=\"4\" cols=\"56\">".sed_cc($urr['user_text'])."</textarea>",
	"USERS_EDIT_AVATAR" => "<input type=\"text\" class=\"text\" name=\"ruseravatar\" value=\"".sed_cc($urr['user_avatar'])."\" size=\"32\" maxlength=\"255\" />",
	"USERS_EDIT_PHOTO" => "<input type=\"text\" class=\"text\" name=\"ruserphoto\" value=\"".sed_cc($urr['user_photo'])."\" size=\"32\" maxlength=\"255\" />",
	"USERS_EDIT_SIGNATURE" => "<input type=\"text\" class=\"text\" name=\"rusersignature\" value=\"".sed_cc($urr['user_signature'])."\" size=\"32\" maxlength=\"255\" />",
	"USERS_EDIT_EXTRA1" => $useredit_form_extra1,
	"USERS_EDIT_EXTRA2" => $useredit_form_extra2,
	"USERS_EDIT_EXTRA3" => $useredit_form_extra3,
	"USERS_EDIT_EXTRA4" => $useredit_form_extra4,
	"USERS_EDIT_EXTRA5" => $useredit_form_extra5,
	"USERS_EDIT_EXTRA6" => $useredit_form_extra6,
	"USERS_EDIT_EXTRA7" => $useredit_form_extra7,
	"USERS_EDIT_EXTRA8" => $useredit_form_extra8,
	"USERS_EDIT_EXTRA9" => $useredit_form_extra9,
	"USERS_EDIT_EXTRA1_TITLE" => $cfg['extra1title'],
	"USERS_EDIT_EXTRA2_TITLE" => $cfg['extra2title'],
	"USERS_EDIT_EXTRA3_TITLE" => $cfg['extra3title'],
	"USERS_EDIT_EXTRA4_TITLE" => $cfg['extra4title'],
	"USERS_EDIT_EXTRA5_TITLE" => $cfg['extra5title'],
	"USERS_EDIT_EXTRA6_TITLE" => $cfg['extra6title'],
	"USERS_EDIT_EXTRA7_TITLE" => $cfg['extra7title'],
	"USERS_EDIT_EXTRA8_TITLE" => $cfg['extra8title'],
	"USERS_EDIT_EXTRA9_TITLE" => $cfg['extra9title'],
	"USERS_EDIT_WEBSITE" => "<input type=\"text\" class=\"text\" name=\"ruserwebsite\" value=\"".sed_cc($urr['user_website'])."\" size=\"56\" maxlength=\"128\" />",
	"USERS_EDIT_ICQ" => "<input type=\"text\" class=\"text\" name=\"rusericq\" value=\"".sed_cc($urr['user_icq'])."\" size=\"32\" maxlength=\"16\" />",
	"USERS_EDIT_MSN" => "<input type=\"text\" class=\"text\" name=\"rusermsn\" value=\"".sed_cc($urr['user_msn'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_EDIT_IRC" => "<input type=\"text\" class=\"text\" name=\"ruserirc\" value=\"".sed_cc($urr['user_irc'])."\" size=\"56\" maxlength=\"128\" />",
	"USERS_EDIT_GENDER" => $user_form_gender,
	"USERS_EDIT_BIRTHDATE" => $user_form_birthdate,
	"USERS_EDIT_TIMEZONE" => "<input type=\"text\" class=\"text\" name=\"rusertimezone\" value=\"".sed_cc($urr['user_timezone'])."\" size=\"32\" maxlength=\"16\" />",
	"USERS_EDIT_LOCATION" => "<input type=\"text\" class=\"text\" name=\"ruserlocation\" value=\"".sed_cc($urr['user_location'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_EDIT_OCCUPATION" => "<input type=\"text\" class=\"text\" name=\"ruseroccupation\" value=\"".sed_cc($urr['user_occupation'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_EDIT_REGDATE" => @date($cfg['dateformat'], $urr['user_regdate'] + $usr['timezone'] * 3600)." ".$usr['timetext'],
	"USERS_EDIT_LASTLOG" => @date($cfg['dateformat'], $urr['user_lastlog'] + $usr['timezone']*3600)." ".$usr['timetext'],
	"USERS_EDIT_LOGCOUNT" => $urr['user_logcount'],
	"USERS_EDIT_LASTIP" => $urr['user_lastip'],
	"USERS_EDIT_DELETE" => $user_form_delete,
);

// Extra fields
if(count($extrafields)>0)
foreach($extrafields as $i=>$row)
{
	$t1 = "USERS_EDIT_".strtoupper($row['field_name']);
	$t2 = $row['field_html'];
	switch($row['field_type']) {
	case "input":
		$t2 = str_replace('<input ','<input name="ruser'.$row['field_name'].'" ', $t2);
		$t2 = str_replace('<input ','<input value="'.$urr['user_'.$row['field_name']].'" ', $t2); break;
	case "textarea":
		$t2 = str_replace('<textarea ','<textarea name="ruser'.$row['field_name'].'" ', $t2);
		$t2 = str_replace('</textarea>',$urr['user_'.$row['field_name']].'</textarea>', $t2); break;
	case "select":
		$t2 = str_replace('<select','<select name="ruser'.$row['field_name'].'"', $t2);
		$options = "";
		$opt_array = explode(",",$row['field_variants']);
		if(count($opt_array)!=0)
			foreach ($opt_array as $var)
			{
				$sel = $var == $urr['user_'.$row['field_name']] ? ' selected="selected"' : '';
				$options .= "<option value=\"$var\" $sel>$var</option>";

			}
		$t2 = str_replace("</select>","$options</select>",$t2); break;
	case "checkbox":
		$t2 = str_replace('<input','<input name="ruser'.$row['field_name'].'"', $t2);
		$sel = $urr['user_'.$row['field_name']]==1 ? ' checked' : '';
		$t2 = str_replace('<input ','<input value="'.$urr['user_'.$row['field_name']].'" '.$sel.' ', $t2); break;
	}
	$useredit_array[$t1] = $t2;
}
$t->assign($useredit_array);

/* === Hook === */
$extp = sed_getextplugins('users.edit.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */


$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>