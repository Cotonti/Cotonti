<?php
/**
 * Plugin loader
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 * @deprecated Deprecated since Cotonti Siena
 */

// Set the environment
define('COT_CODE', true);
define('COT_PLUG', true);
$env['ext'] = 'plug';
$env['location'] = 'plugins';

// Requirements
require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

// Further environment setup
if (isset($_GET['e']))
{
	$env['ext'] = $_GET['e'];
}
elseif (isset($_GET['r']))
{
	$env['ext'] = $_GET['r'];
}
elseif (isset($_GET['o']))
{
	$env['ext'] = $_GET['o'];
}
else
{
	die();
}

require_once $cfg['system_dir'] . '/plugin.php';

?>