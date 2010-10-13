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

defined('COT_CODE') or die('Wrong URL');

cot_require_api('auth');

$v = cot_import('v','G','ALP');
$y = cot_import('y','G','INT');

if ($cfg['disablereg'])
{
	$env['status'] = '403 Forbidden';
	cot_redirect(cot_url('message', 'msg=117', '', true));
}

/* === Hook === */
foreach (cot_getextplugins('users.register.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a=='add')
{
	$bannedreason = FALSE;
	cot_shield_protect();

	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.register.add.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$ruser['name'] = cot_import('rusername','P','TXT', 100, TRUE);
	$ruser['email'] = cot_import('ruseremail','P','TXT',64, TRUE);
	$rpassword1 = cot_import('rpassword1','P','TXT',16);
	$rpassword2 = cot_import('rpassword2','P','TXT',16);
	$ruser['country'] = cot_import('rcountry','P','TXT');
	$ruser['timezone'] = cot_import('rtimezone','P','TXT',5);
	$ruser['timezone'] = is_null($ruser['timezone']) ? $cfg['defaulttimezone'] : (float) $ruser['timezone'];
	$ruser['gender'] = cot_import('rusergender','P','TXT');
	$ruser['email'] = mb_strtolower($ruser['email']);

	// Extra fields
	foreach($cot_extrafields['users'] as $row)
	{
		$ruser[$row['field_name']] = cot_import_extrafields('ruser'.$row['field_name'], $row);
	}
	$ruser['birthdate'] = (int)cot_import_date('ruserbirthdate', false);

	$sql = $cot_db->query("SELECT banlist_reason, banlist_email FROM $db_banlist WHERE banlist_email!=''");

	while ($row = $sql->fetch())
	{
		if (mb_strpos($row['banlist_email'], $ruser['email']) !== false)
			$bannedreason = $row['banlist_reason'];
	}

	$sql = $cot_db->query("SELECT COUNT(*) FROM $db_users WHERE user_name='".$cot_db->prep($ruser['name'])."'");
	$res1 = $sql->fetchColumn();
	$sql = $cot_db->query("SELECT COUNT(*) FROM $db_users WHERE user_email='".$cot_db->prep($ruser['email'])."'");
	$res2 = $sql->fetchColumn();

	if (preg_match('/&#\d+;/', $ruser['name']) || preg_match('/[<>#\'"\/]/', $ruser['name'])) cot_error('aut_invalidloginchars', 'rusername');
	if (!empty($bannedreason)) cot_error($L['aut_emailbanned'].$bannedreason);
	if (mb_strlen($ruser['name']) < 2) cot_error('aut_usernametooshort', 'rusername');
	if (mb_strlen($rpassword1) < 4 || cot_alphaonly($rpassword1) != $rpassword1) cot_error('aut_passwordtooshort', 'rpassword1');
	if (mb_strlen($ruser['email']) < 4 || !preg_match('#^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$#i', $ruser['email']))
		cot_error('aut_emailtooshort', 'ruseremail');
	if ($res1>0) cot_error('aut_usernamealreadyindb', 'rusername');
	if ($res2>0) cot_error('aut_emailalreadyindb', 'ruseremail');
	if ($rpassword1 != $rpassword2) cot_error('aut_passwordmismatch', 'rpassword2');

	if (!$cot_error)
	{
		if ($cot_db->countRows($db_users)==0)
		{
			$ruser['maingrp'] = 5;
		}
		else
		{
			$ruser['maingrp'] = ($cfg['regnoactivation']) ? 4 : 2;
		}

		$ruser['password'] = md5($rpassword1);
		$ruser['birthdate'] = ($ruser['birthdate'] > $sys['now_offset']) ? ($sys['now_offset'] - 31536000) : $ruser['birthdate'];
		$ruser['birthdate'] = ($ruser['birthdate'] == '0') ? '0000-00-00' : cot_stamp2date($ruser['birthdate']);

		$ruser['lostpass'] = md5(microtime());
		cot_shield_update(20, "Registration");

		$ruser['hideemail'] = 1;
		$ruser['pmnotify'] = 0;

		$ruser['theme'] = $cfg['defaulttheme'];
		$ruser['scheme'] = $cfg['defaultscheme'];
		$ruser['lang'] = $cfg['defaultlang'];
		$ruser['regdate'] = (int)$sys['now_offset'];
		$ruser['logcount'] = 0;
		$ruser['lastip'] = $usr['ip'];

		$cot_db->insert($db_users, $ruser, 'user_');

		$userid = $cot_db->lastInsertId();

		$cot_db->insert($db_groups_users, array('gru_userid' => (int)$userid, 'gru_groupid' => (int)$ruser['maingrp']));
	
		/* === Hook for the plugins === */
		foreach (cot_getextplugins('users.register.add.done') as $pl)
		{
			include $pl;
		}
		/* ===== */

		if ($cfg['regnoactivation'] || $ruser['maingrp']==5)
		{
			cot_redirect(cot_url('message', 'msg=106', '', true));
		}

		if ($cfg['regrequireadmin'])
		{
			$rsubject = $cfg['maintitle']." - ".$L['aut_regrequesttitle'];
			$rbody = sprintf($L['aut_regrequest'], $ruser['name'], $rpassword1);
			$rbody .= "\n\n".$L['aut_contactadmin'];
			cot_mail ($ruser['email'], $rsubject, $rbody);

			$rsubject = $cfg['maintitle']." - ".$L['aut_regreqnoticetitle'];
			$rinactive = $cfg['mainurl'].'/'.cot_url('users', 'gm=2&s=regdate&w=desc', '', true);
			$rbody = sprintf($L['aut_regreqnotice'], $ruser['name'], $rinactive);
			cot_mail ($cfg['adminemail'], $rsubject, $rbody);
			cot_redirect(cot_url('message', 'msg=118', '', true));
		}
		else
		{
			$rsubject = $cfg['maintitle']." - ".$L['Registration'];
			$ractivate = $cfg['mainurl'].'/'.cot_url('users', 'm=register&a=validate&v='.$ruser['lostpass'].'&y=1', '', true);
			$rdeactivate = $cfg['mainurl'].'/'.cot_url('users', 'm=register&a=validate&v='.$ruser['lostpass'].'&y=0', '', true);
			$rbody = sprintf($L['aut_emailreg'], $ruser['name'], $rpassword1, $ractivate, $rdeactivate);
			$rbody .= "\n\n".$L['aut_contactadmin'];
			cot_mail ($ruser['email'], $rsubject, $rbody);
			cot_redirect(cot_url('message', 'msg=105', '', true));
		}
	}
	else
	{
		cot_redirect(cot_url('users', 'm=register', '', true));
	}
}

elseif ($a == 'validate' && mb_strlen($v) == 32)
{
	/* === Hook for the plugins === */
	foreach (cot_getextplugins('users.register.validate.first') as $pl)
	{
		include $pl;
	}
	/* ===== */

	cot_shield_protect();
	$sql = $cot_db->query("SELECT user_id, user_maingrp, user_sid FROM $db_users WHERE user_lostpass='$v' AND (user_maingrp=2 OR user_maingrp='-1') ");

	if ($row = $sql->fetch())
	{
		if ($row['user_maingrp'] == 2)
		{
			if ($y == 1)
			{
				$sql = $cot_db->update($db_users, array('user_maingrp' => 4), "user_id='".$row['user_id']."' AND user_lostpass='$v'");
				$sql = $cot_db->update($db_groups_users, array('gru_groupid' => 4), "gru_groupid=2 AND gru_userid='".$row['user_id']."'");

				/* === Hook for the plugins === */
				foreach (cot_getextplugins('users.register.validate.done') as $pl)
				{
					include $pl;
				}
				/* ===== */

				cot_auth_clear($row['user_id']);
				cot_redirect(cot_url('message', 'msg=106', '', true));
			}
			elseif ($y == 0)
			{
				$sql = $cot_db->delete($db_users, "user_maingrp='2' AND user_lastlog='0' AND user_id='".$row['user_id']."' ");
				$sql = $cot_db->delete($db_users, "user_id='".$row['user_id']."'");
				$sql = $cot_db->delete($db_groups_users, "gru_userid='".$row['user_id']."'");

				/* === Hook for the plugins === */
				foreach (cot_getextplugins('users.register.validate.rejected') as $pl)
				{
					include $pl;
				}
				/* ===== */

				cot_redirect(cot_url('message', 'msg=109', '', true));
			}
		}
		elseif ($row['user_maingrp'] == -1)
		{
			$sql = $cot_db->update($db_users, array('user_maingrp' => $row['user_sid']), "user_id='".$row['user_id']."' AND user_lostpass='$v'");
			cot_redirect(cot_url('message', 'msg=106', '', true));
		}
	}
	else
	{
		$env['status'] = '403 Forbidden';
		cot_shield_update(7, "Account validation");
		cot_log("Wrong validation URL", 'sec');
		cot_redirect(cot_url('message', 'msg=157', '', true));
	}
}

/* === Hook === */
foreach (cot_getextplugins('users.register.main') as $pl)
{
	include $pl;
}
/* ===== */

$out['subtitle'] = $L['aut_registertitle'];
$out['head'] .= $R['code_noindex'];
require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(cot_skinfile('users.register'));

cot_require_api('forms');

$timezonelist = array('-12', '-11', '-10', '-09', '-08', '-07', '-06', '-05', '-04', '-03',  '-03.5', '-02', '-01', '+00', '+01', '+02', '+03', '+03.5', '+04', '+04.5', '+05', '+05.5', '+06', '+07', '+08', '+09', '+09.5', '+10', '+11', '+12');
foreach($timezonelist as $x)
{
	$timezonename[] = 'GMT ' . $x;
}
$form_timezone = cot_selectbox($ruser['timezone'], 'rtimezone', $timezonelist, $timezonename, false);
$form_timezone .= ' '.$usr['gmttime']." / ".date($cfg['dateformat'], $sys['now_offset'] + $usr['timezone']*3600).' '.$usr['timetext'];

$useredit_array = array(
	"USERS_REGISTER_TITLE" => $L['aut_registertitle'],
	"USERS_REGISTER_SUBTITLE" => $L['aut_registersubtitle'],
	"USERS_REGISTER_ADMINEMAIL" => $cot_adminemail,
	"USERS_REGISTER_SEND" => cot_url('users', 'm=register&a=add'),
	"USERS_REGISTER_USER" => cot_inputbox('text', 'rusername', $ruser['name'], array('size' => 24, 'maxlength' => 100)),
	"USERS_REGISTER_EMAIL" => cot_inputbox('text', 'ruseremail', $ruser['email'], array('size' => 24, 'maxlength' => 64)),
	"USERS_REGISTER_PASSWORD" => cot_inputbox('password', 'rpassword1', '', array('size' => 8, 'maxlength' => 32)),
	"USERS_REGISTER_PASSWORDREPEAT" => cot_inputbox('password', 'rpassword2', '', array('size' => 8, 'maxlength' => 32)),
	"USERS_REGISTER_COUNTRY" => cot_selectbox_countries($ruser['country'], 'rcountry'),
	"USERS_REGISTER_TIMEZONE" => $form_timezone,
	"USERS_REGISTER_GENDER" => cot_selectbox_gender($ruser['gender'],'rusergender'),
	"USERS_REGISTER_BIRTHDATE" => cot_selectbox_date(cot_mktime(1, 0, 0, $rmonth, $rday, $ryear), 'short', '', date('Y', $sys['now_offset']), date('Y', $sys['now_offset']) - 100, false),
);
$t->assign($useredit_array);

// Extra fields
foreach($cot_extrafields['users'] as $i => $row)
{
	$t->assign('USERS_REGISTER_'.strtoupper($row['field_name']), cot_build_extrafields('ruser'.$row['field_name'],  $row, htmlspecialchars($ruser['extrafields'][$row['field_name']])));
	$t->assign('USERS_REGISTER_'.strtoupper($row['field_name']).'_TITLE', isset($L['user_'.$row['field_name'].'_title']) ? $L['user_'.$row['field_name'].'_title'] : $row['field_description']);
}

// Error and message handling
cot_display_messages($t);


/* === Hook === */
foreach (cot_getextplugins('users.register.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>