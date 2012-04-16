<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=admin.extrafields.first
  [END_COT_EXT]
  ==================== */

/**
 * Users module
 *
 * @package users
 * @version 0.9.4
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */
defined('COT_CODE') or die('Wrong URL');

$extra_whitelist[$db_users] = array(
	'name' => $db_users,
	'caption' => $L['Module'].' Users',
	'type' => 'module',
	'code' => 'users',
	'tags' => array(
		'users.profile.tpl' => '{USERS_PROFILE_XXXXX}, {USERS_PROFILE_XXXXX_TITLE}',
		'users.edit.tpl' => '{USERS_EDIT_XXXXX}, {USERS_EDIT_XXXXX_TITLE}',
		'users.details.tpl' => '{USERS_DETAILS_XXXXX}, {USERS_DETAILS_XXXXX_TITLE}',
		'user.register.tpl' => '{USERS_REGISTER_XXXXX}, {USERS_REGISTER_XXXXX_TITLE}',
		'forums.posts.tpl' => '{FORUMS_POSTS_ROW_USERXXXXX}, {FORUMS_POSTS_ROW_USERXXXXX_TITLE}',
	)
);
?>