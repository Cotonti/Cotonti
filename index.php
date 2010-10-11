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

define('COT_CODE', TRUE);

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
require_once $cfg['system_dir'] . '/common.php';
if ($cfg['enable_obsolete'])
{
    cot_require_api('obsolete');
}
cot_require_api('cotemplate');

$e = cot_import('e', 'G', 'ALP');
$o = cot_import('o', 'G', 'ALP');
$r = (isset($_POST['r'])) ? cot_import('r','P','ALP') : cot_import('r','G','ALP');
$c1 = cot_import('c1', 'G', 'ALP');
$c2 = cot_import('c2', 'G', 'ALP');

if (!empty($e))
{
	define('COT_PLUG', true);
	$extname = $e;
	$exttype = 'plug';
    $exthook = 'standalone';
    $ext_display_header = true;
    $path_skin = cot_skinfile($extname, true);
    $autoassigntags = false;
    if (!file_exists($path_skin))
    {
        $path_skin = cot_skinfile(array('plugin', $extname));
        $autoassigntags = true;
    }
}
elseif (!empty($o))
{
	define('COT_PLUG', true);
	$extname = $o;
	$exttype = 'plug';
    $exthook = 'popup';
    $ext_display_header = false;
    $path_skin = cot_skinfile(array('popup', $extname));
    $autoassigntags = true;
}
elseif (!empty($r))
{
	define('COT_PLUG', true);
	$extname = $r;
	$exttype = 'plug';
    $exthook = 'ajax';
    $ext_display_header = false;
    $path_skin = '';
    $autoassigntags = false;
}
else
{
	define('COT_MODULE', true);
	$extname = $z;
	$exttype = 'module';
}

$req_files = array();
$req_files[] = cot_langfile($extname, $exttype);
$req_files[] = cot_incfile($extname, 'functions', $exttype == 'plug');
$req_files[] = cot_incfile($extname, 'resources', $exttype == 'plug');

foreach ($req_files as $req_file)
{
	if (file_exists($req_file))
	{
		require_once $req_file;
	}
}

if (defined('COT_MODULE'))
{
    if (is_array($cot_modules[$extname]))
    {
        $out['subtitle'] = $cot_modules[$extname]['title'];
        include $cfg['modules_dir'] . '/' . $extname . '/' . $extname . '.php';
        $empty = false;
    }
    else
	{
		$env['status'] = '404 Not Found';
		cot_redirect(cot_url('message', 'msg=907', '', true));
	}
}
elseif (defined('COT_PLUG'))
{
	cot_dieifdisabled($cfg['disable_plug']);

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('plug', $extname);
	cot_block($usr['auth_read']);

    if (!empty($path_skin))
    {
        $t_plug = new XTemplate($path_skin);
        $t = $t_plug;
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

	cot_online_update();

    if ($ext_display_header)
    {
        require_once $cfg['system_dir'] . '/header.php';
    }

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
                'POPUP_JAVASCRIPT' => cot_javascript(),
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
}
else
{
	cot_die();
}

?>
