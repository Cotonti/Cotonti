<?php
/**
 * Installs ratings into modules
 *
 * @package ratings
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

require cot_incfile('ratings', 'plug', 'enablement');

// Add options into module configs
foreach ($rat_modules_list as $mod_name)
{
	if (cot_extension_installed($mod_name) && !cot_config_implanted($mod_name, 'ratings'))
	{
		cot_config_implant($mod_name, $rat_options, false, 'ratings');
	}
}

// Add options into module structure configs
foreach ($rat_modules_struct_list as $mod_name)
{
	if (cot_extension_installed($mod_name) && !cot_config_implanted($mod_name, 'ratings'))
	{
		cot_config_implant($mod_name, $rat_options, true, 'ratings');
	}
}
?>
