<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * Users Names file for Autocomplete plugin
 *
 * @package Autocomplete
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$q = mb_strtolower(cot_import('q', 'G', 'TXT'));
$q = $db->prep(urldecode($q));
if (!empty($q))
{
	$res = array();
	$sql_pm_users = $db->query("SELECT `user_name` FROM $db_users WHERE `user_name` LIKE '$q%'");
	while($row = $sql_pm_users->fetch())
	{
		$res[] = $row['user_name'];
	}
	$sql_pm_users->closeCursor();
	$userlist = implode("\n", $res);
	cot_sendheaders();
}
echo $userlist;
