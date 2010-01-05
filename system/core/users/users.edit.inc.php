<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * @package Cotonti
 * @version 0.0.3
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

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

$sql1 = sed_sql_query("SELECT gru_groupid FROM $db_groups_users WHERE gru_userid='$id' and gru_groupid='".SED_GROUP_TOPADMINS."'");
$sys['edited_istopadmin'] = (sed_sql_numrows($sql1)>0) ? TRUE : FALSE;
$sys['user_istopadmin'] = sed_auth('admin', 'a', 'A');
$sys['protecttopadmin'] = $sys['edited_istopadmin'] && !$sys['user_istopadmin'];

if ($sys['protecttopadmin'])
{
	sed_redirect(sed_url('message', "msg=930", '', true));
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

	$error_string .= (mb_strlen($rusername) < 2 || mb_strpos($rusername, ',') !== false || mb_strpos($rusername, "'") !== false) ? $L['aut_usernametooshort'] . '<br />' : '';
	$error_string .= (!empty($rusernewpass) && (mb_strlen($rusernewpass) < 4 || sed_alphaonly($rusernewpass) != $rusernewpass)) ? $L['aut_passwordtooshort'] . '<br />' : '';

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

	if (empty($error_string))
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
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_lastpostername='$newname' WHERE ft_lastpostername='$oldname'");
			$sql = sed_sql_query("UPDATE $db_forum_topics SET ft_firstpostername='$newname' WHERE ft_firstpostername='$oldname'");
			$sql = sed_sql_query("UPDATE $db_forum_posts SET fp_postername='$newname' WHERE fp_postername='$oldname'");
			$sql = sed_sql_query("UPDATE $db_pages SET page_author='$newname' WHERE page_author='$oldname'");
			$sql = sed_sql_query("UPDATE $db_com SET com_author='$newname' WHERE com_author='$oldname'");
			$sql = sed_sql_query("UPDATE $db_online SET online_name='$newname' WHERE online_name='$oldname'");
			$sql = sed_sql_query("UPDATE $db_pm SET pm_fromuser='$newname' WHERE pm_fromuser='$oldname'");
		}
		// Extra fields
		if(count($extrafields)>0)
		{
			foreach($extrafields as $i=>$row)
			{
				if(!is_null($ruserextrafields[$i]))
				{
					$ssql_extra .= "user_".$row['field_name']." = '".sed_sql_prep($ruserextrafields[$i])."',";
				}
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
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		sed_auth_clear($id);
		sed_log("Edited user #".$id,'adm');
		sed_redirect(sed_url('users', "m=edit&id=".$id, '', true));
	}
}

$user_form_delete = ($sys['user_istopadmin']) ? "<input type=\"radio\" class=\"radio\" name=\"ruserdelete\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserdelete\" value=\"0\" checked=\"checked\" />".$L['No']."<br />+ ".$L['PFS'].":<input type=\"checkbox\" class=\"checkbox\" name=\"ruserdelpfs\" />" : $L['na'];

$user_form_hideemail = ($urr['user_hideemail']) ? "<input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserhideemail\" value=\"0\" checked=\"checked\" />".$L['No'];

$user_form_pmnotify = ($urr['user_pmnotify']) ? "<input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"1\" checked=\"checked\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"0\" />".$L['No'] : "<input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"1\" />".$L['Yes']." <input type=\"radio\" class=\"radio\" name=\"ruserpmnotify\" value=\"0\" checked=\"checked\" />".$L['No'];

$user_form_pass = $sys['protecttopadmin'] ? "<input type=\"text\" class=\"text\" name=\"rusernewpass\" value=\"\" size=\"16\" maxlength=\"16\" disabled=\"disabled\" />" : "<input type=\"text\" class=\"text\" name=\"rusernewpass\" value=\"\" size=\"16\" maxlength=\"16\" />";

$user_form_username = $sys['protecttopadmin'] ? "<input type=\"text\" class=\"text\" name=\"rusername\" value=\"".htmlspecialchars($urr['user_name'])."\" size=\"32\" maxlength=\"100\"  disabled=\"disabled\" />" : "<input type=\"text\" class=\"text\" name=\"rusername\" value=\"".htmlspecialchars($urr['user_name'])."\" size=\"32\" maxlength=\"100\" />";

$user_form_countries = sed_selectbox_countries($urr['user_country'], 'rusercountry');
$user_form_gender = sed_selectbox_gender($urr['user_gender'], 'rusergender');
$user_form_birthdate = sed_selectbox_date($urr['user_birthdate'], 'short', '', date('Y', $sys['now_offset']));
$urr['user_lastip'] = sed_build_ipsearch($urr['user_lastip']);

