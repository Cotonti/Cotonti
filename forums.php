<?php
/**
 * Forums module main
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

// Environment setup
define('COT_CODE', true);
define('COT_MODULE', true);
define('COT_FORUMS', true);
$env['ext'] = 'forums';
$env['location'] = 'forums';

// Basic requirements
require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

// Additional requirements
require_once cot_incfile('extrafields');
require_once cot_incfile('users', 'module');

// Self requirements
require_once cot_incfile('forums', 'module');

// Mode choice
if (!in_array($m, array('topics', 'posts', 'editpost', 'newtopic')))
{
	$m = 'sections';
}

include cot_incfile('forums', 'module', $m);

?>