<?php
/* ====================
[BEGIN_COT_EXT]
File=search.ajax
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * Search standalone.
 *
 * @package search
 * @version 0.7.0
 * @author Neocrome, Spartan, Boss, esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

$q = strtolower(cot_import('q', 'G', 'TXT'));
$q = cot_db_prep(urldecode($q));
if (!empty($q))
{
	$res = array();
	$sql = cot_db_query("SELECT `user_name` FROM $db_users WHERE `user_name` LIKE '$q%'");
	while ($row = cot_db_fetchassoc($sql))
	{
		$res[] = $row['user_name'];
	}
	$userlist = implode("\n", $res);
	cot_sendheaders();
}
echo $userlist;

?>