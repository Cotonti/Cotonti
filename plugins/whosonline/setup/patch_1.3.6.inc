<?php
/**
 * Update to v. 1.3.6
 */

if (!Cot::$db->fieldExists(Cot::$db->online, 'online_url')) {
    Cot::$db->query(
        'ALTER TABLE ' . Cot::$db->quoteTableName(Cot::$db->online)
        . ' ADD ' . Cot::$db->quoteC('online_url') . " varchar(500) NOT NULL DEFAULT '' AFTER "
        . Cot::$db->quoteC('online_subloc')
    );
}
