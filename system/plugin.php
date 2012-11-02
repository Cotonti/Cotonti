<?php
/**
 * Plugin loader
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Bootstrap
require_once $cfg['system_dir'] . '/common.php';

// Input import
$e = cot_import('e', 'G', 'ALP');
$o = cot_import('o', 'G', 'ALP');
$r = (isset($_POST['r'])) ? cot_import('r','P','ALP') : cot_import('r','G','ALP');
$c1 = cot_import('c1', 'G', 'ALP');
$c2 = cot_import('c2', 'G', 'ALP');


if (!empty($o))
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
elseif (!empty($e))
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
else
{
	cot_die_message(404);
}

if (!file_exists($cfg['plugins_dir'] . '/' . $extname))
{
	cot_die_message(404);
}

// Initial permission check
list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', $env['ext']);
cot_block($usr['auth_read']);

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
$pltitle = array();

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
			$out['plu_title'] = $k['pl_title'];
			include $cfg['plugins_dir'] . '/' . $k['pl_file'];
			$empty = false;
		}
	}
}

if ($empty)
{
	cot_die_message(907, TRUE);
}

if (empty($out['subtitle']))
{
	if (empty($L['plu_title']))
	{
		$L['plu_title'] = $L[$extname . '_title'];
	}
	$out['subtitle'] = empty($L['plu_title']) ? $out['plu_title'] : $L['plu_title'];
}
$sys['sublocation'] = $out['subtitle'];

if ($ext_display_header)
{
	$t_plug = $t;
	require_once $cfg['system_dir'] . '/header.php';
	$t = $t_plug;
}

if ($autoassigntags)
{
	array_unshift($pltitle, array(cot_url('plug', "e=$e"), $out['subtitle']));
	if (empty($o))
	{
		$t->assign(array(
			'PLUGIN_TITLE' => cot_breadcrumbs($pltitle, $cfg['homebreadcrumb']),
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
