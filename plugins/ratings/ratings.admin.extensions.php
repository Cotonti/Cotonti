<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extensions.install.tags
[END_COT_EXT]
==================== */

/**
 * Implants missing enablement configs when a new module is installed
 *
 * @package ratings
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
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

?>
