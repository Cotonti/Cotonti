<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=usertags.main
[END_COT_EXT]
==================== */

/**
 * PM user tags
 *
 * @package PM
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

global $L, $Ls, $R;
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
