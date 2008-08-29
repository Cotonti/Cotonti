<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=users.register.inc.php
Version=122
Updated=2007-jul-16
Type=Core
Author=Neocrome
Description=User auth
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$v = sed_import('v','G','ALP');

if ($cfg['disablereg'])
	{
	sed_redirect("message.php?msg=117");
	exit;
	}

/* === Hook === */
$extp = sed_getextplugins('users.register.first');
if (is_array($extp))
	{ foreach ($extp as $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if ($a=='add')
	{
	$bannedreason = FALSE;
	sed_shield_protect();

	/* === Hook for the plugins === */
	$extp = sed_getextplugins('users.register.add.first');
	if (is_array($extp))
		{ foreach ($extp as $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$rusername = sed_import('rusername','P','TXT', 24, TRUE);
	$ruseremail = sed_import('ruseremail','P','TXT',64, TRUE);
	$rpassword1 = sed_import('rpassword1','P','TXT',16);
	$rpassword2 = sed_import('rpassword2','P','TXT',16);
	$rcountry = sed_import('rcountry','P','TXT');
	$rlocation = sed_import('rlocation','P','TXT');
	$rtimezone = sed_import('rtimezone','P','TXT',5);
	$roccupation = sed_import('roccupation','P','TXT');
	$rusergender = sed_import('rusergender','P','TXT');
	$ryear = sed_import('ryear','P','INT');
	$rmonth = sed_import('rmonth','P','INT');
	$rday = sed_import('rday','P','INT');
	$rusericq = sed_import('rusericq','P','TXT');
	$ruserirc = sed_import('ruserirc','P','TXT');
	$rusermsn = sed_import('rusermsn','P','TXT');
	$ruserwebsite = sed_import('ruserwebsite','P','TXT');
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
	$ruseremail = mb_strtolower($ruseremail);

	$sql = sed_sql_query("SELECT banlist_reason, banlist_email FROM $db_banlist WHERE banlist_email!=''");

	while ($row = sed_sql_fetcharray($sql))
		{
		if (mb_eregi($row['banlist_email'], $ruseremail))
			{ $bannedreason = $row['banlist_reason']; }
		}

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_name='".sed_sql_prep($rusername)."'");
	$res1 = sed_sql_result($sql,0,"COUNT(*)");
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_email='".sed_sql_prep($ruseremail)."'");
	$res2 = sed_sql_result($sql,0,"COUNT(*)");

	$rusername = str_replace('&#160;', '', $rusername);
	$error_string .= (!empty($bannedreason)) ? $L['aut_emailbanned'].$bannedreason."<br />" : '';
	$error_string .= (mb_strlen($rusername)<2) ? $L['aut_usernametooshort']."<br />" : '';
	$error_string .= (mb_strlen($rpassword1)<4 || sed_alphaonly($rpassword1)!=$rpassword1) ? $L['aut_passwordtooshort']."<br />" : '';
	$error_string .= (mb_strlen($ruseremail)<4 || !mb_eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$",$ruseremail)) ? $L['aut_emailtooshort']."<br />" : '';
	$error_string .= ($res1>0) ? $L['aut_usernamealreadyindb']."<br />" : '';
	$error_string .= ($res2>0) ? $L['aut_emailalreadyindb']."<br />" : '';
	$error_string .= ($rpassword1!=$rpassword2) ? $L['aut_passwordmismatch']."<br />" : '';

	if (empty($error_string))
		{
		if (sed_sql_rowcount($db_users)==0)
			{ $defgroup = 5; }
		else
			{ $defgroup = ($cfg['regnoactivation']) ? 4 : 2; }

		$mdpass = md5($rpassword1);
		$ruserbirthdate = ($rmonth=='x' || $rday=='x' || $ryear=='x' || $rmonth==0 || $rday==0 || $ryear==0) ? 0 : sed_mktime(1, 0, 0, $rmonth, $rday, $ryear);
		$ruserextra1 = ($ruserextra1_p) ? mb_substr($ruserextra1,0,$cfg['extra1tsetting']) : '';
		$ruserextra2 = ($ruserextra2_p) ? mb_substr($ruserextra2,0,$cfg['extra2tsetting']) : '';
		$ruserextra3 = ($ruserextra3_p) ? mb_substr($ruserextra3,0,$cfg['extra3tsetting']) : '';
		$ruserextra4 = ($ruserextra4_p) ? mb_substr($ruserextra4,0,$cfg['extra4tsetting']) : '';
		$ruserextra5 = ($ruserextra5_p) ? mb_substr($ruserextra5,0,$cfg['extra5tsetting']) : '';
		$ruserextra6 = ($ruserextra6_p) ? $ruserextra6 : '';
		$ruserextra7 = ($ruserextra7_p) ? $ruserextra7 : '';
		$ruserextra8 = ($ruserextra8_p) ? $ruserextra8 : '';
		$ruserextra9 = ($ruserextra9_p) ? $ruserextra9 : '';

		$validationkey = md5(microtime());
		sed_shield_update(20, "Registration");

		$sql = sed_sql_query("INSERT into $db_users
			(user_name,
			user_password,
			user_maingrp,
			user_country,
			user_location,
			user_timezone,
			user_occupation,
			user_text,
			user_email,
			user_hideemail,
			user_pmnotify,
			user_skin,
			user_lang,
			user_regdate,
			user_logcount,
			user_lostpass,
			user_gender,
			user_birthdate,
			user_icq,
			user_irc,
			user_msn,
			user_website,
			user_extra1,
			user_extra2,
			user_extra3,
			user_extra4,
			user_extra5,
			user_extra6,
			user_extra7,
			user_extra8,
			user_extra9,
			user_lastip)
			VALUES
			('".sed_sql_prep($rusername)."',
			'$mdpass',
			".(int)$defgroup.",
			'".sed_sql_prep($rcountry)."',
			'".sed_sql_prep($rlocation)."',
			'".sed_sql_prep($rtimezone)."',
			'".sed_sql_prep($roccupation)."',
			'',
			'".sed_sql_prep($ruseremail)."',
			1,
			0,
			'".$cfg['defaultskin']."',
			'".$cfg['defaultlang']."',
			".(int)$sys['now_offset'].",
			0,
			'$validationkey',
			'".sed_sql_prep($rusergender)."',
			".(int)$ruserbirthdate.",
			'".sed_sql_prep($rusericq)."',
			'".sed_sql_prep($ruserirc)."',
			'".sed_sql_prep($rusermsn)."',
			'".sed_sql_prep($ruserwebsite)."',
			'".sed_sql_prep($ruserextra1)."',
			'".sed_sql_prep($ruserextra2)."',
			'".sed_sql_prep($ruserextra3)."',
			'".sed_sql_prep($ruserextra4)."',
			'".sed_sql_prep($ruserextra5)."',
			'".sed_sql_prep($ruserextra6)."',
			'".sed_sql_prep($ruserextra7)."',
			'".sed_sql_prep($ruserextra8)."',
			'".sed_sql_prep($ruserextra9)."',
			'".$usr['ip']."')");

		$userid = sed_sql_insertid();
		$sql = sed_sql_query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (".(int)$userid.", ".(int)$defgroup.")");

		/* === Hook for the plugins === */
		$extp = sed_getextplugins('users.register.add.done');
		if (is_array($extp))
			{ foreach ($extp as $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		if ($cfg['regnoactivation'] || $defgroup==5)
			{
			sed_redirect("message.php?msg=106");
			exit;
			}

		if ($cfg['regrequireadmin'])
			{
			$rsubject = $cfg['maintitle']." - ".$L['aut_regrequesttitle'];
			$rbody = sprintf($L['aut_regrequest'], $rusername, $rpassword1);
			$rbody .= "\n\n".$L['aut_contactadmin'];
			sed_mail ($ruseremail, $rsubject, $rbody);

			$rsubject = $cfg['maintitle']." - ".$L['aut_regreqnoticetitle'];
			$rinactive = $cfg['mainurl']."/users.php?gm=2&s=regdate&w=desc";
			$rbody = sprintf($L['aut_regreqnotice'], $rusername, $rinactive);
			sed_mail ($cfg['adminemail'], $rsubject, $rbody);
			sed_redirect("message.php?msg=118");
			exit;
			}
		else
			{
			$rsubject = $cfg['maintitle']." - ".$L['Registration'];
			$ractivate = $cfg['mainurl']."/users.php?m=register&a=validate&v=".$validationkey;
			$rbody = sprintf($L['aut_emailreg'], $rusername, $rpassword1, $ractivate);
			$rbody .= "\n\n".$L['aut_contactadmin'];
			sed_mail ($ruseremail, $rsubject, $rbody);
			sed_redirect("message.php?msg=105");
			exit;
			}
		}
	}

elseif ($a=='validate' && mb_strlen($v)==32)
	{
	sed_shield_protect();
	$sql = sed_sql_query("SELECT user_id FROM $db_users WHERE user_lostpass='$v' AND user_maingrp=2");

	if ($row = sed_sql_fetcharray($sql))
		{
		$sql = sed_sql_query("UPDATE $db_users SET user_maingrp=4 WHERE user_id='".$row['user_id']."' AND user_lostpass='$v'");
		$sql = sed_sql_query("UPDATE $db_groups_users SET gru_groupid=4 WHERE gru_groupid=2 AND gru_userid='".$row['user_id']."'");
		sed_auth_clear($row['user_id']);
		sed_redirect("message.php?msg=106");
		exit;
		}
	else
		{
		sed_shield_update(7, "Account validation");
		sed_log("Wrong validation URL", 'sec');
		sed_redirect("message.php?msg=157");
		exit;
		}
	}

$form_usergender = sed_selectbox_gender($rusergender,'rusergender');
$form_birthdate = sed_selectbox_date(sed_mktime(1, 0, 0, $rmonth, $rday, $ryear), 'short');
$form_extra1 = "<input type=\"text\" class=\"text\" name=\"ruserextra1\" value=\"".sed_cc($ruserextra1)."\" size=\"32\" maxlength=\"".$cfg['extra1tsetting']."\" /><input type=\"hidden\" name=\"ruserextra1_p\" value=\"1\" />";
$form_extra2 = "<input type=\"text\" class=\"text\" name=\"ruserextra2\" value=\"".sed_cc($ruserextra2)."\" size=\"32\" maxlength=\"".$cfg['extra2tsetting']."\" /><input type=\"hidden\" name=\"ruserextra2_p\" value=\"1\" />";
$form_extra3 = "<input type=\"text\" class=\"text\" name=\"ruserextra3\" value=\"".sed_cc($ruserextra3)."\" size=\"32\" maxlength=\"".$cfg['extra3tsetting']."\" /><input type=\"hidden\" name=\"ruserextra3_p\" value=\"1\" />";
$form_extra4 = "<input type=\"text\" class=\"text\" name=\"ruserextra4\" value=\"".sed_cc($ruserextra4)."\" size=\"32\" maxlength=\"".$cfg['extra4tsetting']."\" /><input type=\"hidden\" name=\"ruserextra4_p\" value=\"1\" />";
$form_extra5 = "<input type=\"text\" class=\"text\" name=\"ruserextra5\" value=\"".sed_cc($ruserextra5)."\" size=\"32\" maxlength=\"".$cfg['extra5tsetting']."\" /><input type=\"hidden\" name=\"ruserextra5_p\" value=\"1\" />";
$form_extra6 = sed_selectbox($ruserextra6,'ruserextra6',$cfg['extra6tsetting'])."<input type=\"hidden\" name=\"ruserextra6_p\" value=\"1\" />";
$form_extra7 = sed_selectbox($ruserextra7,'ruserextra7',$cfg['extra7tsetting'])."<input type=\"hidden\" name=\"ruserextra7_p\" value=\"1\" />";
$form_extra8 = sed_selectbox($ruserextra8,'ruserextra8',$cfg['extra8tsetting'])."<input type=\"hidden\" name=\"ruserextra8_p\" value=\"1\" />";
$form_extra9 = "<textarea name=\"ruserextra9\" rows=\"4\" cols=\"56\">".sed_cc($ruserextra9)."</textarea><input type=\"hidden\" name=\"ruserextra9_p\" value=\"1\" />";

$timezonelist = array ('-12', '-11', '-10', '-09', '-08', '-07', '-06', '-05', '-04', '-03',  '-03.5', '-02', '-01', '+00', '+01', '+02', '+03', '+03.5', '+04', '+04.5', '+05', '+05.5', '+06', '+07', '+08', '+09', '+09.5', '+10', '+11', '+12');

$form_timezone = "<select name=\"rtimezone\" size=\"1\">";
while( list($i,$x) = each($timezonelist) )
	{
	$selected = ($x==$rtimezone) ? "selected=\"selected\"" : '';
	$form_timezone .= "<option value=\"$x\" $selected>GMT".$x."</option>";
	}
$form_timezone .= "</select> ".$usr['gmttime']." / ".date($cfg['dateformat'], $sys['now_offset'] + $usr['timezone']*3600)." ".$usr['timetext'];

/* === Hook === */
$extp = sed_getextplugins('users.register.main');
if (is_array($extp))
	{ foreach ($extp as $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate("skins/".$skin."/users.register.tpl");

if (!empty($error_string))
	{
	$t->assign("USERS_REGISTER_ERROR_BODY",$error_string);
	$t->parse("MAIN.USERS_REGISTER_ERROR");
	}

$t->assign(array(
	"USERS_REGISTER_TITLE" => $L['aut_registertitle'],
	"USERS_REGISTER_SUBTITLE" => $L['aut_registersubtitle'],
	"USERS_REGISTER_ADMINEMAIL" => "$sed_adminemail",
	"USERS_REGISTER_SEND" => "users.php?m=register&amp;a=add",
	"USERS_REGISTER_USER" => "<input type=\"text\" class=\"text\" name=\"rusername\" value=\"".sed_cc($rusername)."\" size=\"24\" maxlength=\"24\" />",
	"USERS_REGISTER_EMAIL" => "<input type=\"text\" class=\"text\" name=\"ruseremail\" value=\"".sed_cc($ruseremail)."\" size=\"24\" maxlength=\"64\" />",
	"USERS_REGISTER_PASSWORD" => "<input type=\"password\" class=\"password\" name=\"rpassword1\" size=\"8\" maxlength=\"16\" />",
	"USERS_REGISTER_PASSWORDREPEAT" => "<input type=\"password\" class=\"password\" name=\"rpassword2\" size=\"8\" maxlength=\"16\" />",
	"USERS_REGISTER_COUNTRY" => sed_selectbox_countries($rcountry, 'rcountry'),
	"USERS_REGISTER_LOCATION" => "<input type=\"text\" class=\"text\" name=\"rlocation\" value=\"".sed_cc($rlocation)."\" size=\"24\" maxlength=\"64\" />",
	"USERS_REGISTER_TIMEZONE" => $form_timezone,
	"USERS_REGISTER_OCCUPATION" => "<input type=\"text\" class=\"text\" name=\"roccupation\" value=\"".sed_cc($roccupation)."\" size=\"24\" maxlength=\"64\" />",
	"USERS_REGISTER_GENDER" => $form_usergender,
	"USERS_REGISTER_BIRTHDATE" => $form_birthdate,
	"USERS_REGISTER_WEBSITE" => "<input type=\"text\" class=\"text\" name=\"ruserwebsite\" value=\"".sed_cc($ruserwebsite)."\" size=\"56\" maxlength=\"128\" />",
	"USERS_REGISTER_ICQ" => "<input type=\"text\" class=\"text\" name=\"rusericq\" value=\"".sed_cc($rusericq)."\" size=\"32\" maxlength=\"16\" />",
	"USERS_REGISTER_IRC" => "<input type=\"text\" class=\"text\" name=\"ruserirc\" value=\"".sed_cc($ruserirc)."\" size=\"56\" maxlength=\"128\" />",
	"USERS_REGISTER_MSN" => "<input type=\"text\" class=\"text\" name=\"rusermsn\" value=\"".sed_cc($rusermsn)."\" size=\"32\" maxlength=\"64\" />",
	"USERS_REGISTER_EXTRA1" => $form_extra1,
	"USERS_REGISTER_EXTRA2" => $form_extra2,
	"USERS_REGISTER_EXTRA3" => $form_extra3,
	"USERS_REGISTER_EXTRA4" => $form_extra4,
	"USERS_REGISTER_EXTRA5" => $form_extra5,
	"USERS_REGISTER_EXTRA6" => $form_extra6,
	"USERS_REGISTER_EXTRA7" => $form_extra7,
	"USERS_REGISTER_EXTRA8" => $form_extra8,
	"USERS_REGISTER_EXTRA9" => $form_extra9,
		));

/* === Hook === */
$extp = sed_getextplugins('users.register.tags');
if (is_array($extp))
	{ foreach ($extp as $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>