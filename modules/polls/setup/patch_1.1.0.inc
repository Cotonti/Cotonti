<?php
/**
 * Update to v. 1.1.0
 */
defined('COT_CODE') or die('Wrong URL');

global $db_polls;

if (empty($db_polls)) {
    // Registering tables
    Cot::$db->registerTable('polls');
}

if (!Cot::$db->indexExists(Cot::$db->polls,  'poll_type_idx')) {
    Cot::$db->addIndex(Cot::$db->polls,  'poll_type_idx', ['poll_type']);
}

/**
 * @see \cot\modules\forums\inc\ForumsDictionary::SOURCE_TOPIC
 */
Cot::$db->update(Cot::$db->polls, ['poll_type' => 'forumTopic'], "poll_type = 'forum'");