<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=headrc
[END_COT_EXT]
==================== */

/**
 * Extra CKEditor adapters and settings.
 * Consolidated with the others.
 *
 * @package ckeditor
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');


if (!$cfg['plugin']['ckeditor']['cdn'] && $cfg['jquery'])
{
	cot_headrc_load_file($cfg['plugins_dir'] . '/ckeditor/lib/adapters/jquery.js');
}

// Consolidate presets
$dp = opendir($cfg['plugins_dir'] . '/ckeditor/presets');
while ($fname = readdir($dp))
{
	if (preg_match('#^ckeditor\.group_(\d+)\.set\.js$#', $fname, $mt))
	{
		cot_headrc_load_file($cfg['plugins_dir'] . '/ckeditor/presets/' . $fname, 'group_' . $mt[1]);
	}
}
closedir($dp);

// Default preset
cot_headrc_load_file($cfg['plugins_dir'] . '/ckeditor/presets/ckeditor.default.set.js');

?>
