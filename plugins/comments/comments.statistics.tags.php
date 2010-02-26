<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comments
Part=statistics.tags
File=comments.statistics.tags
Hooks=statistics.tags
Tags=statistics.tpl:{STATISTICS_TOTALDBCOMMENTS}
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

$totaldbcomments = sed_sql_rowcount($db_com);
$t->assign(array(
	'STATISTICS_TOTALDBCOMMENTS' => $totaldbcomments
));

?>