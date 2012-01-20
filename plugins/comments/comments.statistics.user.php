<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=statistics.user
Tags=statistics.tpl:{STATISTICS_USER_COMMENTS}
[END_COT_EXT]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');

$sql = $db->query("SELECT COUNT(*) FROM $db_com WHERE com_authorid=".$usr['id']);
$user_comments = $sql->fetchColumn();
$t->assign(array(
	'STATISTICS_USER_COMMENTS' => $user_comments
));

?>