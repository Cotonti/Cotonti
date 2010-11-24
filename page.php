<?php
/**
 * Page module main
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

define('COT_CODE', true);

// Basic requirements
require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

// Environment setup
define('COT_PAGES', TRUE);
$env['ext'] = 'page';
$env['location'] = 'pages';

// Additional API requirements
require_once cot_incfile('extrafields');
require_once cot_incfile('users', 'module');

// Self requirements
require_once cot_incfile('page', 'module');

// Mode choice
if (!in_array($m, array('add', 'edit')))
{
	if (isset($_GET['c']))
	{
		$m = 'list';
	}
	else
	{
		$m = 'main';
	}
}

require_once cot_incfile('page', 'module', $m);

?>