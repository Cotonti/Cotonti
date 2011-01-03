<?php
/**
 * Private messages module
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

// Environment setup
define('COT_CODE', true);
define('COT_MODULE', true);
define('COT_PM', true);
$env['ext'] = 'pm';
$env['location'] = 'private_messages';

// Basic requirements
require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

// Additional API requirements
require_once cot_incfile('extrafields');
require_once cot_incfile('users', 'module');

// Self requirements
require_once cot_incfile('pm', 'module');

// Mode choice
if (!in_array($m, array('send', 'message')))
{
	$m = 'list';
}

require_once cot_incfile('pm', 'module', $m);
?>