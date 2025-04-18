<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extrafields.first
[END_COT_EXT]
==================== */

/**
 * Forums module extrafields
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('forums', 'module');

$extra_whitelist[Cot::$db->forum_posts] = [
	'name' => Cot::$db->forum_posts,
	'caption' => Cot::$L['Module'] . ' Forums',
	'type' => 'module',
	'code' => 'forums',
	'tags' => [
		'forums.posts.tpl' => '{FORUMS_POSTS_ROW_XXXXX}, {FORUMS_POSTS_ROW_XXXXX_TITLE}, {FORUMS_POSTS_NEWPOST_XXXXX}, {FORUMS_POSTS_NEWPOST_XXXXX_TITLE}',
		'forums.editpost.tpl' => '{FORUMS_EDITPOST_FORM_XXXXX}, {FORUMS_EDITPOST_FORM_XXXXX_TITLE}',
		'forums.newtopic.tpl' => '{FORUMS_NEWTOPIC_FORM_XXXXX}, {FORUMS_NEWTOPIC_FORM_XXXXX_TITLE}',
	],
];

$extra_whitelist[Cot::$db->forum_topics] = [
	'name' => Cot::$db->forum_topics,
	'caption' => Cot::$L['Module'] . ' Forums',
	'type' => 'module',
	'code' => 'forums',
	'tags' => [
		'forums.posts.tpl' => '{FORUMS_POSTS_TOPIC_XXXXX}, {FORUMS_POSTS_TOPIC_XXXXX_TITLE}',
		'forums.editpost.tpl' => '{FORUMS_EDITPOST_FORM_TOPIC_XXXXX}, {FORUMS_EDITPOST_FORM_TOPIC_XXXXX_TITLE}',
		'forums.newtopic.tpl' => '{FORUMS_NEWTOPIC_FORM_TOPIC_XXXXX}, {FORUMS_NEWTOPIC_FORM_TOPIC_XXXXX_TITLE}',
		'forums.topics.tpl' => '{FORUMS_TOPICS_ROW_XXXXX}, {FORUMS_TOPICS_ROW_XXXXX_TITLE}',
	],
];
