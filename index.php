<?php
/**
 * Index loader
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

// Redirect to install if config is missing
if (!file_exists('./datas/config.php'))
{
	header('Location: install.php');
	exit;
}

// Let the include files know that we are Cotonti
define('COT_CODE', true);

// Load vital core configuration from file
require_once './datas/config.php';

// If it is a new install, redirect
if (isset($cfg['new_install']) && $cfg['new_install'])
{
	header('Location: install.php');
	exit;
}

// Load the Core API, the template engine
require_once $cfg['system_dir'] . '/functions.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

// Bootstrap
require_once $cfg['system_dir'] . '/common.php';

// Support for ajax and popup hooked plugins
if (empty($_GET['e']) && !empty($_GET['r']))
{
	$_GET['e'] = $_GET['r'];
}
if (empty($_GET['e']) && !empty($_GET['o']))
{
	$_GET['e'] = $_GET['o'];
}

// Detect selected extension
if (empty($_GET['e']))
{
	// Default environment for index module
	define('COT_MODULE', true);
	$env['type'] = 'module';
	$env['ext'] = 'index';
}
else
{
	$found = false;
	if (preg_match('`^\w+$`', $_GET['e']))
	{
		$module_found = false;
		$plugin_found = false;
		if (file_exists($cfg['modules_dir'] . '/' . $_GET['e']))
		{
			$module_found = true;
			$found = true;
		}
		if (file_exists($cfg['plugins_dir'] . '/' . $_GET['e']))
		{
			$plugin_found = true;
			$found = true;
		}
		if ($module_found && $plugin_found)
		{
			// Need to query the db to check which one is installed
			$res = $db->query("SELECT ct_plug FROM $db_core WHERE ct_code = ? LIMIT 1", $_GET['e']);
			if ($res->rowCount() == 1)
			{
				if ((int)$res->fetchColumn())
				{
					$module_found = false;
				}
				else
				{
					$plugin_found = false;
				}
			}
			else
			{
				$found = false;
			}
		}
		if ($module_found)
		{
			$env['type'] = 'module';
			define('COT_MODULE', true);
		}
		elseif ($plugin_found)
		{
			$env['type'] = 'plug';
			$env['location'] = 'plugins';
			define('COT_PLUG', true);
		}
	}
	if ($found)
	{
		$env['ext'] = $_GET['e'];
	}
	else
	{
		// Error page
		cot_die_message(404);
		exit;
	}
}


// Load the requested extension
if ($env['type'] == 'plug')
{
	require_once $cfg['system_dir'] . '/plugin.php';
}
else
{
	require_once $cfg['modules_dir'] . '/' . $env['ext'] . '/' . $env['ext'] . '.php';
}

?>
