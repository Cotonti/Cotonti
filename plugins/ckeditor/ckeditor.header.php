<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.tags
Tags=header.tpl:{HEADER_HEAD}
[END_COT_EXT]
==================== */

/**
 * CKEditor connector for Cotonti
 *
 * @package ckeditor
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (function_exists('cot_textarea') && cot_auth('plug', 'ckeditor', 'W'))
{
	// Main CKEditor file
	$ckeditor = <<<HTM
<script type="text/javascript" src="{$cfg['plugins_dir']}/ckeditor/lib/ckeditor.js"></script>
HTM;
	// Optional jQuery adapter
	if ($cfg['jquery'])
	{
		$ckeditor .= <<<HTM
<script type="text/javascript" src="{$cfg['plugins_dir']}/ckeditor/lib/adapters/jquery.js"></script>
HTM;
	}
	// Load preset and connector
	if ($usr['id'] > 0)
	{
		$preset_name = 'group_' . $usr['maingrp'];
		if (!file_exists($cfg['plugins_dir'] . "/ckeditor/presets/ckeditor.$preset_name.set.js"))
		{
			$preset_name = 'default';
		}
	}
	else
	{
		$preset_name = file_exists($cfg['plugins_dir'] . "/ckeditor/presets/ckeditor.group_1.set.js") ? 'group_1'
			: 'default';
	}
	$ckeditor .= <<<HTM
<script type="text/javascript" src="{$cfg['plugins_dir']}/ckeditor/presets/ckeditor.$preset_name.set.js"></script>
HTM;

	$t->assign('HEADER_HEAD', $t->get('HEADER_HEAD') . $ckeditor);
}

?>
