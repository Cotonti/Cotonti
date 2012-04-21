<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=pfs.first
[END_COT_EXT]
==================== */

/**
 * Overrides markup in PFS insertText
 *
 * @package bbcode
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($parser == 'bbcode')
{
	$R['pfs_code_addfile'] = '[url={$pfs_base_href}{$pfs_dir_user}\'+gfile+\']\'+gfile+\'[/url]';
	$R['pfs_code_addpix'] = '[img]{$pfs_base_href}{$pfs_dir_user}\'+gfile+\'[/img]';
	$R['pfs_code_addthumb'] = '[img={$pfs_base_href}{$pfs_dir_user}\'+gfile+\']{$pfs_base_href}{$thumbs_dir_user}\'+gfile+\'[/img]';
}

?>
