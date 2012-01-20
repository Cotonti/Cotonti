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
 * @package pfs
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('pfs', 'module');

if (cot_get_caller() == 'page.add')
{
	$pfs_tag = 'PAGEADD';
}
else
{
	$pfs_tag = 'PAGEEDIT';
}

$t->assign(array(
	$pfs_tag . '_FORM_PFS' => cot_build_pfs($usr['id'], 'pageform', 'rpagetext',$L['Mypfs'], $sys['parser']),
	$pfs_tag . '_FORM_SFS' => (cot_auth('pfs', 'a', 'A')) ? ' &nbsp; '.cot_build_pfs(0, 'pageform', 'rpagetext', $L['SFS'], $sys['parser']) : '',
	$pfs_tag . '_FORM_URL_PFS' => cot_build_pfs($usr['id'], 'pageform', 'rpageurl', $L['Mypfs']),
	$pfs_tag . '_FORM_URL_SFS' => (cot_auth('pfs', 'a', 'A')) ? ' '.cot_build_pfs(0, 'pageform', 'rpageurl', $L['SFS']) : ''
));
?>
