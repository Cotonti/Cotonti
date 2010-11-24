<?php
/**
 * Installation handler
 *
 * @package i18n
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

// Tags integration
if ($cfg['plugin']['tags'])
{
	require_once cot_incfile('tags', 'plug');
	global $db_tag_references;
	// Add tag_locale column
	$db->query("ALTER TABLE $db_tag_references ADD COLUMN `tag_locale` VARCHAR(8) NOT NULL DEFAULT ''");
}
?>
