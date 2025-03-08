<?php
/**
 * Plugin loader
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\extensions\ExtensionsDictionary;
use cot\extensions\ExtensionsHelper;

defined('COT_CODE') or die('Wrong URL');

// Input import
$e = cot_import('e', 'G', 'ALP');
$o = cot_import('o', 'G', 'ALP');
$r = (isset($_POST['r'])) ? cot_import('r','P','ALP') : cot_import('r','G','ALP');
$c1 = cot_import('c1', 'G', 'ALP');
$c2 = cot_import('c2', 'G', 'ALP');

$autoAssignTags = false;
$templateFile = '';
if (!empty($o)) {
	$extensionCode = $o;
    Cot::$sys['displayHeader'] = Cot::$sys['displayFooter'] = false;
    $templateFile = cot_tplfile(['popup', $extensionCode]);
    $autoAssignTags = true;

} elseif (!empty($r)) {
	$extensionCode = $r;
    Cot::$sys['displayHeader'] = Cot::$sys['displayFooter'] = false;

} elseif (!empty($e)) {
	$extensionCode = $e;
    $templateFile = cot_tplfile($extensionCode, 'plug');
    if ($templateFile === null || !file_exists($templateFile)) {
        $templateFile = cot_tplfile(['plugin', $extensionCode]);
        $autoAssignTags = true;
    }
} else {
	cot_die_message(404);
}

// It may be unnecessary. The router has checked everything. But leave it just in case
if (!file_exists(Cot::$cfg['plugins_dir'] . '/' . $extensionCode)) {
	cot_die_message(404);
}

// Initial permission check
list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('plug', Cot::$env['ext']);
cot_block(Cot::$usr['auth_read']);

// Plugin requirements autoload
$requiredFiles = [
    cot_langfile($extensionCode, ExtensionsDictionary::TYPE_PLUGIN),
    cot_incfile($extensionCode, ExtensionsDictionary::TYPE_PLUGIN, 'resources'),
    cot_incfile($extensionCode, ExtensionsDictionary::TYPE_PLUGIN, 'functions'),
];
foreach ($requiredFiles as $requiredFile) {
	if (!empty($requiredFile) && file_exists($requiredFile)) {
		require_once $requiredFile;
	}
}

// Display
$pluginBreadCrumbs = [];
$pluginTitle = '';
$pluginSubtitle = '';
$pluginBody = '';
if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.25
    $plugin_subtitle = '';
    $plugin_body = '';
    $popup_body = '';
}

$t = null;
if (!empty($templateFile)) {
	$t = new XTemplate($templateFile);
}

if (!empty(Cot::$currentRoute->includeFiles)) {
    foreach (Cot::$currentRoute->includeFiles as $includeFile) {
        require_once $includeFile;
    }
} elseif (Cot::$currentRoute->controller !== null && Cot::$currentRoute->action !== null) {
    $pluginBody = Cot::$currentRoute->controller->runAction(Cot::$currentRoute->action);
}

if (empty($out['subtitle'])) {
    Cot::$out['subtitle'] = ExtensionsHelper::getInstance()
        ->getTitle($extensionCode, ExtensionsDictionary::TYPE_PLUGIN);
}
Cot::$sys['sublocation'] = Cot::$out['subtitle'];

if ($autoAssignTags && !empty($t)) {
	array_unshift($pluginBreadCrumbs, [cot_url($e), Cot::$out['subtitle']]);
	if (empty($o)) {
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            // @deprecated in 0.9.25
            if (!empty($plugin_subtitle)) {
                $pluginTitle = $plugin_subtitle;
            }
            if (!empty($plugin_body)) {
                $pluginBody = $plugin_body;
            }
        }
		$t->assign([
			'BREADCRUMBS' => cot_breadcrumbs($pluginBreadCrumbs, Cot::$cfg['homebreadcrumb']),
			'TITLE' => $pluginTitle,
            'SUBTITLE' => $pluginSubtitle,
			'BODY' => $pluginBody,
		]);
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            // @deprecated in 0.9.25
            $t->assign([
                'PLUGIN_TITLE' => cot_breadcrumbs($pluginBreadCrumbs, Cot::$cfg['homebreadcrumb']),
                'PLUGIN_SUBTITLE' => $pluginTitle,
                'PLUGIN_BODY' => $pluginBody,
            ]);
        }
	} else {
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            // @deprecated in 0.9.25
            if (!empty($popup_body)) {
                $pluginBody = $popup_body;
            }
        }

		cot_sendheaders();
		$t->assign([
			'POPUP_C1' => $c1,
			'POPUP_C2' => $c2,
			'BODY' => $pluginBody,
		]);

        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            // @deprecated in 0.9.25
            $t->assign([
                'POPUP_BODY' => $pluginBody,
            ]);
        }
	}
}

$pluginTemplate = $t ?? null;
require_once Cot::$cfg['system_dir'] . '/header.php';
if (!empty($pluginTemplate)) {
    $t = $pluginTemplate;
}
unset($pluginTemplate);

if (isset($t) && is_object($t)) {
	$t->parse('MAIN');
	$t->out('MAIN');
} else {
    echo $pluginBody;
}

require_once Cot::$cfg['system_dir'] . '/footer.php';
