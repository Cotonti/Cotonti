<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=usertags.main
[END_COT_EXT]
==================== */

/**
 * PM user tags
 *
 * @package pm
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

require_once cot_incfile('pm', 'module');

if ($user_data['user_id'] > 0)
{
	$temp_array['PM'] = cot_build_pm($user_data['user_id']);
	$temp_array['PMNOTIFY'] = $cot_yesno[$user_data['user_pmnotify']];
}
else
{
	$temp_array['PM'] = '';
	$temp_array['PMNOTIFY'] = '';
}

?>