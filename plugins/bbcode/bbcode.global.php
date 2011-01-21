<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Connects BBcode parser, loads data and registers parser function
 *
 * @package bbcode
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('bbcode', 'plug');

cot_bbcode_load();
if ($cfg['plugin']['bbcode']['smilies'])
{
	cot_smilies_load();
}

$cot_parsers[] = 'cot_bbcode_parse';

// Override markup resource strings
// Forums
$R['forums_code_quote'] = "[quote][url={\$url}]#{\$id}[/url] [b]{\$postername} :[/b]\n{\$text}\n[/quote]";
$R['forums_code_quote_begin'] = '[quote';
$R['forums_code_quote_close'] = '[/quote]';
$R['forums_code_update'] = "\n\n[b]{\$updated}[/b]\n\n";
// PFS
$R['pfs_code_addfile'] = "'[url=".$cfg['pfs_dir']."'+gfile+']'+gfile+'[/url]'";
$R['pfs_code_addpix'] = "'[img]'+gfile+'[/img]'";
$R['pfs_code_addthumb'] = "'[img=".$cfg['pfs_dir']."'+gfile+']".$cfg['pfs_thumbpath']."'+gfile+'[/img]'";
?>
