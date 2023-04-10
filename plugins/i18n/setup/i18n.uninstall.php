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
    if (Cot::$db->fieldExists(Cot::$db->tag_references, 'tag_locale')) {
        Cot::$db->delete(Cot::$db->tag_references, "tag_locale != ''");
        Cot::$db->query('ALTER TABLE ' . Cot::$db->tag_references . ' DROP PRIMARY KEY');
        Cot::$db->query('ALTER TABLE ' . Cot::$db->tag_references . ' ADD PRIMARY KEY (tag, tag_area, tag_item)');
        Cot::$db->query('ALTER TABLE ' . Cot::$db->tag_references . ' DROP COLUMN tag_locale');
    }
}
