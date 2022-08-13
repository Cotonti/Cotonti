<?php
/**
 * Administration panel
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(cot::$usr['auth_read'], cot::$usr['auth_write'], cot::$usr['isadmin']) = cot_auth('admin', 'any');
cot_block(cot::$usr['auth_read']);

$enabled[0] = cot::$L['Disabled'];
$enabled[1] = cot::$L['Enabled'];

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
if (in_array($inc_file, $standard_admin) && file_exists(cot_incfile('admin', 'module', $inc_file))) {
	$inc_file = cot_incfile('admin', 'module', $inc_file);
} else {
	cot::$env['ext'] = $m;
	$adminsubtitle = $cot_modules[$m]['title'];
	$inc_file = cot::$cfg['modules_dir'] . "/$m/$m.admin.php";
}

if (!file_exists($inc_file))
{
	cot_die();
}

$allow_img['0']['0'] = cot::$R['admin_icon_deny'];
$allow_img['1']['0'] = cot::$R['admin_icon_allow'];
$allow_img['0']['1'] = cot::$R['admin_icon_deny_locked'];
$allow_img['1']['1'] = cot::$R['admin_icon_allow_locked'];

cot::$usr['admin_config'] = cot_auth('admin', 'a', 'A');
cot::$usr['admin_structure'] = cot_auth('structure', 'a', 'A');
cot::$usr['admin_users'] = cot_auth('users', 'a', 'A') || cot::$usr['maingrp'] == COT_GROUP_SUPERADMINS;

$adminpath = array(array(cot_url('admin'), cot::$L['Adminpanel']));
$adminhelp = isset($adminhelp) ? $adminhelp : '';
$adminmain = isset($adminmain) ? $adminmain : '';

require $inc_file;

$title_params = array(
	'ADMIN' => cot::$L['Administration'],
	'SUBTITLE' => $adminsubtitle
);
if(!isset($out['head'] )) $out['head']  = '';
$out['head'] .= cot::$R['code_noindex'];
$out['subtitle'] = empty($adminsubtitle) ? cot_title('{ADMIN}', $title_params) : cot_title('{SUBTITLE} - {ADMIN}', $title_params);

require_once cot::$cfg['system_dir'].'/header.php';

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

require_once cot::$cfg['system_dir'].'/footer.php';
