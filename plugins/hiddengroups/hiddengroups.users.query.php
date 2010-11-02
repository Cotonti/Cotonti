<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.query
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package Cotonti
 * @version 0.9.2
 * @author Koradhil, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$mode = $cfg['plugin']['hiddengroups']['mode'];

if(!cot_auth('plug', 'hiddengroups', '1'))
{
	if($mode == 'Group + Users (maingroup)')
	{
		$join_condition = "INNER JOIN $db_groups AS g ON g.grp_id=u.user_maingrp AND g.grp_hidden=0";
	}

	if($mode == 'Group + Users (subgroup)')
	{
		$where[] = "u.user_id NOT IN (SELECT gru_userid FROM $db_groups_users INNER JOIN $db_groups ON grp_id=gru_groupid WHERE grp_hidden = 1)";
	}
}

?>