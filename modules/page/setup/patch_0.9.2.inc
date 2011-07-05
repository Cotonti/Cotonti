<?php
/**
 * 0.9.2: add and fill page_markup field
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

require_once cot_incfile('page', 'module');

global $db_pages, $db_config;

$db->query("ALTER TABLE $db_pages ADD COLUMN `page_parser` VARCHAR(64) NOT NULL DEFAULT ''");

if (defined('COT_UPGRADE'))
{
	// Upgrading from Genoa
	$db->update($db_pages, array('page_parser' => 'html'), 'page_type = 1');
	$db->update($db_pages, array('page_parser' => 'bbcode'), 'page_type = 0');
	// Drop HTML cache and page_type
	$db->query("ALTER TABLE $db_pages DROP COLUMN page_type");
	// Set default parsing to bbcode and editor to markitup
	$db->update($db_config, array('config_value' => 'bbcode'), "config_owner = 'module' AND config_cat = 'page' AND config_name = 'parser'");
}
else
{
	// Early Siena patch
	if (cot_extension_installed('bbcode'))
	{
		$db->update($db_config, array('config_value' => 'bbcode'), "config_owner = 'module' AND config_cat = 'page' AND config_name = 'parser'");
		$db->update($db_pages, array('page_parser' => 'bbcode'));
	}
	else
	{
		$db->update($db_config, array('config_value' => 'html'), "config_owner = 'module' AND config_cat = 'page' AND config_name = 'parser'");
		$db->update($db_pages, array('page_parser' => 'html'));
	}
}

?>
