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

sed_check_xg();

/* === Hook === */
foreach (sed_getextplugins('users.logout') as $pl)
{
	include $pl;
}
/* ===== */

if ($usr['id'] > 0)
{
	sed_uriredir_apply($cfg['redirbkonlogout']);
}

if(!empty($_COOKIE[$sys['site_id']]))
{
	sed_setcookie($sys['site_id'], '', time()-63072000, $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
}

session_unset();
session_destroy();

if ($usr['id'] > 0)
{
	sed_sql_query("UPDATE $db_users SET user_lastvisit = {$sys['now_offset']} WHERE user_id = " . $usr['id']);
	sed_sql_query("DELETE FROM $db_online WHERE online_ip='{$usr['ip']}'");
	sed_uriredir_redirect(empty($redirect) ? sed_url('index') : base64_decode($redirect));
}
else
{
	sed_redirect(sed_url('index'));
}
?>