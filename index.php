<?php
/**
 * Index loader
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\ErrorHandler;
use cot\exceptions\NotFoundHttpException;
use cot\extensions\ExtensionsDictionary;
use cot\router\Router;

if (php_sapi_name() == 'cli-server') {
	// Embedded PHP webserver routing
	$tmp = explode('?', $_SERVER['REQUEST_URI']);
	$REQUEST_FILENAME = mb_substr($tmp[0], 1);
	unset($tmp);
	if (file_exists($REQUEST_FILENAME) && !preg_match('#\.php$#', $REQUEST_FILENAME)) {
		// Transfer static file if exists
		return false;
	}
	// Language selector
	$langs = array_map(
        function ($dir) { return str_replace("lang/", "", $dir); },
		glob('lang/??', GLOB_ONLYDIR)
	);
	if (preg_match('#^(' . join('|', $langs) . ')/(.*)$#', $REQUEST_FILENAME, $mt)) {
		$REQUEST_FILENAME = $mt[2];
		$_GET['l'] = $mt[1];
	}
	// Sitemap shortcut
	if ($REQUEST_FILENAME === 'sitemap.xml') {
		$_GET['r'] = 'sitemap';
	}
	// Admin area and message are special scripts
	if (preg_match('#^admin/([a-z0-9]+)#', $REQUEST_FILENAME, $mt)) {
		$_GET['m'] = $mt[1];
		include 'admin.php';
		exit;
	}
	if (preg_match('#^(admin|login|message)(/|$)#', $REQUEST_FILENAME, $mt)) {
		include $mt[1].'.php';
		exit;
	}
	// PHP files have priority
	if (preg_match('#\.php$#', $REQUEST_FILENAME) && $REQUEST_FILENAME !== 'index.php') {
		include $REQUEST_FILENAME;
		exit;
	}
	// All the rest goes through standard rewrite gateway
	if ($REQUEST_FILENAME !== 'index.php') {
		$_GET['rwr'] = $REQUEST_FILENAME;
	}
	unset($REQUEST_FILENAME, $langs, $mt);
}

// Redirect to install if config is missing
if (!file_exists('./datas/config.php')) {
    if (file_exists('install.php')) {
        header('Location: install.php');
        exit;
    }

    $message_body = '<p><em>'.@date('Y-m-d H:i').'</em></p>';
    $message_body .= "<p>File 'install.php' not found</p>";
    echo $message_body;
    http_response_code(500);
	exit (1);
}

// Let the include files know that we are Cotonti
const COT_CODE = true;

// Load vital core configuration from file
require_once './datas/config.php';

// If it is a new install, redirect
if (isset($cfg['new_install']) && $cfg['new_install']) {
	header('Location: install.php');
	exit;
}

// Load the Core API
require_once $cfg['system_dir'] . '/functions.php';

// Bootstrap
require_once $cfg['system_dir'] . '/common.php';

try {
    Cot::$currentRoute = Router::getInstance()->route();

    if (Cot::$currentRoute === null) {
        throw new NotFoundHttpException();
    }

    // Load the requested extension
    if (Cot::$env['type'] === ExtensionsDictionary::TYPE_PLUGIN) {
        require_once Cot::$cfg['system_dir'] . '/plugin.php';
    } elseif (!empty(Cot::$currentRoute->includeFiles)) {
        foreach (Cot::$currentRoute->includeFiles as $includeFile) {
            require_once $includeFile;
        }
    } elseif (Cot::$currentRoute->controller !== null && Cot::$currentRoute->action !== null) {
        $resultContent = Cot::$currentRoute->controller->runAction(Cot::$currentRoute->action);

        require_once Cot::$cfg['system_dir'] . '/header.php';

        echo $resultContent;
        unset($resultContent);

        require_once Cot::$cfg['system_dir'] . '/footer.php';
    }
} catch (Throwable $e) {
    // Handle error
    if (!ErrorHandler::getInstance()->handle($e)) {
        throw $e;
    }
}