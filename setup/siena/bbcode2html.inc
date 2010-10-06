<?php
/**
 * A tool that converts BBcode pages, comments, posts, pms into HTML.
 * Install bbcode plugin before running it. Uninstall bbcode plugin afterwards.
 * Alternatively, it can take HTML from existing HTML cache.
 * It drops all existing HTML cache at the end.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

$bbcode_installed = cot_plugin_installed('bbcode');

if (cot_module_installed('page') && $cfg['module']['page']['markup'])
{
	cot_require('page');
	// Attempt to override from HTML cache
	cot_db_query("UPDATE $db_pages SET page_text = page_html WHERE page_html != '' AND page_type = 0");
	if ($bbcode_installed)
	{
		// Update manually
		$res = cot_db_query("SELECT page_text, page_id FROM $db_pages WHERE page_html = '' AND page_type = 0");
		while ($row = cot_db_fetchassoc($res))
		{
			$html = cot_bbcode_parse($row['page_text']);
			cot_db_update($db_pages, array('page_text' => $html), 'page_id = ' . $row['page_id']);
		}
		cot_db_freeresult($res);
	}
	// Drop HTML cache and page_type
	cot_db_query("ALTER TABLE $db_pages DROP COLUMN page_html");
	cot_db_query("ALTER TABLE $db_pages DROP COLUMN page_type");
}

if (cot_module_installed('forums') && $cfg['module']['forums']['markup'])
{
	cot_require('forums');
	// Attempt to override from HTML cache
	cot_db_query("UPDATE $db_forum_posts SET fp_text = fp_html WHERE fp_html != ''");
	if ($bbcode_installed)
	{
		// Update manually
		$res = cot_db_query("SELECT fp_text, fp_id FROM $db_forum_posts WHERE fp_html = ''");
		while ($row = cot_db_fetchassoc($res))
		{
			$html = cot_bbcode_parse($row['fp_text']);
			cot_db_update($db_forum_posts, array('fp_text' => $html), 'fp_id = ' . $row['fp_id']);
		}
		cot_db_freeresult($res);
	}
	// Drop HTML cache
	cot_db_query("ALTER TABLE $db_forum_posts DROP COLUMN fp_html");
}

if (cot_module_installed('pm') && $cfg['module']['pm']['markup'])
{
	cot_require('pm');
	// Attempt to override from HTML cache
	cot_db_query("UPDATE $db_pm SET pm_text = pm_html WHERE pm_html != ''");
	if ($bbcode_installed)
	{
		// Update manually
		$res = cot_db_query("SELECT pm_text, pm_id FROM $db_pm WHERE pm_html = ''");
		while ($row = cot_db_fetchassoc($res))
		{
			$html = cot_bbcode_parse($row['pm_text']);
			cot_db_update($db_pm, array('pm_text' => $html), 'pm_id = ' . $row['pm_id']);
		}
		cot_db_freeresult($res);
	}
	// Drop HTML cache
	cot_db_query("ALTER TABLE $db_pm DROP COLUMN pm_html");
}

if (cot_plugin_installed('comments') && $cfg['plugin']['comments']['markup'])
{
	cot_require('comments', true);
	// Attempt to override from HTML cache
	cot_db_query("UPDATE $db_com SET com_text = com_html WHERE com_html != ''");
	if ($bbcode_installed)
	{
		// Update manually
		$res = cot_db_query("SELECT com_text, com_id FROM $db_com WHERE com_html = ''");
		while ($row = cot_db_fetchassoc($res))
		{
			$html = cot_bbcode_parse($row['com_text']);
			cot_db_update($db_pm, array('com_text' => $html), 'com_id = ' . $row['com_id']);
		}
		cot_db_freeresult($res);
	}
	// Drop HTML cache
	cot_db_query("ALTER TABLE $db_com DROP COLUMN com_html");
}

?>
