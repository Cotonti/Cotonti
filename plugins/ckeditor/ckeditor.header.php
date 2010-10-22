<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
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
	if ($cfg['plugin']['ckeditor']['cdn'])
	{
		cot_headrc_file('http://' . $cfg['plugin']['ckeditor']['cdn_url']. '/ckeditor.js', 'request', 'js', true);
		if ($cfg['jquery'])
		{
			cot_headrc_file('http://' . $cfg['plugin']['ckeditor']['cdn_url']. '/adapters/jquery.js', 'request');
		}
	}
	else
	{
		cot_headrc_file($cfg['plugins_dir'] . '/ckeditor/lib/ckeditor.js', 'request', 'js', true);
		if ($cfg['jquery'])
		{
			cot_headrc_file($cfg['plugins_dir'] . '/ckeditor/lib/adapters/jquery.js');
		}
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
	cot_headrc_file($cfg['plugins_dir'] . "/ckeditor/presets/ckeditor.$preset_name.set.js", 'request');
}

?>
