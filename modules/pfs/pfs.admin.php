<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin
[END_COT_EXT]
==================== */

/**
 * Administration panel - PFS
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

(defined('SED_CODE') && defined('SED_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('pfs', 'a');
sed_block($usr['isadmin']);

$s = sed_import('s', 'G', 'ALP');

if ($s == 'allpfs')
{
	require sed_incfile('admin.allpfs', 'pfs', true);
}
else
{
	$t = new XTemplate(sed_skinfile('pfs.admin'));

	$adminpath[] = array(sed_url('admin', 'm=other'), $L['Other']);
	$adminpath[] = array(sed_url('admin', 'm=pfs'), $L['PFS']);
	$adminhelp = $L['adm_help_pfs'];

	/* === Hook === */
	$extp = sed_getextplugins('admin.pfs.first');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */

	if (!function_exists('gd_info'))
	{
		$is_adminwarnings = true;
	}
	else
	{
		$gd_datas = gd_info();
		foreach ($gd_datas as $k => $i)
		{
			if (mb_strlen($i) < 2)
			{
				$i = $sed_yesno[$i];
			}
			$t->assign(array(
				'ADMIN_PFS_DATAS_NAME' => $k,
				'ADMIN_PFS_DATAS_ENABLE_OR_DISABLE' => $i
			));
			$t->parse('MAIN.PFS_ROW');
		}
	}

	$t->assign(array(
		'ADMIN_PFS_URL_CONFIG' => sed_url('admin', 'm=config&n=edit&o=core&p=pfs'),
		'ADMIN_PFS_URL_ALLPFS' => sed_url('admin', 'm=pfs&s=allpfs'),
		'ADMIN_PFS_URL_SFS' => sed_url('pfs', 'userid=0')
	));

	/* === Hook  === */
	$extp = sed_getextplugins('admin.pfs.tags');
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
}

$t->parse('MAIN');
if (SED_AJAX)
{
	$t->out('MAIN');
}
else
{
	$adminmain = $t->text('MAIN');
}
?>