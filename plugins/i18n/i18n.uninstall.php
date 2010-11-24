<?php
/**
 * Uninstallation handler
 *
 * @package i18n
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags'])
{
	// Remove i18n-specific tags
	cot_require('tags', true);
	global $db_tag_references;
	$db->query("DELETE FROM $db_tag_references WHERE tag_locale != ''");
	$db->query("ALTER TABLE $db_tag_references DROP COLUMN `tag_locale`");
}
?>
