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
if (cot_extension_installed('tags')) {
    global $db_tag_references;

    require_once cot_incfile('tags', 'plug');

    // Add tag_locale column
    if (!cot::$db->fieldExists(cot::$db->tag_references, 'tag_locale')) {
        cot::$db->query('ALTER TABLE ' . cot::$db->tag_references .
            " ADD COLUMN tag_locale VARCHAR(8) NOT NULL DEFAULT ''");
        cot::$db->query('ALTER TABLE ' . cot::$db->tag_references . ' DROP PRIMARY KEY');
        cot::$db->query('ALTER TABLE ' . cot::$db->tag_references .
            ' ADD PRIMARY KEY (tag, tag_area, tag_item, tag_locale)');
    }
}
