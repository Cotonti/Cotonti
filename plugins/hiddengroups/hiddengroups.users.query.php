<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.query
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package hiddengroups
 * @version 1.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('hiddengroups', 'plug');

if(!cot_auth('plug', 'hiddengroups', '1'))
{
	$hiddenusers = implode(',', cot_hiddengroups_get(cot_hiddengroups_mode(), $type='users'));
	if($hiddenusers)
	{
		$where[] = "u.user_id NOT IN (".$hiddenusers.")";
	}
}

?>