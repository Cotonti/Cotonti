<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=search
Part=ajax
File=search.ajax
Hooks=ajax
Tags=
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Search standalone.
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Neocrome, Spartan, Boss, esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$q = strtolower(sed_import('q', 'G', 'TXT'));
$q = sed_sql_prep(urldecode($q));
if (!empty($q))
{
	$res = array();
	$sql = sed_sql_query("SELECT `user_name` FROM $db_users WHERE `user_name` LIKE '$q%'");
	while ($row = sed_sql_fetchassoc($sql))
	{
		$res[] = $row['user_name'];
	}
	$userlist = implode("\n", $res);
	sed_sendheaders();
}
echo $userlist;

?>