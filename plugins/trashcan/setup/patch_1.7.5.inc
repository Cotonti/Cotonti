<?php
/**
 * Update to v. 1.7.5
 */

defined('COT_CODE') or die('Wrong URL');

global $db_trash;

if (empty($db_trash)) {
    // Registering tables
    Cot::$db->registerTable('trash');
}

/**
 * @see \cot\modules\forums\inc\ForumsDictionary::SOURCE_TOPIC
 * @see \cot\modules\forums\inc\ForumsDictionary::SOURCE_POST
 */
Cot::$db->update($db_trash, ['tr_type' => 'forumTopic'], "tr_type = 'forumtopic'");
Cot::$db->update($db_trash, ['tr_type' => 'forumPost'], "tr_type = 'forumpost'");
