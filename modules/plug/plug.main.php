<?PHP
/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
==================== */

/**
 * Plugin invokation module
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) 2008-2010 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

$p = sed_import('p','G','ALP');
$e = sed_import('e','G','ALP');
$o = sed_import('o','G','ALP');
$s = sed_import('s','G','ALP');
$r = sed_import('r','G','ALP');
$h = sed_import('h','G','ALP');
$c1= sed_import('c1','G','ALP');
$c2 = sed_import('c2','G','ALP');

unset ($plugin_title, $plugin_body);

if (!empty($e))
{
	$path_lang_def	= $cfg['plugins_dir']."/$e/lang/$e.en.lang.php";
	$path_lang_alt	= $cfg['plugins_dir']."/$e/lang/$e.$lang.lang.php";
	$path_skin_ntg	= sed_skinfile('plugin');
	$path_skin_def	= $cfg['plugins_dir']."/$e/$e.tpl";
	$path_skin_alt	= sed_skinfile($e, true);

	if (file_exists($path_lang_def))
	{
		require_once($path_lang_def);
	}
	if (file_exists($path_lang_alt) && $lang!='en')
	{
		require_once($path_lang_alt);
	}
	
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
		foreach($sed_plugins['standalone'] as $k)
		{
			if ($k['pl_code'] == $e)
			{
				$out['subtitle'] = $k['pl_title'];
			}
		}
	}

	/* ============= */

	$t_plug = new XTemplate($path_skin);
	$t = $t_plug;

	$empty = true;

	if (is_array($sed_plugins['standalone']))
	{
		foreach($sed_plugins['standalone'] as $k)
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

	require_once $cfg['system_dir'] . '/header.php';

	$t = $t_plug;

	if ($autoassigntags)
	{
		$plugin_title = (empty($plugin_title)) ? $L['plu_title'] : $plugin_title;

		if($cfg['homebreadcrumb'])
		{
			$bhome = $R['plug_code_homebreadcrumb'];
		}
		else
		{
			$bhome = '';
		}

		$t-> assign(array(
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
		foreach($sed_plugins['popup'] as $k)
		{
			if ($k['pl_code']==$o)
			{
				$extp[$i] = $k;
			}
		}
	}

	if (count($extp)==0)
	{
		sed_redirect(sed_url('message', 'msg=907', '', true));
	}

	/* ============= */

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
		'POPUP_BODY' => $popup_body,
	));

	$t->parse('MAIN');
	$t->out('MAIN');
}
elseif (!empty($r) && defined('SED_AJAX'))
{
	$empty = true;
	if (is_array($sed_plugins['ajax']))
	{
		foreach($sed_plugins['ajax'] as $k)
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
