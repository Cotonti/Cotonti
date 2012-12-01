<?php defined('COT_CODE') or die('Wrong URL');
/**
 * 0.9.12: Fields for meta title and description
 */

require_once cot_incfile('page', 'module');

global $db_pages;

if (!$db->fieldExists($db_pages, 'page_metatitle'))
{
	$db->query("ALTER TABLE `$db_pages` ADD COLUMN `page_metatitle` varchar(255) collate utf8_unicode_ci default NULL");
}

if (!$db->fieldExists($db_pages, 'page_metadesc'))
{
	$db->query("ALTER TABLE `$db_pages` ADD COLUMN `page_metadesc` varchar(255) collate utf8_unicode_ci default NULL");
}
