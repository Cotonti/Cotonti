<?php
/**
 * Cotonti Extension Loader
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

define('SED_CODE', TRUE);

if (!file_exists('./datas/config.php'))
{
	header('Location: install.php');
	exit;
}

require_once './datas/config.php';

if ($cfg['new_install'])
{
	header('Location: install.php');
	exit;
}

require_once $cfg['system_dir'].'/functions.php';
require_once sed_incfile('obsolete'); // FIXME get rid of obsolete functions
require_once sed_incfile('common');
require_once sed_incfile('xtemplate');
require_once sed_incfile('parser'); // TODO module-dependent parser selection/loading

$z = sed_import('z', 'G', 'ALP');
$e = sed_import('e', 'G', 'ALP');
$o = sed_import('o', 'G', 'ALP');
$r = (isset($_POST['r'])) ? sed_import('r','P','ALP') : sed_import('r','G','ALP');
$c1= sed_import('c1', 'G', 'ALP');
$c2 = sed_import('c2', 'G', 'ALP');

if (!empty($z))
{
	define('COT_MODULE', true);
	$extname = $z;
	$exttype = 'module';
}
elseif (!empty($e))
{
	define('SED_PLUG', TRUE);
	$extname = $e;
	$exttype = 'plug';
}
elseif (!empty($o))
{
	define('SED_PLUG', TRUE);
	$extname = $o;
	$exttype = 'plug';
}
elseif (!empty($r))
{
	define('SED_PLUG', TRUE);
	$extname = $r;
	$exttype = 'plug';
}
else
{
	$z = 'index';
	define('COT_MODULE', true);
	$extname = $z;
	$exttype = 'module';
}

$req_files = array();
$req_files[] = sed_langfile($extname, $exttype);
$req_files[] = sed_incfile('config', $extname, $exttype == 'plug');
$req_files[] = sed_incfile('functions', $extname, $exttype == 'plug');
$req_files[] = sed_incfile('resources', $extname, $exttype == 'plug');
// $req_files += sed_get_requirements($extname, $exttype == 'plug');

foreach ($req_files as $req_file)
{
	if (file_exists($req_file))
	{
		require_once $req_file;
	}
}

if (SED_PLUG)
{
	sed_dieifdisabled($cfg['disable_plug']);
	
	$path_skin_ntg = sed_skinfile('plugin');
	$path_skin_def = $cfg['plugins_dir']."/$e/$e.tpl";
	$path_skin_alt = sed_skinfile($e, true);

	if (file_exists($path_skin_alt))
	{
		$path_skin= $path_skin_alt;
		$autoassigntags = FALSE;
	}
	elseif (file_exists($path_skin_def))
	{
		$path_skin = $path_skin_def;
		$autoassigntags = FALSE;
	}
	elseif (file_exists($path_skin_ntg))
	{
		$path_skin = $path_skin_ntg;
		$autoassigntags = TRUE;
	}
	else
	{
		sed_redirect(sed_url('message', 'msg=907', '', true));
	}

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('plug', $e);
	sed_block($usr['auth_read']);

	if (is_array($sed_plugins))
	{
		foreach ($sed_plugins['standalone'] as $k)
		{
			if ($k['pl_code'] == $e)
			{
				$out['subtitle'] = $k['pl_title'];
			}
		}
	}

	$t_plug = new XTemplate($path_skin);
	$t = $t_plug;

	$empty = true;

	if (is_array($sed_plugins['standalone']))
	{
		foreach ($sed_plugins['standalone'] as $k)
		{
			if ($k['pl_code'] == $e)
			{
				include $cfg['plugins_dir'].'/'.$k['pl_code'].'/'.$k['pl_file'].'.php';
				$empty = false;
			}
		}
	}

	if ($empty)
	{
		sed_redirect(sed_url('message', 'msg=907', '', true));
	}

	$out['subtitle'] = empty($out['subtitle']) ? $L['plu_title'] : $out['subtitle'];
	$sys['sublocation'] = $out['subtitle'];

	sed_online_update();

	require_once $cfg['system_dir'] . '/header.php';

	$t = $t_plug;

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

		$t->assign(array(
			'PLUGIN_TITLE' => sed_rc('plug_code_title', array('url' => sed_url('plug', "e=$e"))),
			'PLUGIN_SUBTITLE' => $plugin_subtitle,
			'PLUGIN_BODY' => $plugin_body
		));
	}

	$t->parse('MAIN');
	$t->out('MAIN');

	require_once $cfg['system_dir'] . '/footer.php';
}
elseif (!empty($o))
{
	$extp = array();
	if (is_array($sed_plugins))
	{
		foreach ($sed_plugins['popup'] as $k)
		{
			if ($k['pl_code'] == $o)
			{
				$extp[] = $cfg['plugins_dir'].'/'.$k['pl_code'].'/'.$k['pl_file'].'.php';
			}
		}
	}

	if (count($extp) == 0)
	{
		sed_redirect(sed_url('message', 'msg=907', '', true));
	}

	sed_sendheaders();

	$mskin = sed_skinfile(array('popup', $o));
	$t = new XTemplate($mskin);

	foreach ($extp as $pl)
	{
		include $pl;
	}

	$t->assign(array(
		'POPUP_METAS' => sed_htmlmetas(),
		'POPUP_JAVASCRIPT' => sed_javascript(),
		'POPUP_C1' => $c1,
		'POPUP_C2' => $c2,
		'POPUP_BODY' => $popup_body
	));

	$t->parse('MAIN');
	$t->out('MAIN');
}
elseif (!empty($r) && defined('SED_AJAX'))
{
	$empty = true;
	if (is_array($sed_plugins['ajax']))
	{
		foreach ($sed_plugins['ajax'] as $k)
		{
			if ($k['pl_code'] == $r)
			{
				include $cfg['plugins_dir'].'/'.$k['pl_code'].'/'.$k['pl_file'].'.php';
				$empty = false;
			}
		}
	}

	if ($empty)
	{
		sed_redirect(sed_url('message', 'msg=907', '', true));
	}
}
else
{
	sed_die();
}

?>