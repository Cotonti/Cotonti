<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
Tags=header.tpl:{HEADER_HEAD}
[END_COT_EXT]
==================== */

/**
 * CKEditor connector for Cotonti.
 * Uses direct header output rather than consolidated cache
 * because CKEditor uses dynamic AJAX component loading and
 * does not support consolidation.
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
		cot_rc_link_file('http://' . $cfg['plugin']['ckeditor']['cdn_url']. '/ckeditor.js', true);
		if ($cfg['jquery'])
		{
			cot_rc_link_file('http://' . $cfg['plugin']['ckeditor']['cdn_url']. '/adapters/jquery.js');
		}
	}
	else
	{
		cot_rc_link_file($cfg['plugins_dir'] . '/ckeditor/lib/ckeditor.js', true);
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
	cot_rc_link_file($cfg['plugins_dir'] . "/ckeditor/presets/ckeditor.$preset_name.set.js");
}

?>
