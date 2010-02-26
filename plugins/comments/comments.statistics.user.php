<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=statistics.user
File=comments.statistics.user
Hooks=statistics.user
Tags=statistics.tpl:{STATISTICS_USER_COMMENTS}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comments system for Cotonti
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$sql = sed_sql_query("SELECT COUNT(*) FROM $db_com WHERE com_authorid='".$usr['id']."'");
$user_comments = sed_sql_result($sql, 0, "COUNT(*)");
$t->assign(array(
	'STATISTICS_USER_COMMENTS' => $user_comments
));

?>