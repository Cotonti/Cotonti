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
$env['z'] = 'plug';
$env['location'] = 'plugins';

// Requirements
require_once './datas/config.php';
require_once $cfg['system_dir'] . '/functions.php';

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

require_once $cfg['system_dir'] . '/common.php';
require_once $cfg['system_dir'] . '/cotemplate.php';

// Initial permission check
list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', $env['ext']);
cot_block($usr['auth_read']);

// Input import
$e = cot_import('e', 'G', 'ALP');
$o = cot_import('o', 'G', 'ALP');
$r = (isset($_POST['r'])) ? cot_import('r','P','ALP') : cot_import('r','G','ALP');
$c1 = cot_import('c1', 'G', 'ALP');
$c2 = cot_import('c2', 'G', 'ALP');

if (!empty($e))
{
	$extname = $e;
    $exthook = 'standalone';
    $ext_display_header = true;
    $path_skin = cot_tplfile($extname, 'plug');
    $autoassigntags = false;
    if (!file_exists($path_skin))
    {
        $path_skin = cot_tplfile(array('plugin', $extname));
        $autoassigntags = true;
    }
}
elseif (!empty($o))
{
	$extname = $o;
    $exthook = 'popup';
    $ext_display_header = false;
    $path_skin = cot_tplfile(array('popup', $extname));
    $autoassigntags = true;
}
elseif (!empty($r))
{
	$extname = $r;
    $exthook = 'ajax';
    $ext_display_header = false;
    $path_skin = '';
    $autoassigntags = false;
}
else
{
	cot_die(true, true);
}

// Plugin requirements autoloading
$req_files = array();
$req_files[] = cot_langfile($extname, 'plug');
$req_files[] = cot_incfile($extname, 'plug', 'resources');
$req_files[] = cot_incfile($extname, 'plug', 'functions');

foreach ($req_files as $req_file)
{
	if (file_exists($req_file))
	{
		require_once $req_file;
	}
}

// Display

if (!empty($path_skin))
{
	$t = new XTemplate($path_skin);
}

$empty = true;

if (is_array($cot_plugins[$exthook]))
{
	foreach ($cot_plugins[$exthook] as $k)
	{
		if ($k['pl_code'] == $extname)
		{
			$out['subtitle'] = $k['pl_title'];
			include $k['pl_file'];
			$empty = false;
		}
	}
}

if ($empty)
{
	$env['status'] = '404 Not Found';
	cot_redirect(cot_url('message', 'msg=907', '', true));
}

$out['subtitle'] = empty($out['subtitle']) ? $L['plu_title'] : $out['subtitle'];
$sys['sublocation'] = $out['subtitle'];

if ($ext_display_header)
{
	$t_plug = $t;
	require_once $cfg['system_dir'] . '/header.php';
	$t = $t_plug;
}

if ($autoassigntags)
{
	$plugin_title = (empty($plugin_title)) ? $L['plu_title'] : $plugin_title;

	if ($cfg['homebreadcrumb'])
	{
		$bhome = $R['plug_code_homebreadcrumb'];
	}
	else
	{
		$bhome = '';
	}

	if (empty($o))
	{
		$t->assign(array(
			'PLUGIN_TITLE' => cot_rc('plug_code_title', array('url' => cot_url('plug', "e=$e"))),
			'PLUGIN_SUBTITLE' => $plugin_subtitle,
			'PLUGIN_BODY' => $plugin_body
		));
	}
	else
	{
		cot_sendheaders();

		$t->assign(array(
			'POPUP_C1' => $c1,
			'POPUP_C2' => $c2,
			'POPUP_BODY' => $popup_body
		));
	}
}

if (is_object($t))
{
	$t->parse('MAIN');
	$t->out('MAIN');
}

if ($ext_display_header)
{
	require_once $cfg['system_dir'] . '/footer.php';
}

?>