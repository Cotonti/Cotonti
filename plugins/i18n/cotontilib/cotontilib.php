<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 *  Cotonti Lib plugin for Cotonti Siena
 *
 * @package Cotonti Lib
 * @author Kalnov Alexey <kalnovalexey@yandex.ru>
 * @copyright © Portal30 Studio http://portal30.ru
 */
defined('COT_CODE') or die('Wrong URL.');

// Self requirements
//require_once cot_incfile(cot::$env['ext'], 'plug');

// Default controller
if (!$m) $m = 'Main';

$controllerName = cot::$env['ext'].'_controller_'.ucfirst($m);

if (class_exists($controllerName)) {

    /* Create the controller */
    $controller = new $controllerName();

    if(!$a) $a = cot_import('a', 'P', 'TXT');
    $action = $a;
    if(!empty($action) && mb_strpos($action, '-') !== false) {
        $action = explode('-', $action);
        $tmp = array_shift($action);
        $action = array_map('mb_ucfirst', $action);
        $action = $tmp.implode('', $action);
    }

    /* Perform the Request task */
    $currentAction = $action.'Action';
    if (!$a && method_exists($controller, 'indexAction')) {
        $outContent = $controller->indexAction();

    } elseif (method_exists($controller, $currentAction)) {
        $outContent = $controller->$currentAction();

    } else {
        // Error page
        cot_die_message(404);
        exit;
    }

    if (isset($outContent)){
        $plugin_body .= $outContent;
        unset($outContent);
    }

}else{
    // Error page
    cot_die_message(404);
    exit;
}

