<?php
/**
 * Adds tags support to i18n and vice versa
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
