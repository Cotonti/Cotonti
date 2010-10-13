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

defined('COT_CODE') or die('Wrong URL');

cot_check_xg();

/* === Hook === */
foreach (cot_getextplugins('users.logout') as $pl)
{
	include $pl;
}
/* ===== */

if ($usr['id'] > 0)
{
	cot_uriredir_apply($cfg['redirbkonlogout']);
}

if(!empty($_COOKIE[$sys['site_id']]))
{
	cot_setcookie($sys['site_id'], '', time()-63072000, $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
}

session_unset();
session_destroy();

if ($usr['id'] > 0)
{
	$cot_db->query("UPDATE $db_users SET user_lastvisit = {$sys['now_offset']} WHERE user_id = " . $usr['id']);
	$cot_db->query("DELETE FROM $db_online WHERE online_ip='{$usr['ip']}'");
	cot_uriredir_redirect(empty($redirect) ? cot_url('index') : base64_decode($redirect));
}
else
{
	cot_redirect(cot_url('index'));
}
?>