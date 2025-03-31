<?php
/**
 * Update to v. 1.0.9
 */
defined('COT_CODE') or die('Wrong URL');

global $db_tag_references;

if (empty($db_tags)) {
    // Registering tables
    Cot::$db->registerTable('tag_references');
}

/**
 * @see \cot\modules\page\inc\PageDictionary::SOURCE_PAGE
 * @see \cot\modules\forums\inc\ForumsDictionary::SOURCE_TOPIC
 */
Cot::$db->update($db_tag_references, ['tag_area' => 'page'], "tag_area = 'pages'");
Cot::$db->update($db_tag_references, ['tag_area' => 'forumTopic'], "tag_area = 'forums'");

Cot::$db->update(
    Cot::$db->config,
    ['config_value' => 'page'],
    "config_owner = 'plug' AND config_cat = 'tags' AND config_name = 'index' AND config_value = 'pages'"
);
Cot::$db->update(
    Cot::$db->config,
    ['config_value' => 'forumTopic'],
    "config_owner = 'plug' AND config_cat = 'tags' AND config_name = 'index' AND config_value = 'forums'"
);

Cot::$db->query("ALTER TABLE {$db_tag_references} MODIFY tag_area VARCHAR(64) NOT NULL default 'page';");