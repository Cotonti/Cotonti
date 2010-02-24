<?php

/**
 * User Registration Script
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

require_once sed_incfile('auth');

$v = sed_import('v','G','ALP');
$y = sed_import('y','G','INT');

if ($cfg['disablereg'])
{
	sed_redirect(sed_url('message', 'msg=117', '', true));
}

/* === Hook === */
$extp = sed_getextplugins('users.register.first');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

if ($a=='add')
{
	$bannedreason = FALSE;
	sed_shield_protect();

	/* === Hook for the plugins === */
	$extp = sed_getextplugins('users.register.add.first');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	$rusername = sed_import('rusername','P','TXT', 100, TRUE);
	$ruseremail = sed_import('ruseremail','P','TXT',64, TRUE);
	$rpassword1 = sed_import('rpassword1','P','TXT',16);
	$rpassword2 = sed_import('rpassword2','P','TXT',16);
	$rcountry = sed_import('rcountry','P','TXT');
	$rlocation = sed_import('rlocation','P','TXT');
	$rtimezone = (float) sed_import('rtimezone','P','TXT',5);
	$roccupation = sed_import('roccupation','P','TXT');
	$rusergender = sed_import('rusergender','P','TXT');
	$ryear = sed_import('ryear','P','INT');
	$rmonth = sed_import('rmonth','P','INT');
	$rday = sed_import('rday','P','INT');
	$rusericq = sed_import('rusericq','P','TXT');
	$ruserirc = sed_import('ruserirc','P','TXT');
	$rusermsn = sed_import('rusermsn','P','TXT');
	$ruserwebsite = sed_import('ruserwebsite','P','TXT');
	$ruseremail = mb_strtolower($ruseremail);

	// Extra fields
	foreach($sed_extrafields['users'] as $row)
	{
		$import = sed_import('ruser'.$row['field_name'],'P','HTM');
		if($row['field_type']=="checkbox")
		{
			$import = $import != '';
		}
		$ruserextrafields[$row['field_name']] = $import;
		$urr['user_'.$row[ 'field_name']] = $import;
	}

	$sql = sed_sql_query("SELECT banlist_reason, banlist_email FROM $db_banlist WHERE banlist_email!=''");

	while ($row = sed_sql_fetcharray($sql))
	{
		if (mb_strpos($row['banlist_email'], $ruseremail) !== false)
		{ $bannedreason = $row['banlist_reason']; }
	}

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_name='".sed_sql_prep($rusername)."'");
	$res1 = sed_sql_result($sql,0,"COUNT(*)");
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_email='".sed_sql_prep($ruseremail)."'");
	$res2 = sed_sql_result($sql,0,"COUNT(*)");

	$error_string .= (preg_match('/&#\d+;/', $rusername) || preg_match('/[<>#\'"\/]/', $rusername)) ?
		$L['aut_invalidloginchars'] . '<br />' : '';
	$error_string .= (!empty($bannedreason)) ? $L['aut_emailbanned'].$bannedreason."<br />" : '';
	$error_string .= (mb_strlen($rusername)<2) ? $L['aut_usernametooshort']."<br />" : '';
	$error_string .= (mb_strlen($rpassword1)<4 || sed_alphaonly($rpassword1)!=$rpassword1) ? $L['aut_passwordtooshort']."<br />" : '';
	$error_string .= (mb_strlen($ruseremail)<4 || !preg_match('#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$#i', $ruseremail)) ? $L['aut_emailtooshort']."<br />" : '';
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

		$validationkey = md5(microtime());
		sed_shield_update(20, "Registration");

		// Extra fields
		$extra_columns = ""; $extra_values = "";
		foreach($sed_extrafields['users'] as $i=>$row)
		{
			$extra_columns .= "user_".$row['field_name'].", ";
			$extra_values .= "'".sed_sql_prep($ruserextrafields[$i])."', ";
		}
		$ssql = "INSERT into $db_users
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
			user_theme,
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
			$extra_columns
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
			'".$cfg['defaulttheme']."',
			'".$cfg['defaultlang']."',
			".(int)$sys['now_offset'].",
			0,
			'$validationkey',
			'".sed_sql_prep($rusergender)."',
			'".$ruserbirthdate."',
			'".sed_sql_prep($rusericq)."',
			'".sed_sql_prep($ruserirc)."',
			'".sed_sql_prep($rusermsn)."',
			'".sed_sql_prep($ruserwebsite)."',
			$extra_values
			'".$usr['ip']."')";
		$sql = sed_sql_query($ssql);
		$userid = sed_sql_insertid();
		$sql = sed_sql_query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (".(int)$userid.", ".(int)$defgroup.")");

		/* === Hook for the plugins === */
		$extp = sed_getextplugins('users.register.add.done');
		foreach ($extp as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($cfg['regnoactivation'] || $defgroup==5)
		{
			sed_redirect(sed_url('message', 'msg=106', '', true));
		}

		if ($cfg['regrequireadmin'])
		{
			$rsubject = $cfg['maintitle']." - ".$L['aut_regrequesttitle'];
			$rbody = sprintf($L['aut_regrequest'], $rusername, $rpassword1);
			$rbody .= "\n\n".$L['aut_contactadmin'];
			sed_mail ($ruseremail, $rsubject, $rbody);

			$rsubject = $cfg['maintitle']." - ".$L['aut_regreqnoticetitle'];
			$rinactive = $cfg['mainurl'].'/'.sed_url('users', 'gm=2&s=regdate&w=desc', '', true);
			$rbody = sprintf($L['aut_regreqnotice'], $rusername, $rinactive);
			sed_mail ($cfg['adminemail'], $rsubject, $rbody);
			sed_redirect(sed_url('message', 'msg=118', '', true));
		}
		else
		{
			$rsubject = $cfg['maintitle']." - ".$L['Registration'];
			$ractivate = $cfg['mainurl'].'/'.sed_url('users', 'm=register&a=validate&v='.$validationkey.'&y=1', '', true);
			$rdeactivate = $cfg['mainurl'].'/'.sed_url('users', 'm=register&a=validate&v='.$validationkey.'&y=0', '', true);
			$rbody = sprintf($L['aut_emailreg'], $rusername, $rpassword1, $ractivate, $rdeactivate);
			$rbody .= "\n\n".$L['aut_contactadmin'];
			sed_mail ($ruseremail, $rsubject, $rbody);
			sed_redirect(sed_url('message', 'msg=105', '', true));
		}
	}
}

