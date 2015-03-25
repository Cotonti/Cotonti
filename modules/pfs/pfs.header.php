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
 */

defined('COT_CODE') or die('Wrong URL.');

if ($usr['id'] > 0 && $cot_groups[$usr['maingrp']]['pfs_maxtotal'] > 0 && $cot_groups[$usr['maingrp']]['pfs_maxfile'] > 0)
{
	$pfs_url = cot_url('pfs');
	$out['pfs'] = cot_rc_link($pfs_url, $L['Mypfs']);
	$t->assign(array(
		'HEADER_USER_PFS' => $out['pfs'],
		'HEADER_USER_PFS_URL' => $pfs_url
	));
}
