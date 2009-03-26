<?PHP

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

if (!defined('SED_CODE')) { die('Wrong URL.'); }

sed_check_xg();

/* === Hook === */
$extp = sed_getextplugins('users.logout');
if (is_array($extp))
{ foreach ($extp as $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

if(!empty($_COOKIE[$sys['site_id']]))
{
	sed_setcookie($sys['site_id'], '', time()-63072000, $cfg['cookiepath'], $cfg['cookiedomain'], $sys['secure'], true);
}

if (!empty($_SESSION[$sys['site_id']]))
{
	session_unset();
	session_destroy();
}

if ($usr['id']>0)
{
	$sql = sed_sql_query("DELETE FROM $db_online WHERE online_ip='{$usr['ip']}'");
	sed_redirect(sed_url('message', 'msg=102', '', true));
	exit;
}
else
{
	sed_redirect(sed_url('message', 'msg=101', '', true));
	exit;
}

?>
