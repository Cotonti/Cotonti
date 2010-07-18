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
$z = sed_import('z', 'G', 'ALP');
$z = empty($z) ? 'index' : $z;
$e = sed_import('e', 'G', 'ALP');
$o = sed_import('o', 'G', 'ALP');
$r = (isset($_POST['r'])) ? sed_import('r','P','ALP') : sed_import('r','G','ALP');
$c1 = sed_import('c1', 'G', 'ALP');
$c2 = sed_import('c2', 'G', 'ALP');

require_once sed_incfile('common');
if ($cfg['enable_obsolete'])
{
    require_once sed_incfile('obsolete');
}
require_once sed_incfile('xtemplate');
require_once sed_incfile('parser'); // TODO module-dependent parser selection/loading

if (!empty($e))
{
	define('SED_PLUG', true);
	$extname = $e;
	$exttype = 'plug';
    $exthook = 'standalone';
    $ext_display_header = true;
    $path_skin = sed_skinfile($extname, true);
    $autoassigntags = false;
    if (!file_exists($path_skin))
    {
        $path_skin = sed_skinfile(array('plugin', $extname));
        $autoassigntags = true;
    }
}
elseif (!empty($o))
{
	define('SED_PLUG', true);
	$extname = $o;
	$exttype = 'plug';
    $exthook = 'popup';
    $ext_display_header = false;
    $path_skin = sed_skinfile(array('popup', $extname));
    $autoassigntags = true;
}
elseif (!empty($r))
{
	define('SED_PLUG', true);
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

if (COT_MODULE)
{
    if (is_array($sed_modules[$extname]))
    {
        $out['subtitle'] = $sed_modules[$extname]['title'];
        include $cfg['modules_dir'] . '/' . $extname . '/' . $extname . '.php';
        $empty = false;
    }
    else
	{
		sed_redirect(sed_url('message', 'msg=907', '', true));
	}
}
elseif (SED_PLUG)
{
	sed_dieifdisabled($cfg['disable_plug']);

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('plug', $extname);
	sed_block($usr['auth_read']);

    if (!empty($path_skin))
    {
        $t_plug = new XTemplate($path_skin);
        $t = $t_plug;
    }

	$empty = true;

	if (is_array($sed_plugins[$exthook]))
	{
		foreach ($sed_plugins[$exthook] as $k)
		{
			if ($k['pl_code'] == $extname)
			{
                $out['subtitle'] = $k['pl_title'];
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
                'PLUGIN_TITLE' => sed_rc('plug_code_title', array('url' => sed_url('plug', "e=$e"))),
                'PLUGIN_SUBTITLE' => $plugin_subtitle,
                'PLUGIN_BODY' => $plugin_body
            ));
        }
        else
        {
            sed_sendheaders();

            $t->assign(array(
                'POPUP_JAVASCRIPT' => sed_javascript(),
                'POPUP_C1' => $c1,
                'POPUP_C2' => $c2,
                'POPUP_BODY' => $popup_body
            ));
        }
	}

	$t->parse('MAIN');
	$t->out('MAIN');

    if ($ext_display_header)
    {
        require_once $cfg['system_dir'] . '/footer.php';
    }
}
else
{
	sed_die();
}

?>