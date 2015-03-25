<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=usertags.main
[END_COT_EXT]
==================== */

/**
 * Avatar and photo for users
 *
 * @package UserImages
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('userimages', 'plug');
require_once cot_incfile('userimages', 'plug', 'resources');
$userimages = cot_userimages_config_get();

foreach($userimages as $code => $settings)
{
	$temp_array[strtoupper($code)] = cot_userimages_build($user_data['user_'.$code], $code);
	$temp_array[strtoupper($code).'_SRC'] = $user_data['user_'.$code];
}
