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

$standardAdmin = [
    'cache.disk',
    'cache',
    'config',
    'extrafields',
    'extensions',
    'home',
    'infos',
    'log',
    'other',
    'phpinfo',
    'rights',
    'rightsbyitem',
    'structure',
    'urls',
    'users'
];

$includeFile = (empty($m)) ? 'home' : $m;
$includeFile = (empty($s)) ? $includeFile : $includeFile . '.' . $s;
$standardIncFile = cot_incfile('admin', 'module', $includeFile);
if (in_array($includeFile, $standardAdmin) && file_exists($standardIncFile)) {
	$includeFile = $standardIncFile;
} else {
	Cot::$env['ext'] = $m;
	$adminTitle = isset($cot_modules[$m]['title']) ? $cot_modules[$m]['title'] : '';
    $hook = 'admin';
    if (!empty($cot_plugins[$hook]) && is_array($cot_plugins[$hook])) {
        if (Cot::$cfg['debug_mode']) {
            $cotHooksFired[] = $hook;
        }
        foreach ($cot_plugins[$hook] as $extensionRow) {
            if ($extensionRow['pl_code'] === $m) {
                $extensionDirectory = $extensionRow['pl_module'] ? Cot::$cfg['modules_dir'] : Cot::$cfg['plugins_dir'];
                $includeFile = $extensionDirectory . "/{$extensionRow['pl_file']}";
                break;
            }
        }
    }
	$includeFile = Cot::$cfg['modules_dir'] . "/$m/$m.admin.php";
}

if (!file_exists($includeFile)) {
	cot_die_message(404);
}

$adminPath = [[cot_url('admin'), Cot::$L['Adminpanel']]];
$adminTitle = isset($adminTitle) ? $adminTitle : '';
$adminHelp = isset($adminHelp) ? $adminHelp : '';
$adminMain = isset($adminMain) ? $adminMain : '';

require $includeFile;

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.24 (for backward compatibility)
    if (!empty($adminhelp)) {
        $adminHelp = $adminhelp;
    }
    if (!empty($adminmain)) {
        $adminMain = $adminmain;
    }
    if (!empty($adminpath)) {
        $adminPath = $adminpath;
    }
}

$titleParams = [
	'ADMIN' => Cot::$L['Administration'],
	'SUBTITLE' => $adminTitle,
];
if (!isset(Cot::$out['head'] )) {
    Cot::$out['head']  = '';
}
Cot::$out['head'] .= Cot::$R['code_noindex'];
Cot::$out['subtitle'] = empty($adminTitle)
    ? cot_title('{ADMIN}', $titleParams)
    : cot_title('{SUBTITLE} - {ADMIN}', $titleParams);

require_once Cot::$cfg['system_dir'] . '/header.php';

$t = new XTemplate(cot_tplfile('admin', 'core'));

$t->assign([
	'ADMIN_BREADCRUMBS' => cot_breadcrumbs($adminPath, false),
	'ADMIN_TITLE' => $adminTitle,
	'ADMIN_MAIN' => $adminMain,
	'ADMIN_HELP' => $adminHelp,
]);

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.tags') as $pl) {
	include $pl;
}
/* ===== */

$t->parse('MAIN.BODY');
if (!COT_AJAX) {
	$t->parse('MAIN');
	$t->out('MAIN');
} else {
	$t->out('MAIN.BODY');
}

require_once Cot::$cfg['system_dir'] . '/footer.php';