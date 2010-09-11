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

sed_require_api('auth');

$v = sed_import('v','G','ALP');
$y = sed_import('y','G','INT');

if ($cfg['disablereg'])
{
	sed_redirect(sed_url('message', 'msg=117', '', true));
}

/* === Hook === */
foreach (sed_getextplugins('users.register.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a=='add')
{
	$bannedreason = FALSE;
	sed_shield_protect();

	/* === Hook for the plugins === */
	foreach (sed_getextplugins('users.register.add.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$ruser['name'] = sed_import('rusername','P','TXT', 100, TRUE);
	$ruser['email'] = sed_import('ruseremail','P','TXT',64, TRUE);
	$rpassword1 = sed_import('rpassword1','P','TXT',16);
	$rpassword2 = sed_import('rpassword2','P','TXT',16);
	$ruser['country'] = sed_import('rcountry','P','TXT');
	$ruser['timezone'] = sed_import('rtimezone','P','TXT',5);
	$ruser['timezone'] = is_null($ruser['timezone']) ? $cfg['defaulttimezone'] : (float) $ruser['timezone'];
	$ruser['gender'] = sed_import('rusergender','P','TXT');
	$ruser['email'] = mb_strtolower($ruser['email']);

	// Extra fields
	foreach($sed_extrafields['users'] as $row)
	{
		$ruser[$row['field_name']] = sed_import_extrafields('user', $row);
	}
	$ruser['birthdate'] = (int)sed_import_date('ruserbirthdate');

	$sql = sed_sql_query("SELECT banlist_reason, banlist_email FROM $db_banlist WHERE banlist_email!=''");

	while ($row = sed_sql_fetcharray($sql))
	{
		if (mb_strpos($row['banlist_email'], $ruser['email']) !== false)
			$bannedreason = $row['banlist_reason'];
	}

	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_name='".sed_sql_prep($ruser['name'])."'");
	$res1 = sed_sql_result($sql,0,"COUNT(*)");
	$sql = sed_sql_query("SELECT COUNT(*) FROM $db_users WHERE user_email='".sed_sql_prep($ruser['email'])."'");
	$res2 = sed_sql_result($sql,0,"COUNT(*)");

	if (preg_match('/&#\d+;/', $ruser['name']) || preg_match('/[<>#\'"\/]/', $ruser['name'])) sed_error('aut_invalidloginchars', 'rusername');
	if (!empty($bannedreason)) sed_error($L['aut_emailbanned'].$bannedreason);
	if (mb_strlen($ruser['name']) < 2) sed_error('aut_usernametooshort', 'rusername');
	if (mb_strlen($rpassword1) < 4 || sed_alphaonly($rpassword1) != $rpassword1) sed_error('aut_passwordtooshort', 'rpassword1');
	if (mb_strlen($ruser['email']) < 4 || !preg_match('#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$#i', $ruser['email']))
		sed_error('aut_emailtooshort', 'ruseremail');
	if ($res1>0) sed_error('aut_usernamealreadyindb', 'rusername');
	if ($res2>0) sed_error('aut_emailalreadyindb', 'ruseremail');
	if ($rpassword1 != $rpassword2) sed_error('aut_passwordmismatch', 'rpassword2');

	if (!$cot_error)
	{
		if (sed_sql_rowcount($db_users)==0)
		{
			$ruser['maingrp'] = 5;
		}
		else
		{
			$ruser['maingrp'] = ($cfg['regnoactivation']) ? 4 : 2;
		}

		$ruser['password'] = md5($rpassword1);
		$ruser['birthdate'] = ($ruser['birthdate'] > $sys['now_offset']) ? ($sys['now_offset'] - 31536000) : $ruser['birthdate'];
		$ruser['birthdate'] = ($ruser['birthdate'] == '0') ? '0000-00-00' : sed_stamp2date($ruser['birthdate']);

		$ruser['lostpass'] = md5(microtime());
		sed_shield_update(20, "Registration");

		$ruser['hideemail'] = 1;
		$ruser['pmnotify'] = 0;

		$ruser['skin'] = $cfg['defaultskin'];
		$ruser['theme'] = $cfg['defaulttheme'];
		$ruser['lang'] = $cfg['defaultlang'];
		$ruser['regdate'] = (int)$sys['now_offset'];
		$ruser['logcount'] = 0;
		$ruser['lastip'] = $usr['ip'];

		sed_sql_insert($db_users, $ruser, 'user_');

		$userid = sed_sql_insertid();

		sed_sql_insert($db_groups_users, array('gru_userid' => (int)$userid, 'gru_groupid' => (int)$ruser['maingrp']));
	
		/* === Hook for the plugins === */
		foreach (sed_getextplugins('users.register.add.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($cfg['regnoactivation'] || $ruser['maingrp']==5)
		{
			sed_redirect(sed_url('message', 'msg=106', '', true));
		}

		if ($cfg['regrequireadmin'])
		{
			$rsubject = $cfg['maintitle']." - ".$L['aut_regrequesttitle'];
			$rbody = sprintf($L['aut_regrequest'], $ruser['name'], $rpassword1);
			$rbody .= "\n\n".$L['aut_contactadmin'];
			sed_mail ($ruser['email'], $rsubject, $rbody);

			$rsubject = $cfg['maintitle']." - ".$L['aut_regreqnoticetitle'];
			$rinactive = $cfg['mainurl'].'/'.sed_url('users', 'gm=2&s=regdate&w=desc', '', true);
			$rbody = sprintf($L['aut_regreqnotice'], $ruser['name'], $rinactive);
			sed_mail ($cfg['adminemail'], $rsubject, $rbody);
			sed_redirect(sed_url('message', 'msg=118', '', true));
		}
		else
		{
			$rsubject = $cfg['maintitle']." - ".$L['Registration'];
			$ractivate = $cfg['mainurl'].'/'.sed_url('users', 'm=register&a=validate&v='.$ruser['lostpass'].'&y=1', '', true);
			$rdeactivate = $cfg['mainurl'].'/'.sed_url('users', 'm=register&a=validate&v='.$ruser['lostpass'].'&y=0', '', true);
			$rbody = sprintf($L['aut_emailreg'], $ruser['name'], $rpassword1, $ractivate, $rdeactivate);
			$rbody .= "\n\n".$L['aut_contactadmin'];
			sed_mail ($ruser['email'], $rsubject, $rbody);
			sed_redirect(sed_url('message', 'msg=105', '', true));
		}
	}
	else
	{
		sed_redirect(sed_url('users', 'm=register', '', true));
	}
}

elseif ($a == 'validate' && mb_strlen($v) == 32)
{
	/* === Hook for the plugins === */
	foreach (sed_getextplugins('users.register.validate.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	sed_shield_protect();
	$sql = sed_sql_query("SELECT user_id, user_maingrp, user_sid FROM $db_users WHERE user_lostpass='$v' AND (user_maingrp=2 OR user_maingrp='-1') ");

	if ($row = sed_sql_fetcharray($sql))
	{
		if ($row['user_maingrp'] == 2)
		{
			if ($y == 1)
			{
				$sql = sed_sql_update($db_users, array('user_maingrp' => 4), "user_id='".$row['user_id']."' AND user_lostpass='$v'");
				$sql = sed_sql_update($db_groups_users, array('gru_groupid' => 4), "gru_groupid=2 AND gru_userid='".$row['user_id']."'");

				/* === Hook for the plugins === */
				foreach (sed_getextplugins('users.register.validate.done') as $pl)
				{
					include $pl;
				}
				/* ===== */

				sed_auth_clear($row['user_id']);
				sed_redirect(sed_url('message', 'msg=106', '', true));
			}
			elseif ($y == 0)
			{
				$sql = sed_sql_delete($db_users, "user_maingrp='2' AND user_lastlog='0' AND user_id='".$row['user_id']."' ");
				$sql = sed_sql_delete($db_users, "user_id='".$row['user_id']."'");
				$sql = sed_sql_delete($db_groups_users, "gru_userid='".$row['user_id']."'");

				/* === Hook for the plugins === */
				foreach (sed_getextplugins('users.register.validate.rejected') as $pl)
				{
					include $pl;
				}
				/* ===== */

				sed_redirect(sed_url('message', 'msg=109', '', true));
			}
		}
		elseif ($row['user_maingrp'] == -1)
		{
			$sql = sed_sql_update($db_users, array('user_maingrp' => $row['user_sid']), "user_id='".$row['user_id']."' AND user_lostpass='$v'");
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

/* === Hook === */
foreach (sed_getextplugins('users.register.main') as $pl)
{
	include $pl;
}
/* ===== */

$out['subtitle'] = $L['aut_registertitle'];
$out['head'] .= $R['code_noindex'];
require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('users.register'));

sed_require_api('forms');

$timezonelist = array('-12', '-11', '-10', '-09', '-08', '-07', '-06', '-05', '-04', '-03',  '-03.5', '-02', '-01', '+00', '+01', '+02', '+03', '+03.5', '+04', '+04.5', '+05', '+05.5', '+06', '+07', '+08', '+09', '+09.5', '+10', '+11', '+12');
foreach($timezonelist as $x)
{
	$timezonename[] = 'GMT ' . $x;
}
$form_timezone = sed_selectbox($ruser['timezone'], 'rtimezone', $timezonelist, $timezonename, false);
$form_timezone .= ' '.$usr['gmttime']." / ".date($cfg['dateformat'], $sys['now_offset'] + $usr['timezone']*3600).' '.$usr['timetext'];

$useredit_array = array(
	"USERS_REGISTER_TITLE" => $L['aut_registertitle'],
	"USERS_REGISTER_SUBTITLE" => $L['aut_registersubtitle'],
	"USERS_REGISTER_ADMINEMAIL" => $sed_adminemail,
	"USERS_REGISTER_SEND" => sed_url('users', 'm=register&a=add'),
	"USERS_REGISTER_USER" => sed_inputbox('text', 'rusername', $ruser['name'], array('size' => 24, 'maxlength' => 100)),
	"USERS_REGISTER_EMAIL" => sed_inputbox('text', 'ruseremail', $ruser['email'], array('size' => 24, 'maxlength' => 64)),
	"USERS_REGISTER_PASSWORD" => sed_inputbox('password', 'rpassword1', '', array('size' => 8, 'maxlength' => 32)),
	"USERS_REGISTER_PASSWORDREPEAT" => sed_inputbox('password', 'rpassword2', '', array('size' => 8, 'maxlength' => 32)),
	"USERS_REGISTER_COUNTRY" => sed_selectbox_countries($ruser['country'], 'rcountry'),
	"USERS_REGISTER_TIMEZONE" => $form_timezone,
	"USERS_REGISTER_GENDER" => sed_selectbox_gender($ruser['gender'],'rusergender'),
	"USERS_REGISTER_BIRTHDATE" => sed_selectbox_date(sed_mktime(1, 0, 0, $rmonth, $rday, $ryear), 'short', '', date('Y', $sys['now_offset']), 1910),
);
$t->assign($useredit_array);

// Extra fields
foreach($sed_extrafields['users'] as $i => $row)
{
	$t->assign('USERS_REGISTER_'.strtoupper($row['field_name']), sed_build_extrafields('user',  $row, htmlspecialchars($ruser['extrafields'][$row['field_name']])));
	$t->assign('USERS_REGISTER_'.strtoupper($row['field_name']).'_TITLE', isset($L['user_'.$row['field_name'].'_title']) ? $L['user_'.$row['field_name'].'_title'] : $row['field_description']);
}

// Error and message handling
if (sed_check_messages())
{
	$t->assign('USERS_REGISTER_ERROR_BODY', sed_implode_messages());
	$t->parse('MAIN.USERS_REGISTER_ERROR');
	sed_clear_messages();
}


/* === Hook === */
foreach (sed_getextplugins('users.register.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>