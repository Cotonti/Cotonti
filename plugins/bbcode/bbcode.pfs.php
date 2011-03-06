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
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$R['pfs_code_addfile'] = "'[url={$cfg['pfs_dir']}/'+gfile+']'+gfile+'[/url]'";
$R['pfs_code_addpix'] = "'[img]'+gfile+'[/img]'";
$R['pfs_code_addthumb'] = "'[img={$cfg['pfs_dir']}/'+gfile+']{$cfg['thumbs_dir']}/'+gfile+'[/img]'";

?>
