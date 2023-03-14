<?php
/**
 * Uninstallation handler
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (cot_plugin_active('tags')) {
    global $db_tag_references;

    require_once cot_incfile('tags', 'plug');

    // Remove i18n-specific data
    if (cot::$db->fieldExists(cot::$db->tag_references, 'tag_locale')) {
        cot::$db->delete(cot::$db->tag_references, "tag_locale != ''");
        cot::$db->query('ALTER TABLE ' . cot::$db->tag_references . ' DROP PRIMARY KEY');
        cot::$db->query('ALTER TABLE ' . cot::$db->tag_references . ' ADD PRIMARY KEY (tag, tag_area, tag_item)');
        cot::$db->query('ALTER TABLE ' . cot::$db->tag_references . ' DROP COLUMN tag_locale');
    }
}
