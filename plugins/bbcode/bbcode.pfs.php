<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=pfs.first
[END_COT_EXT]
==================== */

/**
 * Overrides markup in PFS insertText
 *
 * @package BBcode
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($sys['parser'] == 'bbcode')
{
	$R['pfs_code_addfile'] = '[url={$pfs_base_href}{$pfs_dir_user}\'+gfile+\']\'+gfile+\'[/url]';
	$R['pfs_code_addpix'] = '[img]{$pfs_base_href}{$pfs_dir_user}\'+gfile+\'[/img]';
	$R['pfs_code_addthumb'] = '[img={$pfs_base_href}{$pfs_dir_user}\'+gfile+\']{$pfs_base_href}{$thumbs_dir_user}\'+gfile+\'[/img]';
}
