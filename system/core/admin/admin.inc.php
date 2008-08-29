<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=admin.inc.php
Version=110
Updated=2006-sep-01
Type=Core
Author=Neocrome
Description=Administration panel
[END_SED]
==================== */

if (!defined('SED_CODE') || !defined('SED_ADMIN')) { die('Wrong URL.'); }

$c = sed_import('c','G','TXT');
$id = sed_import('id','G','TXT');
$po = sed_import('po','G','TXT');
$p = sed_import('p','G','TXT');
$l = sed_import('l','G','TXT');
$o = sed_import('o','P','TXT');
$w = sed_import('w','P','TXT');
$u = sed_import('u','P','TXT');
$s = sed_import('s','G','ALP', 24);

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'any');
sed_block($usr['auth_read']);

$enabled[0] = $L['Disabled'];
$enabled[1] = $L['Enabled'];

/* === Hook for the plugins === */
$extp = sed_getextplugins('admin.main');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }

$sys['inc'] = (empty($m)) ? 'admin.home' : "admin.$m";
$sys['inc'] = (empty($s)) ? $sys['inc'] : $sys['inc'].".$s";
$sys['inc'] = $cfg['system_dir'].'/core/admin/'.$sys['inc'].'.inc.php';

if (!file_exists($sys['inc']))
	{ sed_die(); }

$allow_img['0']['0'] = "<img src=\"images/admin/deny.gif\" alt=\"\" />";
$allow_img['1']['0'] = "<img src=\"images/admin/allow.gif\" alt=\"\" />";
$allow_img['0']['1'] = "<img src=\"images/admin/deny_locked.gif\" alt=\"\" />";
$allow_img['1']['1'] = "<img src=\"images/admin/allow_locked.gif\" alt=\"\" />";

$adminmenu = "<table style=\"width:100%;\"><tr>";
$adminmenu .= "<td style=\"width:11%; text-align:center;\"><a href=\"admin.php\">";
$adminmenu .= "<img src=\"images/admin/admin.gif\" alt=\"\" /><br />".$L['Home']."</a></td>";
$adminmenu .= "<td style=\"width:12%; text-align:center;\">";
$adminmenu .= sed_linkif("admin.php?m=config", "<img src=\"images/admin/config.gif\" alt=\"\" /><br />".$L['Configuration'], sed_auth('admin', 'a', 'A'));
$adminmenu .= "</td>";
$adminmenu .= "<td style=\"width:11%; text-align:center;\">";
$adminmenu .= sed_linkif("admin.php?m=page", "<img src=\"images/admin/page.gif\" alt=\"\" /><br />".$L['Pages'], sed_auth('page', 'any', 'A'));
$adminmenu .= "</td>";
$adminmenu .= "<td style=\"width:11%; text-align:center;\">";
$adminmenu .= sed_linkif("admin.php?m=forums", "<img src=\"images/admin/forums.gif\" alt=\"\" /><br />".$L['Forums'], sed_auth('admin', 'a', 'A'));
$adminmenu .= "</td>";
$adminmenu .= "<td style=\"width:11%; text-align:center;\">";
$adminmenu .= sed_linkif("admin.php?m=users", "<img src=\"images/admin/users.gif\" alt=\"\" /><br />".$L['Users'], sed_auth('users', 'a', 'A'));
$adminmenu .= "</td>";
$adminmenu .= "<td style=\"width:11%; text-align:center;\">";
$adminmenu .= sed_linkif("admin.php?m=plug", "<img src=\"images/admin/plugins.gif\" alt=\"\" /><br />".$L['Plugins'], sed_auth('admin', 'a', 'A'));
$adminmenu .= "</td>";
$adminmenu .= "<td style=\"width:11%; text-align:center;\">";
$adminmenu .= sed_linkif("admin.php?m=tools", "<img src=\"images/admin/tools.gif\" alt=\"\" /><br />".$L['Tools'], sed_auth('admin', 'a', 'A'));
$adminmenu .= "</td>";
$adminmenu .= "<td style=\"width:11%; text-align:center;\">";
$adminmenu .= sed_linkif("admin.php?m=trashcan", "<img src=\"images/admin/delete.gif\" alt=\"\" /><br />".$L['Trashcan'], sed_auth('admin', 'a', 'A'));
$adminmenu .= "</td>";
$adminmenu .= "<td style=\"width:11%; text-align:center;\"><a href=\"admin.php?m=other\">";
$adminmenu .= "<img src=\"images/admin/folder.gif\" alt=\"\" /><br />".$L['Other']."</a></td>";
$adminmenu .= "</tr></table>";

require_once($sys['inc']);
$adminhelp = (empty($adminhelp)) ? $L['None'] : $adminhelp;

require_once($cfg['system_dir'].'/header.php');

$t = new XTemplate("skins/".$skin."/admin.tpl");

$t->assign(array(
	"ADMIN_TITLE" => sed_build_adminsection($adminpath),
	"ADMIN_SUBTITLE" => $adminsubtitle,
	"ADMIN_MENU" => $adminmenu,
	"ADMIN_MAIN" => $adminmain,
	"ADMIN_HELP" => $adminhelp,
		));

/* === Hook for the plugins === */
$extp = sed_getextplugins('admin.tags');
if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
/* ===== */

$t->parse("MAIN");
$t->out("MAIN");

require_once($cfg['system_dir'].'/footer.php');

?>