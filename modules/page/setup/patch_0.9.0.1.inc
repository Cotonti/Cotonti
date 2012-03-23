<?php
/**
 * 0.9.0.1 page_keywords field
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');

global $db_pages;

if (!$db->fieldExists($db_pages, 'page_keywords'))
{
	$db->query("ALTER TABLE `$db_pages` ADD COLUMN `page_keywords` varchar(255) collate utf8_unicode_ci");
}
?>
