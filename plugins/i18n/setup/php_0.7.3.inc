<?php
/**
 * Adds tags support to i18n and vice versa
 *
 * @package i18n
 * @version 0.7.3
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010-2011
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

// Tags integration
if ($cfg['plugin']['tags'])
{
	global $db_tag_references, $db_x;
	require_once cot_incfile('tags', 'plug');
	// Add tag_locale column
	$db->query("ALTER TABLE $db_tag_references ADD COLUMN `tag_locale` VARCHAR(8) NOT NULL DEFAULT ''");
}
?>