elseif ($a=='validate' && mb_strlen($v)==32)
{
	sed_shield_protect();
	$sql = sed_sql_query("SELECT user_id, user_maingrp, user_sid FROM $db_users WHERE user_lostpass='$v' AND (user_maingrp=2 OR user_maingrp='-1') ");

	if ($row = sed_sql_fetcharray($sql))
	{
	
	if ($row['user_maingrp'] == 2)
		{
		
	if ($y==1)
		{
		$sql = sed_sql_query("UPDATE $db_users SET user_maingrp=4 WHERE user_id='".$row['user_id']."' AND user_lostpass='$v'");
		$sql = sed_sql_query("UPDATE $db_groups_users SET gru_groupid=4 WHERE gru_groupid=2 AND gru_userid='".$row['user_id']."'");
		sed_auth_clear($row['user_id']);
		sed_redirect(sed_url('message', 'msg=106', '', true));
		}
	elseif ($y==0)
		{
		$sql = sed_sql_query("DELETE FROM $db_users WHERE user_maingrp='2' AND user_lastlog='0' AND user_id='".$row['user_id']."' ");
		$sql = sed_sql_query("DELETE FROM $db_users WHERE user_id='".$row['user_id']."'");
		$sql = sed_sql_query("DELETE FROM $db_groups_users WHERE gru_userid='".$row['user_id']."'");
		sed_redirect(sed_url('message', 'msg=109', '', true));
		}
		
		}
	elseif ($row['user_maingrp']==-1)
		{
		$sql = sed_sql_query("UPDATE $db_users SET user_maingrp='".sed_sql_prep($row['user_sid'])."' WHERE user_id='".$row['user_id']."' AND user_lostpass='$v'");
		sed_redirect(sed_url('message', 'msg=106', '', true));
		}
	}
	else
	{
		sed_shield_update(7, "Account validation");
		sed_log("Wrong validation URL", 'sec');
		sed_redirect(sed_url('message', 'msg=157', '', true));
	}
}

$form_usergender = sed_selectbox_gender($rusergender,'rusergender');
$form_birthdate = sed_selectbox_date(sed_mktime(1, 0, 0, $rmonth, $rday, $ryear), 'short', '', date('Y', $sys['now_offset']));

$timezonelist = array ('-12', '-11', '-10', '-09', '-08', '-07', '-06', '-05', '-04', '-03',  '-03.5', '-02', '-01', '+00', '+01', '+02', '+03', '+03.5', '+04', '+04.5', '+05', '+05.5', '+06', '+07', '+08', '+09', '+09.5', '+10', '+11', '+12');

