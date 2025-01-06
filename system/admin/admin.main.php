<?php
/**
 * Administration panel
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\router\Router;

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

$adminPath = [[cot_url('admin'), Cot::$L['Adminpanel']]];
$adminTitle = '';
$adminHelp = '';
$adminMain =  '';

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.main') as $pl) {
	include $pl;
}
/* ===== */

$route = Router::getInstance()->routeAdmin();

if (!empty($route->extensionCode)) {
    // Check the extension administration rights
    list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin'])
        = cot_auth($route->extensionCode, 'any');
    cot_block(Cot::$usr['isadmin']);

    $adminTitle = $cot_modules[$route->extensionCode]['title'] ?? '';
}
if (!empty($route->includeFiles)) {
    foreach ($route->includeFiles as $includeFile) {
        require_once $includeFile;
    }
} elseif ($route->controller !== null && $route->action !== null) {
    $adminMain = $route->controller->runAction($route->action);
}
unset($route);

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