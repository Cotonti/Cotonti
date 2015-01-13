<?php

/* ====================
  [BEGIN_COT_EXT]
  Hooks=admin.extrafields.first
  [END_COT_EXT]
  ==================== */

/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

$extra_whitelist[$db_com] = array(
	'name' => $db_com,
	'caption' => $L['Plugin'].' Comments system',
	'type' => 'plug',
	'code' => 'comments',
	'tags' => array(
		'comments.tools.tpl' => '{ADMIN_COMMENTS_XXXXX}, {ADMIN_COMMENTS_XXXXX_TITLE}',
		'comments.tpl' => '{COMMENTS_FORM_XXXXX}, {COMMENTS_FORM_XXXXX_TITLE}, {COMMENTS_ROW_XXXXX} {COMMENTS_ROW_XXXXX_TITLE}',
	)
);
