<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.add.tags,page.edit.tags
Tags=page.add.tpl:{PAGEADD_FORM_MYPFS},{PAGEADD_FORM_URL_MYPFS};page.edit.tpl:{PAGEEDIT_FORM_MYPFS},{PAGEEDIT_FORM_URL_MYPFS}
[END_COT_EXT]
==================== */

/**
 * PFS link on page.add
 *
 * @package pfs
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

cot_require('pfs');

// TODO unify field names for cleaner multihooking
if ($cot_current_hook == 'page.add.tags')
{
	$pfs_src = 'rpage';
	$pfs_tag = 'PAGEADD';
}
else
{
	$pfs_src = 'update';
	$pfs_tag = 'PAGEAEDIT';
}

$pfs = cot_build_pfs($usr['id'], $pfs_src, 'rpagetext',$L['Mypfs']);
$pfs .= (cot_auth('pfs', 'a', 'A')) ? ' &nbsp; '.cot_build_pfs(0, $pfs_src, 'rpagetext', $L['SFS']) : '';
$pfs_form_url_myfiles = (!$cfg['disable_pfs']) ? cot_build_pfs($usr['id'], $pfs_src, 'rpageurl', $L['Mypfs']) : '';
$pfs_form_url_myfiles .= (cot_auth('pfs', 'a', 'A')) ? ' '.cot_build_pfs(0, $pfs_src, 'rpageurl', $L['SFS']) : '';

$t->assign(array(
	$pfs_tag . '_FORM_MYPFS' => $pfs,
	$pfs_tag . '_FORM_URL_MYPFS' => $pfs_form_url_myfiles
));
?>
