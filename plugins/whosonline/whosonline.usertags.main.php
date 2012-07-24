<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=usertags.main
[END_COT_EXT]
==================== */

/**
 * Online user tags
 *
 * @package whosonline
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (is_array($user_data) && $user_data['user_id'] > 0 && !empty($user_data['user_name']))
{
	$temp_array['ONLINE'] = (cot_userisonline($user_data['user_id'])) ? '1' : '0';
	$temp_array['ONLINETITLE'] = (cot_userisonline($user_data['user_id'])) ? $L['Online'] : $L['Offline'];
}
else
{
	$temp_array['ONLINE'] = '0';
	$temp_array['ONLINETITLE'] = '';
}

?>