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
 */

defined('COT_CODE') or die('Wrong URL');

if ($row['poll_id'] > 0)
{
	$row['ft_title'] = $L['Poll'].": ".$row['ft_title'];
}

$t-> assign(array(
	'FORUMS_TOPICS_ROW_TITLE' => htmlspecialchars($row['ft_title'])
));
