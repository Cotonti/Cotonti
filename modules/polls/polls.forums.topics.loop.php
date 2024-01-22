<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.loop
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var array $row Forum topic data
 * @var XTemplate $t
 */

defined('COT_CODE') or die('Wrong URL');

if ($row['poll_id'] > 0) {
	$row['ft_title'] = Cot::$L['Poll'] . ": " . $row['ft_title'];
}

$t-> assign([
	'FORUMS_TOPICS_ROW_TITLE' => htmlspecialchars($row['ft_title'])
]);
