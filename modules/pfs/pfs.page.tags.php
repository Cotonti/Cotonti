<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.tags,page.edit.tags
Tags=page.add.tpl:{PAGEADD_FORM_PFS},{PAGEADD_FORM_SFS},{PAGEADD_FORM_URL_PFS},{PAGEADD_FORM_URL_SFS};page.edit.tpl:{PAGEEDIT_FORM_PFS},{PAGEEDIT_FORM_SFS},{PAGEEDIT_FORM_URL_PFS},{PAGEEDIT_FORM_URL_SFS}
[END_COT_EXT]
==================== */

/**
 * PFS link on page.add
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('pfs', 'module');

if (cot_get_caller() == 'page.add') {
	$pfs_tag = 'PAGEADD';
} else {
	$pfs_tag = 'PAGEEDIT';
}

$t->assign([
	$pfs_tag . '_FORM_PFS' => cot_build_pfs(Cot::$usr['id'], 'pageform', 'rpagetext', Cot::$L['Mypfs'], Cot::$sys['parser']),
	$pfs_tag . '_FORM_SFS' => cot_auth('pfs', 'a', 'A')
        ? cot_build_pfs(0, 'pageform', 'rpagetext', Cot::$L['SFS'], Cot::$sys['parser'])
        : '',
	$pfs_tag . '_FORM_URL_PFS' => cot_build_pfs(Cot::$usr['id'], 'pageform', 'rpageurl', Cot::$L['Mypfs']),
	$pfs_tag . '_FORM_URL_SFS' => cot_auth('pfs', 'a', 'A')
        ? cot_build_pfs(0, 'pageform', 'rpageurl', Cot::$L['SFS'])
        : ''
]);
