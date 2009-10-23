<?php
/**
 * Administration panel
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'any');
sed_block($usr['auth_read']);

$enabled[0] = $L['Disabled'];
$enabled[1] = $L['Enabled'];

$id = sed_import('id', 'G', 'TXT');
$po = sed_import('po', 'G', 'TXT');
$c = sed_import('c', 'G', 'TXT');
$p = sed_import('p', 'G', 'TXT');
$l = sed_import('l', 'G', 'TXT');
$o = sed_import('o', 'P', 'TXT');
$w = sed_import('w', 'P', 'TXT');
$u = sed_import('u', 'P', 'TXT');
$s = sed_import('s', 'G', 'ALP', 24);

/* === Hook for the plugins === */
$extp = sed_getextplugins('admin.main');
if (is_array($extp))
{
	foreach ($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}

$sys['inc'] = (empty($m)) ? 'admin.home' : "admin.$m";
$sys['inc'] = (empty($s)) ? $sys['inc'] : $sys['inc'].".$s";
$sys['inc'] = $cfg['system_dir'].'/core/admin/'.$sys['inc'].'.inc.php';

if (!file_exists($sys['inc']))
{
	sed_die();
}

$allow_img['0']['0'] = "<img src=\"images/admin/deny.gif\" alt=\"\" />";
$allow_img['1']['0'] = "<img src=\"images/admin/allow.gif\" alt=\"\" />";
$allow_img['0']['1'] = "<img src=\"images/admin/deny_locked.gif\" alt=\"\" />";
$allow_img['1']['1'] = "<img src=\"images/admin/allow_locked.gif\" alt=\"\" />";

$lincif_conf = sed_auth('admin', 'a', 'A');
$lincif_page = sed_auth('page', 'any', 'A');
$lincif_user = sed_auth('users', 'a', 'A');

require_once($sys['inc']);
$adminhelp = (empty($adminhelp)) ? $L['None'] : $adminhelp;

$title_tags[] = array('{ADMIN}');
$title_tags[] = array('%1$s');
$title_data = array($L['Administration']);
$out['subtitle'] = sed_title('{ADMIN}', $title_tags, $title_data);

require_once($cfg['system_dir'].'/header.php');

$t = new XTemplate(sed_skinfile('admin', false, true));

$t->assign(array(
	"ADMIN_TITLE" => sed_build_adminsection($adminpath),
	"ADMIN_SUBTITLE" => $adminsubtitle,
	"ADMIN_MAIN" => $adminmain,
	"ADMIN_HELP" => $adminhelp,
	"ADMINMENU_URL" => sed_url('admin'),
	"ADMINMENU_CONF_URL" => sed_url('admin', "m=config"),
	"ADMINMENU_PAGE_URL" => sed_url('admin', "m=page"),
	"ADMINMENU_FORUMS_URL" => sed_url('admin', "m=forums"),
	"ADMINMENU_USERS_URL" => sed_url('admin', "m=users"),
	"ADMINMENU_PLUG_URL" => sed_url('admin', "m=plug"),
	"ADMINMENU_TOOLS_URL" => sed_url('admin', "m=tools"),
	"ADMINMENU_TRASHCAN_URL" => sed_url('admin', "m=trashcan"),
	"ADMINMENU_OTHER_URL" => sed_url('admin', "m=other")
));

/* === Hook for the plugins === */
$extp = sed_getextplugins('admin.tags');
if (is_array($extp))
{
	foreach ($extp as $k => $pl)
	{
		include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php');
	}
}
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once($cfg['system_dir'].'/footer.php');

?>