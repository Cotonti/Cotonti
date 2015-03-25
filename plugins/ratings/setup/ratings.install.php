<?php
/**
 * Installs ratings into modules
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
