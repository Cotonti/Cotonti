<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=forums.topics.query
[END_COT_EXT]
==================== */

/**
 * Polls
 *
 * @package polls
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if($cfg['forums']['cat_' . $s]['allowpolls'])
{
	$where['poll'] .= "(poll_type='forum' OR poll_id IS NULL)";
	$join_columns = ', p.poll_id, p.poll_type';
	$join_condition = " LEFT JOIN $db_polls AS p ON t.ft_id=p.poll_code";
}

?>