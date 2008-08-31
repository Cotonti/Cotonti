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

	$rusername = sed_import('rusername','P','TXT', 24, TRUE);
	$rpassword = sed_import('rpassword','P','PSW', 16, TRUE);
	$rcookiettl = sed_import('rcookiettl','P','INT');
	$rmdpass  = md5($rpassword);

	$sql = sed_sql_query("SELECT user_id, user_maingrp, user_banexpire, user_skin, user_lang FROM $db_users WHERE user_password='$rmdpass' AND user_name='".sed_sql_prep($rusername)."'");

	if ($row = sed_sql_fetcharray($sql))
		{
		if ($row['user_maingrp']==2)
			{
			sed_log("Log in attempt, user inactive : ".$rusername, 'usr');
			sed_redirect("message.php?msg=152");
			exit;
			}
	 	elseif ($row['user_maingrp']==3)
			{
			if ($sys['now'] > $row['user_banexpire'] && $row['user_banexpire']>0)
				{
				$sql = sed_sql_query("UPDATE $db_users SET user_maingrp='4' WHERE user_id='".$row['user_id']."'");
				}
		    else
		       	{
				sed_log("Log in attempt, user banned : ".$rusername, 'usr');
				sed_redirect("message.php?msg=153&amp;num=".$row['user_banexpire']);
				exit;
				}
			}

		$ruserid = $row['user_id'];
		$rdefskin = $row['user_skin'];

		sed_sql_query("UPDATE $db_users SET user_lastip='".$usr['ip']."' WHERE user_id='".$row['user_id']."' LIMIT 1");

		if ($rcookiettl>0 && ($cfg['authmode']==1 || $cfg['authmode']==3))
			{
			$rcookiettl = ($rcookiettl==0) ? 604800 : $rcookiettl;
			$rcookiettl = ($rcookiettl > $cfg['cookielifetime']) ? $cfg['cookielifetime'] : $rcookiettl;
			$u = base64_encode("$ruserid:_:$rmdpass:_:$rdefskin");
			setcookie("SEDITIO", "$u", time()+$rcookiettl, $cfg['cookiepath'], $cfg['cookiedomain']);
			}

		if ($cfg['authmode']==2 || $cfg['authmode']==3)
			{
			$_SESSION['rsedition'] = $ruserid;
			$_SESSION['rseditiop'] = $rmdpass;
			$_SESSION['rseditioq'] = $rdefskin;
			}

		/* === Hook === */
		$extp = sed_getextplugins('users.auth.check.done');
		if (is_array($extp))
			{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
		/* ===== */

		$sql = sed_sql_query("DELETE FROM $db_online WHERE online_userid='-1' AND online_ip='".$usr['ip']."' LIMIT 1");
		sed_redirect("message.php?msg=104&amp;redirect=".$redirect);
		exit;
		}
	else
		{
		sed_shield_update(7, "Log in");
		sed_log("Log in failed, user : ".$rusername,'usr');
		sed_redirect("message.php?msg=151");
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

require_once $cfg['system_dir'] . '/header.php';
$t = new XTemplate(sed_skinfile('users.auth'));

$t->assign(array(
	"USERS_AUTH_TITLE" => $L['aut_logintitle'],
	"USERS_AUTH_SEND" => "users.php?m=auth&amp;a=check&amp;redirect=".$redirect,
	"USERS_AUTH_USER" => "<input type=\"text\" class=\"text\" name=\"rusername\" size=\"16\" maxlength=\"32\" />",
	"USERS_AUTH_PASSWORD" => "<input type=\"password\" class=\"password\" name=\"rpassword\" size=\"16\" maxlength=\"32\" />".$redir,
	"USERS_AUTH_REGISTER" => "users.php?m=register"
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
