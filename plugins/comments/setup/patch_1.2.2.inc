<?php
/**
 * Update to v. 1.2.2
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