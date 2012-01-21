<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=pfs.first
[END_COT_EXT]
==================== */

/**
 * Overrides markup in PFS insertText
 *
 * @package html
 * @version 0.9.1
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($sys['parser'] == 'html')
{
	$R['pfs_code_addfile'] = '<a href="{$pfs_base_href}{$pfs_dir_user}\'+gfile+\'" title="\'+gdesc+\'">\'+gfile+\'</a>';
	$R['pfs_code_addpix'] = '<img src="{$pfs_base_href}{$pfs_dir_user}\'+gfile+\'" alt="\'+gdesc+\'" />';
	$R['pfs_code_addthumb'] = '<a href="{$pfs_base_href}{$pfs_dir_user}\'+gfile+\'" title="\'+gdesc+\'"><img src="{$pfs_base_href}{$thumbs_dir_user}\'+gfile+\'" alt="\'+gdesc+\'" /></a>';
}
?>
