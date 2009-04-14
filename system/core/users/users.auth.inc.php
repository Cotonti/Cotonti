<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=users.auth.inc.php
Version=102
Updated=2006-apr-19
Type=Core
Author=Neocrome
Description=User authication
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$v = sed_import('v','G','PSW');

/* === Hook === */
$extp = sed_getextplugins('users.auth.first');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if ($a=='check')
{
	sed_shield_protect();

	/* === Hook for the plugins === */
	$extp = sed_getextplugins('users.auth.check');
	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
	/* ===== */

	$rusername = sed_import('rusername','P','TXT', 100, TRUE);
	$rpassword = sed_import('rpassword','P','PSW', 16, TRUE);
	$rcookiettl = sed_import('rcookiettl', 'P', 'INT');
	$rremember = sed_import('rremember', 'P', 'BOL');
	if(empty($rremember) && $rcookiettl > 0) $rremember = true;
	$rmdpass  = md5($rpassword);

	$sql = sed_sql_query("SELECT user_id, user_maingrp, user_banexpire, user_skin, user_theme, user_lang FROM $db_users WHERE user_password='$rmdpass' AND user_name='".sed_sql_prep($rusername)."'");

	if ($row = sed_sql_fetcharray($sql))
	{
		if ($row['user_maingrp']==-1)
		{
			sed_log("Log in attempt, user inactive : ".$rusername, 'usr');
			sed_redirect(sed_url('message', 'msg=152', '', true));
			exit;
		}
		if ($row['user_maingrp']==2)
		{
			sed_log("Log in attempt, user inactive : ".$rusername, 'usr');
			sed_redirect(sed_url('message', 'msg=152', '', true));
			exit;
		}
		elseif ($row['user_maingrp']==3)
		{
			if ($sys['now'] > $row['user_banexpire'] && $row['user_banexpire']>0)
			{
				$sql = sed_sql_query("UPDATE $db_users SET user_maingrp='4' WHERE user_id={$row['user_id']}");
			}
			else
			{
				sed_log("Log in attempt, user banned : ".$rusername, 'usr');
				sed_redirect(sed_url('message', 'msg=153&num='.$row['user_banexpire'], '', true));
				exit;
			}
		}

		$ruserid = $row['user_id'];
		$rdefskin = $row['user_skin'];
		$rdeftheme = $row['user_theme'];

		$hashsalt = sed_unique(16);

		sed_sql_query("UPDATE $db_users SET user_lastip='{$usr['ip']}', user_lastlog = {$sys['now_offset']}, user_hashsalt = '$hashsalt' WHERE user_id={$row['user_id']}");

		$passhash = md5($rmdpass.$hashsalt);
		$u = base64_encode($ruserid.':_:'.$passhash);

		if($rremember)
		{
			sed_setcookie($sys['site_id'], $u, time()+$cfg['cookielifetime'], $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
		}
		else
		{
			$_SESSION[$sys['site_id']] = $u;
		}

		$_SESSION['saltstamp'] = $sys['now_offset'];

		/* === Hook === */
		$extp = sed_getextplugins('users.auth.check.done');
		if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		$sql = sed_sql_query("DELETE FROM $db_online WHERE online_userid='-1' AND online_ip='".$usr['ip']."' LIMIT 1");
		$ru = (empty($redirect)) ? sed_url('index') : base64_decode($redirect);
		header("Location: " . $ru);
		exit;
	}
	else
	{
		sed_shield_update(7, "Log in");
		sed_log("Log in failed, user : ".$rusername,'usr');
		sed_redirect(sed_url('message', 'msg=151', '', true));
		exit;
	}
}

else
{ unset($redir); }

/* === Hook === */
$extp = sed_getextplugins('users.auth.main');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if (empty($redirect))
 {
	sed_redirect(sed_url('users', 'm=auth&redirect='. base64_encode($sys['referer']), '', true));
	exit;
 }

$plug_head .= '<meta name="robots" content="noindex" />';
require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('users.auth'));


if ($cfg['maintenance'])
{
	$t-> assign(array("USERS_AUTH_MAINTENANCERES" => $cfg['maintenancereason']));
	$t->parse("MAIN.USERS_AUTH_MAINTENANCE");
}

$t->assign(array(
	"USERS_AUTH_TITLE" => $L['aut_logintitle'],
	"USERS_AUTH_SEND" => sed_url('users', 'm=auth&a=check&redirect='.$redirect),
	"USERS_AUTH_USER" => "<input type=\"text\" class=\"text\" name=\"rusername\" size=\"16\" maxlength=\"32\" />",
	"USERS_AUTH_PASSWORD" => "<input type=\"password\" class=\"password\" name=\"rpassword\" size=\"16\" maxlength=\"32\" />".$redir,
	"USERS_AUTH_REGISTER" => sed_url('users', 'm=register')
));

/* === Hook === */
$extp = sed_getextplugins('users.auth.tags');
if (is_array($extp))
{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once $cfg['system_dir'] . '/footer.php';

?>
