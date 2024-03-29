<?php
/**
 * Installs comments into modules
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require cot_incfile('comments', 'plug', 'enablement');

// Add options into module configs
foreach ($commentsModulesList as $mod_name)
{
	if (cot_extension_installed($mod_name) && !cot_config_implanted($mod_name, 'comments'))
	{
		cot_config_implant($mod_name, $commentsOptions, false, 'comments');
	}
}

// Add options into module structure configs
foreach ($commentsModulesStructList as $mod_name)
{
	if (cot_extension_installed($mod_name) && !cot_config_implanted($mod_name, 'comments'))
	{
		cot_config_implant($mod_name, $commentsOptions, true, 'comments');
	}
}
