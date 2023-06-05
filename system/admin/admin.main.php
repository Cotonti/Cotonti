<?php
/**
 * Administration panel
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('admin', 'any');
cot_block(Cot::$usr['auth_read']);

$enabled[0] = Cot::$L['Disabled'];
$enabled[1] = Cot::$L['Enabled'];

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
foreach (cot_getextplugins('admin.main') as $pl) {
	include $pl;
}
/* ===== */

$standard_admin = ['cache.disk', 'cache', 'config', 'extrafields', 'extensions', 'home', 'infos',
	'log', 'other', 'phpinfo', 'rights', 'rightsbyitem', 'structure', 'urls', 'users'];

$inc_file = (empty($m)) ? 'home' : $m;
$inc_file = (empty($s)) ? $inc_file : $inc_file.'.'.$s;
if (in_array($inc_file, $standard_admin) && file_exists(cot_incfile('admin', 'module', $inc_file))) {
	$inc_file = cot_incfile('admin', 'module', $inc_file);
} else {
	Cot::$env['ext'] = $m;
	$adminTitle = isset($cot_modules[$m]['title']) ? $cot_modules[$m]['title'] : '';
	$inc_file = Cot::$cfg['modules_dir'] . "/$m/$m.admin.php";
}

if (!file_exists($inc_file)) {
	cot_die();
}

$adminpath = array(array(cot_url('admin'), Cot::$L['Adminpanel']));
$adminhelp = isset($adminhelp) ? $adminhelp : '';
$adminmain = isset($adminmain) ? $adminmain : '';

require $inc_file;

$title_params = array(
	'ADMIN' => Cot::$L['Administration'],
	'SUBTITLE' => $adminTitle
);
if (!isset(Cot::$out['head'] )) {
    Cot::$out['head']  = '';
}
Cot::$out['head'] .= Cot::$R['code_noindex'];
Cot::$out['subtitle'] = empty($adminTitle) ?
    cot_title('{ADMIN}', $title_params) : cot_title('{SUBTITLE} - {ADMIN}', $title_params);

require_once Cot::$cfg['system_dir'].'/header.php';

$t = new XTemplate(cot_tplfile('admin', 'core'));

$t->assign(array(
	'ADMIN_BREADCRUMBS' => cot_breadcrumbs($adminpath, false),
	'ADMIN_TITLE' => $adminTitle,
	'ADMIN_MAIN' => $adminmain,
	'ADMIN_HELP' => $adminhelp
));

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.tags') as $pl) {
	include $pl;
}
/* ===== */
$t->parse('MAIN.BODY');
if(!COT_AJAX) {
	$t->parse('MAIN');
	$t->out('MAIN');
} else {
	$t->out('MAIN.BODY');
}

require_once Cot::$cfg['system_dir'].'/footer.php';
