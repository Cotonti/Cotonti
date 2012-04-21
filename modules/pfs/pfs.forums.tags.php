<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.tags, forums.posts.newpost.tags, forums.newtopic.tags
Tags=forums.editpost.tpl:{FORUMS_EDITPOST_MYPFS};forums.editpost.tpl:{FORUMS_POSTS_NEWPOST_MYPFS};forums.newtopic.tpl:{FORUMS_NEWTOPIC_MYPFS}
[END_COT_EXT]
==================== */

/**
 * PFS links for forums
 *
 * @package pfs
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('pfs', 'module');

$pfs_caller = cot_get_caller();
if ($pfs_caller == 'forums.posts')
{
	$pfs_src = 'newpost';
	$pfs_name = 'rmsgtext';
	$pfs_tag = 'POSTS_NEWPOST';
}
elseif ($pfs_caller == 'forums.newtopic')
{
	$pfs_src = 'newtopic';
	$pfs_name = 'rmsgtext';
	$pfs_tag = 'NEWTOPIC';
}
else
{
	$pfs_src = 'editpost';
	$pfs_name = 'rmsgtext';
	$pfs_tag = 'EDITPOST';
}

$pfs = cot_build_pfs($usr['id'], $pfs_src, $pfs_name, $L['Mypfs']);
$pfs .= (cot_auth('pfs', 'a', 'A')) ? ' &nbsp; '.cot_build_pfs(0, $pfs_src, $pfs_name, $L['SFS']) : '';

$t->assign('FORUMS_' . $pfs_tag . '_MYPFS', $pfs);
?>
