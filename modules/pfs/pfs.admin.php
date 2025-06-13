<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin
[END_COT_EXT]
==================== */

/**
 * Administration panel - PFS
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list(Cot::$usr['auth_read'], Cot::$usr['auth_write'], Cot::$usr['isadmin']) = cot_auth('pfs', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('pfs', 'module');

if ($s == 'allpfs') {
	require cot_incfile('pfs', 'module', 'admin.allpfs');
} else {
	$t = new XTemplate(cot_tplfile('pfs.admin', 'module', true));

	$adminPath[] = [cot_url('admin', 'm=extensions'), Cot::$L['Extensions']];
	$adminPath[] = [cot_url('admin', 'm=extensions&a=details&mod=' . $m), $cot_modules[$m]['title']];
	$adminPath[] = [cot_url('admin', 'm='.$m), Cot::$L['Administration']];
	//$adminHelp = $L['adm_help_pfs'];
	$adminTitle = Cot::$L['pfs_myFiles'];

	/* === Hook === */
	foreach (cot_getextplugins('pfs.admin.first') as $pl) {
		include $pl;
	}
	/* ===== */

	if (!function_exists('gd_info')) {
		if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
			// @deprecated in 0.9.25
			$is_adminwarnings = true;
		}
		cot_error('adm_nogd');
	} else {
		$gd_datas = gd_info();
		foreach ($gd_datas as $k => $i) {
			if (mb_strlen($i) < 2) {
				$i = $cot_yesno[$i];
			}
			$t->assign([
				'ADMIN_PFS_DATAS_NAME' => $k,
				'ADMIN_PFS_DATAS_ENABLE_OR_DISABLE' => $i,
			]);
			$t->parse('MAIN.PFS_ROW');
		}
	}

	$t->assign([
		'ADMIN_PFS_URL_CONFIG' => cot_url('admin', ['m' => 'config', 'n' => 'edit', 'o' => 'module', 'p' => 'pfs']),
		'ADMIN_PFS_URL_ALLPFS' => cot_url('admin', 'm=pfs&s=allpfs'),
		'ADMIN_PFS_URL_SFS' => cot_url('pfs', 'userid=0'),
	]);

	/* === Hook  === */
	foreach (cot_getextplugins('pfs.admin.tags') as $pl) {
		include $pl;
	}
	/* ===== */
}

$t->parse('MAIN');
$adminMain = $t->text('MAIN');