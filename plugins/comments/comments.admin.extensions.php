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
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require cot_incfile('comments', 'plug', 'enablement');

if ($is_module && in_array($code, $com_modules_list) && !cot_config_implanted($code, 'comments'))
{
	cot_config_implant($code, $com_options, false, 'comments');
}
elseif ($is_module && in_array($code, $com_modules_struct_list) && !cot_config_implanted($code, 'comments'))
{
	cot_config_implant($code, $com_options, true, 'comments');
}

?>
