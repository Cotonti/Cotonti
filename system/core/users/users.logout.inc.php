<?php
/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=users.logout.inc.php
Version=101
Updated=2006-mar-15
Type=Core
Author=Neocrome
Description=User authication
[END_SED]
==================== */

defined('SED_CODE') or die('Wrong URL');

if (!sed_check_xg())
{
	header('Location: ' . SED_ABSOLUTE_URL . sed_url('message', 'msg=951', '', TRUE));
	exit;
}

/* === Hook === */
$extp = sed_getextplugins('users.logout');
if (is_array($extp))
{ foreach ($extp as $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if ($usr['id'] > 0)
{
	sed_uriredir_apply($cfg['redirbkonlogout']);
}

if(!empty($_COOKIE[$sys['site_id']]))
{
	sed_setcookie($sys['site_id'], '', time()-63072000, $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
}

if (!empty($_SESSION[$sys['site_id']]))
{
	session_unset();
	session_destroy();
}

if ($usr['id'] > 0)
{
	$sql = sed_sql_query("DELETE FROM $db_online WHERE online_ip='{$usr['ip']}'");
	sed_redirect(empty($redirect) ? sed_url('index') : base64_decode($redirect));
	exit;
}
else
{
	sed_redirect(sed_url('index'));
	exit;
}
?>