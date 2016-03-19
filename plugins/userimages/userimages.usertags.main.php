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

// we need globals as it's a cot_generate_usertags() scope
global $R;

require_once cot_incfile('userimages', 'plug');
require_once cot_incfile('userimages', 'plug', 'resources');

if (is_array($user_data))
{
	$userimages = cot_userimages_config_get();

	foreach ($userimages as $code => $settings)
	{
		$uimage = $user_data['user_' . $code];
		$temp_array[strtoupper($code) . '_SRC'] = $uimage;
		$temp_array[strtoupper($code)] = cot_userimages_build( is_file($uimage) ? $uimage : '', $code );
	}
}
