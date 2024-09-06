<?php
/**
 * Plugin loader
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

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
    $hook = 'popup';
    Cot::$sys['displayHeader'] = Cot::$sys['displayFooter'] = false;
    $templateFile = cot_tplfile(['popup', $extensionCode]);
    $autoAssignTags = true;

} elseif (!empty($r)) {
	$extensionCode = $r;
    $hook = 'ajax';
    Cot::$sys['displayHeader'] = Cot::$sys['displayFooter'] = false;

} elseif (!empty($e)) {
	$extensionCode = $e;
    $hook = 'standalone';
    $templateFile = cot_tplfile($extensionCode, 'plug');
    if ($templateFile === null || !file_exists($templateFile)) {
        $templateFile = cot_tplfile(['plugin', $extensionCode]);
        $autoAssignTags = true;
    }
} else {
	cot_die_message(404);
}

if (!file_exists(Cot::$cfg['plugins_dir'] . '/' . $extensionCode)) {
	cot_die_message(404);
}

// Initial permission check
list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('plug', Cot::$env['ext']);
cot_block(Cot::$usr['auth_read']);

// Plugin requirements autoload
$requiredFiles = [
    cot_langfile($extensionCode, 'plug'),
    cot_incfile($extensionCode, 'plug', 'resources'),
    cot_incfile($extensionCode, 'plug', 'functions'),
];
foreach ($requiredFiles as $requiredFile) {
	if (file_exists($requiredFile)) {
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

$found = false;
if (!empty($cot_plugins[$hook]) && is_array($cot_plugins[$hook])) {
    if (Cot::$cfg['debug_mode']) {
        $cotHooksFired[] = $hook;
    }
	foreach ($cot_plugins[$hook] as $extensionRow) {
		if ($extensionRow['pl_code'] == $extensionCode) {
			$out['plu_title'] = $extensionRow['pl_title']; // @todo is it needed?
            $filename = Cot::$cfg['plugins_dir'] . '/' . $extensionRow['pl_file'];
            if (is_readable($filename)) {
                include Cot::$cfg['plugins_dir'] . '/' . $extensionRow['pl_file'];
                $found = true;
                break;
            }
		}
	}
}
if (!$found) {
	cot_die_message(404);
}

if (empty($out['subtitle'])) {
	if (empty(Cot::$L['plu_title']) && isset(Cot::$L[$extensionCode . '_title'])) {
        Cot::$L['plu_title'] = Cot::$L[$extensionCode . '_title'];
	}
    Cot::$out['subtitle'] = empty(Cot::$L['plu_title']) ? Cot::$out['plu_title'] : Cot::$L['plu_title'];
}
Cot::$sys['sublocation'] = Cot::$out['subtitle'];

if ($autoAssignTags) {
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
}

require_once Cot::$cfg['system_dir'] . '/footer.php';
