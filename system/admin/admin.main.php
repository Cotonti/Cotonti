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

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'any');
cot_block($usr['auth_read']);

$enabled[0] = $L['Disabled'];
$enabled[1] = $L['Enabled'];

$id = cot_import('id', 'G', 'TXT');
$po = cot_import('po', 'G', 'TXT');
$c = cot_import('c', 'G', 'TXT');
$p = cot_import('p', 'G', 'TXT');
$l = cot_import('l', 'G', 'TXT');
$o = cot_import('o', 'P', 'TXT');
$w = cot_import('w', 'P', 'TXT');
$u = cot_import('u', 'P', 'TXT');
$s = cot_import('s', 'G', 'ALP', 24);

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.main') as $pl)
{
	include $pl;
}
/* ===== */

$standard_admin = array('cache.disk', 'cache', 'config', 'extrafields', 'home', 'infos',
	'log', 'other', 'extensions', 'rights', 'rightsbyitem', 'structure', 'urls', 'users');

$inc_file = (empty($m)) ? 'home' : $m;
$inc_file = (empty($s)) ? $inc_file : $inc_file.'.'.$s;
if (in_array($inc_file, $standard_admin))
{
	$inc_file = cot_incfile('admin', 'module', $inc_file);
}
else
{
	$inc_file = $cfg['modules_dir'] . "/$inc_file/$inc_file.admin.php";
}

if (!file_exists($inc_file))
{
	cot_die();
}

$allow_img['0']['0'] = $R['admin_icon_deny'];
$allow_img['1']['0'] = $R['admin_icon_allow'];
$allow_img['0']['1'] = $R['admin_icon_deny_locked'];
$allow_img['1']['1'] = $R['admin_icon_allow_locked'];

$usr['admin_config'] = cot_auth('admin', 'a', 'A');
$usr['admin_structure'] = cot_auth('structure', 'a', 'A');
$usr['admin_users'] = cot_auth('users', 'a', 'A');

require $inc_file;

$adminhelp = (empty($adminhelp)) ? $L['None'] : $adminhelp;

$title_params = array(
	'ADMIN' => $L['Administration']
);
$out['head'] .= $R['code_noindex'];
$out['subtitle'] = cot_title('{ADMIN}', $title_params);

require_once $cfg['system_dir'].'/header.php';

if (!COT_AJAX)
{
	$t = new XTemplate(cot_tplfile('admin', 'core'));

	// Generate the admin menu
	$admin_menu = array();
	// Standard admin areas
	$admin_menu[] = array(
		'url' => cot_url('admin'),
		'icon' => $R['admin_menu_icon_home'],
		'class' => empty($m) ? 'sel' : ''
	);
	if ($usr['admin_config'])
	{
		$admin_menu[] = array(
			'url' => cot_url('admin', 'm=config'),
			'icon' => $R['admin_menu_icon_config'],
			'class' => $m == 'config' ? 'sel' : ''
		);
		$admin_menu[] = array(
			'url' => cot_url('admin', 'm=extensions'),
			'icon' => $R['admin_menu_icon_extensions'],
			'class' => $m == 'extensions' ? 'sel' : ''
		);
	}
	if ($usr['admin_users'])
	{
		$admin_menu[] = array(
			'url' => cot_url('admin', 'm=users'),
			'icon' => $R['admin_menu_icon_users'],
			'class' => $m == 'users' ? 'sel' : ''
		);
	}
	if ($usr['isadmin'])
	{
		$admin_menu[] = array(
			'url' => cot_url('admin', "m=other"),
			'icon' => $R['admin_menu_icon_other'],
			'class' => $m == 'other' ? 'sel' : ''
		);
	}
	// Module admin
	foreach ($cot_modules as $code => $mod)
	{
		$info = cot_infoget($cfg['modules_dir'] . "/$code/$code.setup.php", 'COT_EXT');
		if (!empty($info['Admin_icon']))
		{
			if (file_exists(cot_langfile($code, 'module')))
			{
				require_once cot_langfile($code, 'module');
				$title = $L[ucfirst($code)];
			};
			$title = isset($L[$info['Name']]) ? $L[$info['Name']] : $info['Name'];
			$src = $cfg['modules_dir'] . "/$code/" . trim($info['Admin_icon']);
			$admin_menu[] = array(
				'url' => cot_url('admin', "m=$code"),
				'icon' => cot_rc('admin_menu_icon_module', array('code' => $code, 'src' => $src, 'title' => $title)),
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
		'ADMIN_TITLE' => cot_build_adminsection($adminpath),
		'ADMIN_SUBTITLE' => $adminsubtitle,
		'ADMIN_MAIN' => $adminmain,
		'ADMIN_HELP' => $adminhelp
	));

	/* === Hook for the plugins === */
	foreach (cot_getextplugins('admin.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$t->parse('MAIN');
	$t->out('MAIN');
}

require_once $cfg['system_dir'].'/footer.php';

?>