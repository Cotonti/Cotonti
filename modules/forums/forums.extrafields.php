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

$extra_whitelist[cot::$db->forum_posts] = array(
	'name' => cot::$db->forum_posts,
	'caption' => cot::$L['Module'].' Forums',
	'type' => 'module',
	'code' => 'forums',
	'tags' => array(
		'forums.posts.tpl' => '{FORUMS_POSTS_ROW_XXXXX}, {FORUMS_POSTS_ROW_XXXXX_TITLE}, {FORUMS_POSTS_NEWPOST_XXXXX}, {FORUMS_POSTS_NEWPOST_XXXXX_TITLE}',
		'forums.editpost.tpl' => '{FORUMS_EDITPOST_XXXXX}, {FORUMS_EDITPOST_XXXXX_TITLE}',
		'forums.newtopic.tpl' => '{FORUMS_NEWTOPIC_XXXXX}, {FORUMS_NEWTOPIC_XXXXX_TITLE}',
	)
);

$extra_whitelist[cot::$db->forum_topics] = array(
	'name' => cot::$db->forum_topics,
	'caption' => cot::$L['Module'].' Forums',
	'type' => 'module',
	'code' => 'forums',
	'tags' => array(
		'forums.posts.tpl' => '{FORUMS_POSTS_TOPIC_XXXXX}, {FORUMS_POSTS_TOPIC_XXXXX_TITLE}',
		'forums.editpost.tpl' => '{FORUMS_EDITPOST_TOPIC_XXXXX}, {FORUMS_EDITPOST_TOPIC_XXXXX_TITLE}',
		'forums.newtopic.tpl' => '{FORUMS_NEWTOPIC_TOPIC_XXXXX}, {FORUMS_NEWTOPIC_TOPIC_XXXXX_TITLE}',
		'forums.topics.tpl' => '{FORUMS_TOPICS_ROW_XXXXX}, {FORUMS_TOPICS_ROW_XXXXX_TITLE}',
	)
);
