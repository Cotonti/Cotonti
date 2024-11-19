<?php
/**
 * Administration panel - Other Admin parts listing
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\extensions\ExtensionsDictionary;
use cot\extensions\ExtensionsService;
use cot\router\Router;

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

$t = new XTemplate(cot_tplfile('admin.other', 'core'));

$p = cot_import('p', 'G', 'ALP');

/* === Hook === */
foreach (cot_getextplugins('admin.other.first') as $extension) {
	include $extension;
}
/* ===== */

if (!empty($p)) {
    $route = Router::getInstance()->routeAdminOther($p);

	list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('plug', $p);
	cot_block(Cot::$usr['isadmin']);

    if (file_exists(cot_langfile($p, ExtensionsDictionary::TYPE_PLUGIN))) {
        require_once cot_langfile($p, ExtensionsDictionary::TYPE_PLUGIN);
    }

    $extInfo = cot_get_extensionparams($p, false);
    $adminTitle = $extInfo['name'];

    $adminPath = [
        [cot_url('admin', ['m' => 'extensions']), Cot::$L['Extensions']],
        [cot_url('admin', ['m' => 'extensions', 'a' => 'details', 'pl' => $p]), $adminTitle],
        [cot_url('admin', ['m' => 'other', 'p' => $p]), Cot::$L['Administration']],
    ];

    $adminMain = '';
    $legacyMode = isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode'];

    if (!empty($route->includeFiles)) {
        foreach ($route->includeFiles as $includeFile) {
            if ($legacyMode) {
                /** @deprecated in 0.9.25 */
                $plugin_body = '';
            }

            $pluginBody = '';

            include_once $includeFile;

            $adminMain .= $pluginBody;

            if ($legacyMode) {
                // @deprecated in 0.9.25
                $adminMain .= $plugin_body;
            }
        }
    } elseif ($route->controller !== null && $route->action !== null) {
        $adminMain = $route->controller->runAction($route->action);
    }
    unset($route);
} else {
	$adminPath[] = [cot_url('admin', ['m' => 'other']), Cot::$L['Other']];
	$adminTitle = Cot::$L['Other'];
	list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('admin', 'a');
	cot_block(Cot::$usr['auth_read']);

	$target = [];

    $extensionsService = ExtensionsService::getInstance();

	foreach ([ExtensionsDictionary::TYPE_MODULE, ExtensionsDictionary::TYPE_PLUGIN] as $type) {
		if ($type === ExtensionsDictionary::TYPE_MODULE) {
			$list = $extensionsService->getModulesList();
			$title = Cot::$L['Modules'];
		} else {
            $list = $extensionsService->getPluginsList();
			$title = Cot::$L['Plugins'];
		}

		if (!empty($list) && is_array($list)) {
			usort(
                $list,
                function (array $a, array $b): int {
                    if ($a['code'] === $b['code']) {
                        return 0;
                    }
                    return ($a['code'] < $b['code']) ? -1 : 1;
                }
            );
			foreach ($list as $extension) {
                $adminPartUrl = $extensionsService->getAdminPageUrl($extension['code'], $type);
                if (empty($adminPartUrl)) {
                    continue;
                }
				$extensionInfo = cot_get_extensionparams(
                    $extension['code'], $type === ExtensionsDictionary::TYPE_MODULE
                );
				$t->assign([
					'ADMIN_OTHER_EXT_URL' => $adminPartUrl,
					'ADMIN_OTHER_EXT_ICON' => $extensionInfo['icon'],
					'ADMIN_OTHER_EXT_NAME' => $extensionInfo['name'],
					'ADMIN_OTHER_EXT_DESC' => $extensionInfo['desc'],
				]);
                if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
                    $t->assign([
                        // @deprecated For backward compatibility. Will be removed in future releases
                        'ADMIN_OTHER_EXT_ICO' => $extensionInfo['legacyIcon'],
                    ]);
                }

				$t->parse('MAIN.SECTION.ROW');
			}
		} else {
			$t->parse('MAIN.SECTION.EMPTY');
		}
		$t->assign('ADMIN_OTHER_SECTION', $title);
		$t->parse('MAIN.SECTION');
	}

	$t->assign([
		'ADMIN_OTHER_URL_CACHE' => cot_url('admin', 'm=cache'),
		'ADMIN_OTHER_URL_DISKCACHE' => cot_url('admin', 'm=cache&s=disk'),
		'ADMIN_OTHER_URL_EXFLDS' => cot_url('admin', 'm=extrafields'),
		'ADMIN_OTHER_URL_STRUCTURE' => cot_url('admin', 'm=structure'),
		'ADMIN_OTHER_URL_LOG' => cot_url('admin', 'm=log'),
		'ADMIN_OTHER_URL_INFOS' => cot_url('admin', 'm=infos'),
        'ADMIN_OTHER_URL_PHPINFO' => cot_url('admin', 'm=phpinfo'),
	]);

	/* === Hook === */
	foreach (cot_getextplugins('admin.other.tags') as $extension) {
		include $extension;
	}
	/* ===== */

	$t->parse('MAIN');
	$adminMain = $t->text('MAIN');
}
