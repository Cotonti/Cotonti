<?php
/**
 * Administration panel
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'any');
cot_block($usr['auth_read']);

$enabled[0] = $L['Disabled'];
$enabled[1] = $L['Enabled'];

$id = cot_import('id', 'G', 'TXT');
$po = cot_import('po', 'G', 'TXT');
$c = cot_import('c', 'G', 'TXT');
$p = cot_import('p', 'G', 'TXT');
$l = cot_import('l', 'G', 'TXT');
$o = cot_import('o', 'P', 'TXT');
$w = cot_import('w', 'P', 'TXT');
$u = cot_import('u', 'P', 'TXT');
$s = cot_import('s', 'G', 'ALP', 24);

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.main') as $pl)
{
	include $pl;
}
/* ===== */

$standard_admin = array('cache.disk', 'cache', 'config', 'extrafields', 'home', 'infos',
	'log', 'other', 'extensions', 'rights', 'rightsbyitem', 'structure', 'urls', 'users');

$inc_file = (empty($m)) ? 'home' : $m;
$inc_file = (empty($s)) ? $inc_file : $inc_file.'.'.$s;
if (in_array($inc_file, $standard_admin) && file_exists(cot_incfile('admin', 'module', $inc_file)))
{
	$inc_file = cot_incfile('admin', 'module', $inc_file);
}
else
{
	$inc_file = $cfg['modules_dir'] . "/$m/$m.admin.php";
}

if (!file_exists($inc_file))
{
	cot_die();
}

$allow_img['0']['0'] = $R['admin_icon_deny'];
$allow_img['1']['0'] = $R['admin_icon_allow'];
$allow_img['0']['1'] = $R['admin_icon_deny_locked'];
$allow_img['1']['1'] = $R['admin_icon_allow_locked'];

$usr['admin_config'] = cot_auth('admin', 'a', 'A');
$usr['admin_structure'] = cot_auth('structure', 'a', 'A');
$usr['admin_users'] = cot_auth('users', 'a', 'A');

$adminpath = array(array(cot_url('admin'), $L['Adminpanel']));

require $inc_file;

$adminhelp = (empty($adminhelp)) ? '' : $adminhelp;

$title_params = array(
	'ADMIN' => $L['Administration']
);
$out['head'] .= $R['code_noindex'];
$out['subtitle'] = cot_title('{ADMIN}', $title_params);

require_once $cfg['system_dir'].'/header.php';

$t = new XTemplate(cot_tplfile('admin', 'core'));

$t->assign(array(
	'ADMIN_TITLE' => cot_breadcrumbs($adminpath, false),
	'ADMIN_SUBTITLE' => $adminsubtitle,
	'ADMIN_MAIN' => $adminmain,
	'ADMIN_HELP' => $adminhelp
));

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.tags') as $pl)
{
	include $pl;
}
/* ===== */
$t->parse('MAIN.BODY');
if(!COT_AJAX)
{
	$t->parse('MAIN');
	$t->out('MAIN');
}
else
{
	$t->out('MAIN.BODY');
}

require_once $cfg['system_dir'].'/footer.php';

?>