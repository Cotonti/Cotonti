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

if($cot_groups[$grpid]['hidden'])
{
	if(cot_auth('users', 'a', 'A'))
	{
		return cot_rc_link(cot_url('users', 'gm='.$grpid), $cot_groups[$grpid]['name'].' ('.$L['Hidden'].')');
	}
	elseif(!cot_auth('plug', 'hiddengroups', '1'))
	{
		return $L['Hidden'];
	}
}

?>
