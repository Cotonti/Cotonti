<?php
/**
 * User Logout
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

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
	$db->update($db_users, array('user_lastvisit' => $sys['now_offset']), "user_id = " . $usr['id']);
	$db->delete($db_online, "online_ip='{$usr['ip']}'");
	cot_uriredir_redirect(empty($redirect) ? cot_url('index') : base64_decode($redirect));
}
else
{
	cot_redirect(cot_url('index'));
}
?>