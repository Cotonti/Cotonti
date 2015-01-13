<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=users.query
[END_COT_EXT]
==================== */

/**
 * Hidden groups
 *
 * @package HiddenGroups
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
