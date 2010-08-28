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
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD License
 */

defined('SED_CODE') or die('Wrong URL');

$sqql_where .= " AND (poll_type='forum' OR poll_id IS NULL)";
$sqql_join_ratings_columns = ', p.poll_id';
$sqql_join_ratings_condition = ' LEFT JOIN
	$db_polls AS p ON t.ft_id=p.poll_code';

?>