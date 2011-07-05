<?php
/**
 * r2145 Set default parsing mode and editor
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

global $db_config;

if (cot_extension_installed('bbcode'))
{
	// All to bbcode
	$db->update($db_config, array('config_value' => 'bbcode'), "config_owner = 'core' AND config_cat = 'main' AND config_name = 'parser'");
}
else
{
	// All to html
	$db->update($db_config, array('config_value' => 'html'), "config_owner = 'core' AND config_cat = 'main' AND config_name = 'parser'");
}

cot_extension_install('html');

?>