$title_tags[] = array('{EDIT}', '{NAME}');
$title_tags[] = array('%1$s', '%2$s');
$title_data = array($L['Edit'], htmlspecialchars($urr['user_name']));
$out['subtitle'] = sed_title('title_users_edit', $title_tags, $title_data);

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

if (!empty($error_string))
{
	$t->assign("USERS_EDIT_ERROR_BODY",$error_string);
	$t->parse("MAIN.USERS_EDIT_ERROR");
}

$bhome = $cfg['homebreadcrumb'] ? '<a href="'.$cfg['mainurl'].'">'.htmlspecialchars($cfg['maintitle']).'</a> '.$cfg['separator'].' ' : '';

$useredit_array = array(
	"USERS_EDIT_TITLE" => $bhome . "<a href=\"".sed_url('users')."\">".$L['Users']."</a> ".$cfg['separator']." ".sed_build_user($urr['user_id'], htmlspecialchars($urr['user_name']))." ".$cfg['separator']." <a href=\"".sed_url('users', 'm=edit&id='.$urr['user_id'])."\">".$L['Edit']."</a>",
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
	"USERS_EDIT_EMAIL" => "<input type=\"text\" class=\"text\" name=\"ruseremail\" value=\"".htmlspecialchars($urr['user_email'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_EDIT_HIDEEMAIL" => $user_form_hideemail,
	"USERS_EDIT_PMNOTIFY" => $user_form_pmnotify,
	"USERS_EDIT_TEXT" => "<textarea class=\"editor\" name=\"rusertext\" rows=\"4\" cols=\"56\">".htmlspecialchars($urr['user_text'])."</textarea>",
	"USERS_EDIT_TEXTBOXER" => "<textarea class=\"editor\" name=\"rusertext\" rows=\"4\" cols=\"56\">".htmlspecialchars($urr['user_text'])."</textarea>",
	"USERS_EDIT_AVATAR" => "<input type=\"text\" class=\"text\" name=\"ruseravatar\" value=\"".htmlspecialchars($urr['user_avatar'])."\" size=\"32\" maxlength=\"255\" />",
	"USERS_EDIT_PHOTO" => "<input type=\"text\" class=\"text\" name=\"ruserphoto\" value=\"".htmlspecialchars($urr['user_photo'])."\" size=\"32\" maxlength=\"255\" />",
	"USERS_EDIT_SIGNATURE" => "<input type=\"text\" class=\"text\" name=\"rusersignature\" value=\"".htmlspecialchars($urr['user_signature'])."\" size=\"32\" maxlength=\"255\" />",
	"USERS_EDIT_WEBSITE" => "<input type=\"text\" class=\"text\" name=\"ruserwebsite\" value=\"".htmlspecialchars($urr['user_website'])."\" size=\"56\" maxlength=\"128\" />",
	"USERS_EDIT_ICQ" => "<input type=\"text\" class=\"text\" name=\"rusericq\" value=\"".htmlspecialchars($urr['user_icq'])."\" size=\"32\" maxlength=\"16\" />",
	"USERS_EDIT_MSN" => "<input type=\"text\" class=\"text\" name=\"rusermsn\" value=\"".htmlspecialchars($urr['user_msn'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_EDIT_IRC" => "<input type=\"text\" class=\"text\" name=\"ruserirc\" value=\"".htmlspecialchars($urr['user_irc'])."\" size=\"56\" maxlength=\"128\" />",
	"USERS_EDIT_GENDER" => $user_form_gender,
	"USERS_EDIT_BIRTHDATE" => $user_form_birthdate,
	"USERS_EDIT_TIMEZONE" => "<input type=\"text\" class=\"text\" name=\"rusertimezone\" value=\"".htmlspecialchars($urr['user_timezone'])."\" size=\"32\" maxlength=\"16\" />",
	"USERS_EDIT_LOCATION" => "<input type=\"text\" class=\"text\" name=\"ruserlocation\" value=\"".htmlspecialchars($urr['user_location'])."\" size=\"32\" maxlength=\"64\" />",
	"USERS_EDIT_OCCUPATION" => "<input type=\"text\" class=\"text\" name=\"ruseroccupation\" value=\"".htmlspecialchars($urr['user_occupation'])."\" size=\"32\" maxlength=\"64\" />",
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
	$extra_array = sed_build_extrafields('user', 'USERS_EDIT', $extrafields, $urr);
	$useredit_array = $useredit_array + $extra_array;
}
$t->assign($useredit_array);

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