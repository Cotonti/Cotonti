<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.editpost.tags, forums.posts.newpost.tags, forums.newtopic.tags
Tags=forums.editpost.tpl:{FORUMS_EDITPOST_PFS},{FORUMS_EDITPOST_SFS};forums.posts.tpl:{FORUMS_POSTS_NEWPOST_PFS},{FORUMS_POSTS_NEWPOST_SFS};forums.newtopic.tpl:{FORUMS_NEWTOPIC_PFS},{FORUMS_NEWTOPIC_SFS}
[END_COT_EXT]
==================== */

/**
 * PFS links for forums
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('pfs', 'module');

$pfs_caller = cot_get_caller();
if ($pfs_caller == 'forums.posts') {
    $pfsSrc = 'newpost';
    $pfsName = 'rmsgtext';
    $pfsTag = 'POSTS_NEWPOST';
} elseif ($pfs_caller == 'forums.newtopic') {
    $pfsSrc = 'newtopic';
    $pfsName = 'rmsgtext';
    $pfsTag = 'NEWTOPIC';
} else {
    $pfsSrc = 'editpost';
    $pfsName = 'rmsgtext';
    $pfsTag = 'EDITPOST';
}

$t->assign([
    'FORUMS_' . $pfsTag . '_PFS' => cot_build_pfs(Cot::$usr['id'], $pfsSrc, $pfsName, Cot::$L['Mypfs'], Cot::$sys['parser']),
    'FORUMS_' . $pfsTag . '_SFS' => (cot_auth('pfs', 'a', 'A'))
        ? cot_build_pfs(0, $pfsSrc, $pfsName, Cot::$L['SFS'], Cot::$sys['parser'])
        : '',
]);

if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
    // @deprecated in 0.9.26
    $pfs = cot_build_pfs(Cot::$usr['id'], $pfsSrc, $pfsName, Cot::$L['Mypfs'], Cot::$sys['parser']);
    $pfs .= (cot_auth('pfs', 'a', 'A'))
        ? ' &nbsp; ' . cot_build_pfs(0, $pfsSrc, $pfsName, Cot::$L['SFS'], Cot::$sys['parser'])
        : '';
    $t->assign('FORUMS_' . $pfsTag . '_MYPFS', $pfs);
}