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

require_once cot_incfile('hiddengroups', 'plug');

if(!cot_auth('plug', 'hiddengroups', '1'))
{
	$hiddenusers = implode(',', cot_hiddengroups_get(cot_hiddengroups_mode(), $type='users'));
	if($hiddenusers)
	{
		$where[] = "u.user_id NOT IN (".$hiddenusers.")";
	}
}
