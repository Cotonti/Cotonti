<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=input
Order=20
[END_COT_EXT]
==================== */

/**
 * Shield protection
 *
 * @package shield
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('whosonline', 'plug');
require_once cot_incfile('shield', 'plug');

if (isset($online_row)
	&& ($usr['id'] == 0 || !cot_auth('admin', 'a', 'A') || $cfg['plugin']['shield']['shield_force']))
{
	$shield_limit = $online_row['online_shield'];
	$shield_action = $online_row['online_action'];
	$shield_hammer = cot_shield_hammer($online_row['online_hammer'], $shield_action, $online_row['online_lastseen']);
	$sys['online_hammer'] = $shield_hammer;
}

?>
