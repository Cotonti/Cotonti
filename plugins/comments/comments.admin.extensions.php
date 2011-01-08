<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.extensions.install.tags
[END_COT_EXT]
==================== */

/**
 * Implants missing enablement configs when a new module is installed
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require cot_incfile('comments', 'plug', 'enablement');

cot_watch($is_module, $code, $com_modules_list, $com_modules_struct_list);

if ($is_module && in_array($code, $com_modules_list))
{
	cot_config_implant($code, $com_options);
}
elseif ($is_module && in_array($code, $com_modules_struct_list))
{
	cot_config_implant($code, $com_options, true);
}

?>
