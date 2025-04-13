<?php
/**
 * Comments system for Cotonti
 * Update to v. 2.0.0
 */

defined('COT_CODE') or die('Wrong URL');

global $db_com;

if (empty($db_com)) {
    // Registering tables
    Cot::$db->registerTable('com');
}

/**
 * @see \cot\modules\polls\inc\PollsDictionary::SOURCE_POLL
 */
Cot::$db->update($db_com, ['com_area' => 'poll'], "com_area = 'polls'");

// Comments for categories should be attached the same way as for all other items.
$query = "SELECT * FROM {$db_com} WHERE com_area = 'page' AND com_code NOT REGEXP '^[0-9]+$';";
$categoryComments = Cot::$db->query($query)->fetchAll();
if (!empty($categoryComments)) {
    foreach ($categoryComments as $comment) {
        if (empty(Cot::$structure['page'][$comment['com_code']])) {
            // Comment on a non-existent category.
            Cot::$db->delete($db_com, 'com_id = ' . $comment['com_id']);
            continue;
        }

        /** @see StructureDictionary::SOURCE_CATEGORY */
        Cot::$db->update(
            $db_com,
            ['com_area' => 'category', 'com_code' => Cot::$structure['page'][$comment['com_code']]['id']],
            'com_id = ' . $comment['com_id']
        );
    }
}

// Obsolete fields
if (Cot::$db->fieldExists($db_com, 'com_count')) {
    Cot::$db->query("ALTER TABLE {$db_com} DROP COLUMN com_count");
    Cot::$db->query("ALTER TABLE {$db_com} DROP COLUMN com_isspecial");
}

Cot::$db->query("ALTER TABLE {$db_com} MODIFY com_area VARCHAR(64) NOT NULL AFTER com_id");
