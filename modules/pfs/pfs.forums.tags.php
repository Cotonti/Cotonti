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
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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

$pfs = cot_build_pfs($usr['id'], $pfs_src, $pfs_name, $L['Mypfs'], $sys['parser']);
$pfs .= (cot_auth('pfs', 'a', 'A')) ? ' &nbsp; '.cot_build_pfs(0, $pfs_src, $pfs_name, $L['SFS'], $sys['parser']) : '';

$t->assign('FORUMS_' . $pfs_tag . '_MYPFS', $pfs);
