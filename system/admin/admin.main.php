<?php
/**
 * Administration panel
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('admin', 'any');
sed_block($usr['auth_read']);

$enabled[0] = $L['Disabled'];
$enabled[1] = $L['Enabled'];

$id = sed_import('id', 'G', 'TXT');
$po = sed_import('po', 'G', 'TXT');
$c = sed_import('c', 'G', 'TXT');
$p = sed_import('p', 'G', 'TXT');
$l = sed_import('l', 'G', 'TXT');
$o = sed_import('o', 'P', 'TXT');
$w = sed_import('w', 'P', 'TXT');
$u = sed_import('u', 'P', 'TXT');
$s = sed_import('s', 'G', 'ALP', 24);

/* === Hook for the plugins === */
foreach (sed_getextplugins('admin.main') as $pl)
{
	include $pl;
}
/* ===== */

$standard_admin = array('banlist', 'bbcode', 'cache.disk', 'cache', 'config', 'extrafields', 'hits', 'home', 'infos',
	'log', 'other', 'extensions', 'ratings', 'referers', 'rights', 'rightsbyitem', 'structure', 'tools', 'trashcan',
	'urls', 'users');

$inc_file = (empty($m)) ? 'home' : $m;
$inc_file = (empty($s)) ? $inc_file : $inc_file.'.'.$s;
if (in_array($inc_file, $standard_admin))
{
	$inc_file = sed_incfile('admin', $inc_file);
}
else
{
	$inc_file = sed_incfile($inc_file, 'admin');
}

if (!file_exists($inc_file))
{
	sed_die();
}

$allow_img['0']['0'] = $R['admin_icon_deny'];
$allow_img['1']['0'] = $R['admin_icon_allow'];
$allow_img['0']['1'] = $R['admin_icon_deny_locked'];
$allow_img['1']['1'] = $R['admin_icon_allow_locked'];

$usr['admin_config'] = sed_auth('admin', 'a', 'A');
$usr['admin_structure'] = sed_auth('structure', 'a', 'A');
$usr['admin_users'] = sed_auth('users', 'a', 'A');

require $inc_file;

$adminhelp = (empty($adminhelp)) ? $L['None'] : $adminhelp;

$title_params = array(
	'ADMIN' => $L['Administration']
);
$out['head'] .= $R['code_noindex'];
$out['subtitle'] = sed_title('{ADMIN}', $title_params);

require_once $cfg['system_dir'].'/header.php';

if (!SED_AJAX)
{
	$t = new XTemplate(sed_skinfile('admin'));

	// Generate the admin menu
	$admin_menu = array();
	// Standard admin areas
	$admin_menu[] = array(
		'url' => sed_url('admin'),
		'icon' => $R['admin_menu_icon_home'],
		'class' => empty($m) ? 'sel' : ''
	);
	if ($usr['admin_config'])
	{
		$admin_menu[] = array(
			'url' => sed_url('admin', 'm=config'),
			'icon' => $R['admin_menu_icon_config'],
			'class' => $m == 'config' ? 'sel' : ''
		);
		$admin_menu[] = array(
			'url' => sed_url('admin', 'm=extensions'),
			'icon' => $R['admin_menu_icon_extensions'],
			'class' => $m == 'extensions' ? 'sel' : ''
		);
	}
	if ($usr['admin_structure'])
	{
		$admin_menu[] = array(
			'url' => sed_url('admin', 'm=structure'),
			'icon' => $R['admin_menu_icon_structure'],
			'class' => $m == 'structure' ? 'sel' : ''
		);
	}
	if ($usr['admin_users'])
	{
		$admin_menu[] = array(
			'url' => sed_url('admin', 'm=users'),
			'icon' => $R['admin_menu_icon_users'],
			'class' => $m == 'users' ? 'sel' : ''
		);
	}
	if ($usr['isadmin'])
	{
		foreach (array('tools', 'other') as $loc)
		{
			$admin_menu[] = array(
				'url' => sed_url('admin', "m=$loc"),
				'icon' => $R['admin_menu_icon_' . $loc],
				'class' => $m == $loc ? 'sel' : ''
			);
		}
	}
	// Module admin
	foreach ($sed_modules as $code => $mod)
	{
		$info = sed_infoget($cfg['modules_dir'] . "/$code/$code.setup.php", 'COT_EXT');
		if (!empty($info['Admin_icon']))
		{
			if (file_exists(sed_langfile($code, 'module')))
			{
				sed_require_lang($code, 'module');
				$title = $L[ucfirst($code)];
			}
			$uccode = ucfirst($code);
			$title = isset($L[$uccode]) ? $L[$uccode] : $uccode;
			$src = $cfg['modules_dir'] . "/$code/" . trim($info['Admin_icon']);
			$admin_menu[] = array(
				'url' => sed_url('admin', "m=$code"),
				'icon' => sed_rc('admin_menu_icon_module', array('code' => $code, 'src' => $src, 'title' => $title)),
				'class' => $m == $code ? 'sel' : ''
			);
		}
	}

	// Rendering
	foreach ($admin_menu as $item)
	{
		$t->assign(array(
			'ADMIN_MENU_URL' => $item['url'],
			'AMDIN_MENU_TITLE' => $item['title'],
			'ADMIN_MENU_ICON' => $item['icon'],
			'ADMIN_MENU_CLASS' => $item['class']
		));
		$t->parse('MAIN.ADMIN_MENU_ROW');
	}

	$t->assign(array(
		'ADMIN_TITLE' => sed_build_adminsection($adminpath),
		'ADMIN_SUBTITLE' => $adminsubtitle,
		'ADMIN_MAIN' => $adminmain,
		'ADMIN_HELP' => $adminhelp
	));

	/* === Hook for the plugins === */
	foreach (sed_getextplugins('admin.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN');
	$t->out('MAIN');
}

require_once $cfg['system_dir'].'/footer.php';

?>