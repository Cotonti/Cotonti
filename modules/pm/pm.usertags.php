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
 *
 * @var array $user_data
 */

defined('COT_CODE') or die('Wrong URL.');

global $L, $Ls, $R, $cot_yesno;
require_once cot_incfile('pm', 'module');

if (is_array($user_data) && !empty($user_data['user_id']) && !empty($user_data['user_name'])) {
	$temp_array['PM'] = cot_build_pm($user_data['user_id']);
	$temp_array['PMNOTIFY'] = $cot_yesno[$user_data['user_pmnotify']];

} else {
	$temp_array['PM'] = '';
	$temp_array['PMNOTIFY'] = '';
}
