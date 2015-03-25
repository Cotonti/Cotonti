<?php
/**
 * Installation handler
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Tags integration
if (cot_extension_installed('tags'))
{
	require_once cot_incfile('tags', 'plug');
	global $db_tag_references;
	// Add tag_locale column
	$db->query("ALTER TABLE $db_tag_references ADD COLUMN `tag_locale` VARCHAR(8) NOT NULL DEFAULT ''");
	$db->query("ALTER TABLE $db_tag_references DROP PRIMARY KEY");
	$db->query("ALTER TABLE $db_tag_references ADD PRIMARY KEY (`tag`,`tag_area`,`tag_item`, `tag_locale`)");
}
