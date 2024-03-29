<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=admin.extrafields.first
  [END_COT_EXT]
  ==================== */

/**
 * Users module
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

$extra_whitelist[Cot::$db->users] = [
	'name' => Cot::$db->users,
	'caption' => Cot::$L['Module'] . ' Users',
	'type' => 'module',
	'code' => 'users',
	'tags' => [
		'users.profile.tpl' => '{USERS_PROFILE_XXXXX}, {USERS_PROFILE_XXXXX_TITLE}',
		'users.edit.tpl' => '{USERS_EDIT_XXXXX}, {USERS_EDIT_XXXXX_TITLE}',
		'users.details.tpl' => '{USERS_DETAILS_XXXXX}, {USERS_DETAILS_XXXXX_TITLE}',
		'user.register.tpl' => '{USERS_REGISTER_XXXXX}, {USERS_REGISTER_XXXXX_TITLE}',
		'forums.posts.tpl' => '{FORUMS_POSTS_ROW_USER_XXXXX}, {FORUMS_POSTS_ROW_USER_XXXXX_TITLE}',
	],
];
