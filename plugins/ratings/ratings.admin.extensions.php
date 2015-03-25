<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extensions.install.tags
[END_COT_EXT]
==================== */

/**
 * Implants missing enablement configs when a new module is installed
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require cot_incfile('ratings', 'plug', 'enablement');

if ($is_module && in_array($code, $rat_modules_list) && !cot_config_implanted($code, 'ratings'))
{
	cot_config_implant($code, $rat_options, false, 'ratings');
}
elseif ($is_module && in_array($code, $rat_modules_struct_list) && !cot_config_implanted($code, 'ratings'))
{
	cot_config_implant($code, $rat_options, true, 'ratings');
}
