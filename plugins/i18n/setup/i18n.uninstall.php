<?php
/**
 * Uninstallation handler
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (cot_plugin_active('tags'))
{
	// Remove i18n-specific tags
	require_once cot_incfile('tags', 'plug');
	global $db_tag_references;
	$db->delete($db_tag_references, "tag_locale != ''");
	$db->query("ALTER TABLE $db_tag_references DROP PRIMARY KEY");
	$db->query("ALTER TABLE $db_tag_references ADD PRIMARY KEY (`tag`,`tag_area`,`tag_item`)");
	$db->query("ALTER TABLE $db_tag_references DROP COLUMN `tag_locale`");
}
