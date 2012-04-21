<?php
/**
 * 0.9.9-01 Uninstall shield plugin, it's merged back to the core
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

if (cot_extension_installed('shield'))
{
	cot_extension_uninstall('shield');
}

?>
