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
 * @package pfs
 * @version 0.7.0
 * @author Neocrome, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

if ($usr['id'] > 0 && $cot_groups[$usr['maingrp']]['pfs_maxtotal'] > 0 && $cot_groups[$usr['maingrp']]['pfs_maxfile'] > 0)
{
	$out['pfs'] = cot_rc_link(cot_url('pfs'), $L['Mypfs']);
	$t->assign('HEADER_USER_PFS', $out['pfs']);
}
?>
