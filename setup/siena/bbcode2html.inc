<?php
/**
 * A tool that converts BBcode pages, comments, posts, pms into HTML.
 * Install bbcode plugin before running it. Uninstall bbcode plugin afterwards.
 * Alternatively, it can take HTML from existing HTML cache.
 * It drops all existing HTML cache at the end.
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Trustmaster
 * @copyright Copyright (c) 2008-2011 Cotonti Team
 * @license BSD License
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

require_once cot_incfile('bbcode', 'plug');

// Pages
global $db_pages;
require_once cot_incfile('page', 'module');
// Attempt to override from HTML cache
$db->query("UPDATE $db_pages SET page_text = page_html WHERE page_html != '' AND page_type = 0");
// Update manually
$res = $db->query("SELECT page_text, page_id FROM $db_pages WHERE page_html = '' AND page_type = 0");
while ($row = $res->fetch())
{
	$html = cot_bbcode_parse($row['page_text']);
	$db->update($db_pages, array('page_text' => $html), 'page_id = ' . $row['page_id']);
}
$res->closeCursor();
// Drop HTML cache and page_type
$db->query("ALTER TABLE $db_pages DROP COLUMN page_html");
$db->query("ALTER TABLE $db_pages DROP COLUMN page_type");

// Forums
global $db_forum_posts;
require_once cot_incfile('forums', 'module');
// Attempt to override from HTML cache
$db->query("UPDATE $db_forum_posts SET fp_text = fp_html WHERE fp_html != ''");
// Update manually
$res = $db->query("SELECT fp_text, fp_id FROM $db_forum_posts WHERE fp_html = ''");
while ($row = $res->fetch())
{
	$html = cot_bbcode_parse($row['fp_text']);
	$db->update($db_forum_posts, array('fp_text' => $html), 'fp_id = ' . $row['fp_id']);
}
$res->closeCursor();
// Drop HTML cache
$db->query("ALTER TABLE $db_forum_posts DROP COLUMN fp_html");

// PMs
global $db_pm;
require_once cot_incfile('pm', 'module');
// Attempt to override from HTML cache
$db->query("UPDATE $db_pm SET pm_text = pm_html WHERE pm_html != ''");
// Update manually
$res = $db->query("SELECT pm_text, pm_id FROM $db_pm WHERE pm_html = ''");
while ($row = $res->fetch())
{
	$html = cot_bbcode_parse($row['pm_text']);
	$db->update($db_pm, array('pm_text' => $html), 'pm_id = ' . $row['pm_id']);
}
$res->closeCursor();
// Drop HTML cache
$db->query("ALTER TABLE $db_pm DROP COLUMN pm_html");

// Comments
global $db_com;
require_once cot_incfile('comments', 'plug');
// Attempt to override from HTML cache
$db->query("UPDATE $db_com SET com_text = com_html WHERE com_html != ''");
// Update manually
$res = $db->query("SELECT com_text, com_id FROM $db_com WHERE com_html = ''");
while ($row = $res->fetch())
{
	$html = cot_bbcode_parse($row['com_text']);
	$db->update($db_pm, array('com_text' => $html), 'com_id = ' . $row['com_id']);
}
$res->closeCursor();
// Drop HTML cache
$db->query("ALTER TABLE $db_com DROP COLUMN com_html");

// User signatures
global $db_users;
// Update manually
$res = $db->query("SELECT user_text, user_id FROM $db_users");
while ($row = $res->fetch())
{
	$html = cot_bbcode_parse($row['user_text']);
	$db->update($db_users, array('user_text' => $html), 'user_id = ' . $row['user_id']);
}
$res->closeCursor();

?>
