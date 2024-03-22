<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.tags
Tags=header.tpl:{HEADER_USER_PFS}
[END_COT_EXT]
==================== */

/**
 * PFS header link
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t;
 */

defined('COT_CODE') or die('Wrong URL.');

if (Cot::$usr['id'] > 0 && $cot_groups[Cot::$usr['maingrp']]['pfs_maxtotal'] > 0 && $cot_groups[Cot::$usr['maingrp']]['pfs_maxfile'] > 0) {
	$pfs_url = cot_url('pfs');
	$out['pfs'] = cot_rc_link($pfs_url, Cot::$L['Mypfs']);
	$t->assign([
		'HEADER_USER_PFS' => $out['pfs'],
		'HEADER_USER_PFS_URL' => $pfs_url,
	]);
}