$form_timezone = "<select name=\"rtimezone\" size=\"1\">";
foreach($timezonelist as $x) 
{ 
	$f = (float) $x; 
	$selected = ($f==$rtimezone) ? "selected=\"selected\"" : ''; 
	$form_timezone .= "<option value=\"$f\" $selected>GMT ".$x."</option>";
}
$form_timezone .= "</select> ".$usr['gmttime']." / ".date($cfg['dateformat'], $sys['now_offset'] + $usr['timezone']*3600)." ".$usr['timetext'];

/* === Hook === */
$extp = sed_getextplugins('users.register.main');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$out['subtitle'] = $L['aut_registertitle'];
$out['head'] .= $R['code_noindex'];
require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('users.register'));

if (!empty($error_string))
{
	$t->assign("USERS_REGISTER_ERROR_BODY",$error_string);
	$t->parse("MAIN.USERS_REGISTER_ERROR");
}

$useredit_array = array(
	"USERS_REGISTER_TITLE" => $L['aut_registertitle'],
	"USERS_REGISTER_SUBTITLE" => $L['aut_registersubtitle'],
	"USERS_REGISTER_ADMINEMAIL" => "$sed_adminemail",
	"USERS_REGISTER_SEND" => sed_url('users', 'm=register&a=add'),
	"USERS_REGISTER_USER" => "<input type=\"text\" class=\"text\" name=\"rusername\" value=\"".htmlspecialchars($rusername)."\" size=\"24\" maxlength=\"100\" />",
	"USERS_REGISTER_EMAIL" => "<input type=\"text\" class=\"text\" name=\"ruseremail\" value=\"".htmlspecialchars($ruseremail)."\" size=\"24\" maxlength=\"64\" />",
	"USERS_REGISTER_PASSWORD" => "<input type=\"password\" class=\"password\" name=\"rpassword1\" size=\"8\" maxlength=\"16\" />",
	"USERS_REGISTER_PASSWORDREPEAT" => "<input type=\"password\" class=\"password\" name=\"rpassword2\" size=\"8\" maxlength=\"16\" />",
	"USERS_REGISTER_COUNTRY" => sed_selectbox_countries($rcountry, 'rcountry'),
	"USERS_REGISTER_LOCATION" => "<input type=\"text\" class=\"text\" name=\"rlocation\" value=\"".htmlspecialchars($rlocation)."\" size=\"24\" maxlength=\"64\" />",
	"USERS_REGISTER_TIMEZONE" => $form_timezone,
	"USERS_REGISTER_OCCUPATION" => "<input type=\"text\" class=\"text\" name=\"roccupation\" value=\"".htmlspecialchars($roccupation)."\" size=\"24\" maxlength=\"64\" />",
	"USERS_REGISTER_GENDER" => $form_usergender,
	"USERS_REGISTER_BIRTHDATE" => $form_birthdate,
	"USERS_REGISTER_WEBSITE" => "<input type=\"text\" class=\"text\" name=\"ruserwebsite\" value=\"".htmlspecialchars($ruserwebsite)."\" size=\"56\" maxlength=\"128\" />",
	"USERS_REGISTER_ICQ" => "<input type=\"text\" class=\"text\" name=\"rusericq\" value=\"".htmlspecialchars($rusericq)."\" size=\"32\" maxlength=\"16\" />",
	"USERS_REGISTER_IRC" => "<input type=\"text\" class=\"text\" name=\"ruserirc\" value=\"".htmlspecialchars($ruserirc)."\" size=\"56\" maxlength=\"128\" />",
	"USERS_REGISTER_MSN" => "<input type=\"text\" class=\"text\" name=\"rusermsn\" value=\"".htmlspecialchars($rusermsn)."\" size=\"32\" maxlength=\"64\" />",
);
$t->assign($useredit_array);

// Extra fields
foreach($sed_extrafields['users'] as $i => $row)
{
	$t->assign('USERS_REGISTER_'.strtoupper($row['field_name']), sed_build_extrafields('user',  $row, htmlspecialchars($ruserextrafields[$row['field_name']])));
	$t->assign('USERS_REGISTER_'.strtoupper($row['field_name']).'_TITLE', isset($L['user_'.$row['field_name'].'_title']) ? $L['user_'.$row['field_name'].'_title'] : $row['field_description']);
}


/* === Hook === */
$extp = sed_getextplugins('users.register.tags');
foreach ($extp as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>