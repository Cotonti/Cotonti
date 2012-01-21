<?php
/**
 * Sets HTML as default parser on clean installs
 *
 * @package html
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (!defined('COT_UPGRADE'))
{
	global $db_config, $db_core;
	$bbcode_installed = $db->query("SELECT COUNT(*) FROM $db_core WHERE ct_code = 'bbcode'")->fetchColumn() == 1;
	if (!$bbcode_installed)
	{
		// Set as default parser
		$db->update($db_config, array('config_value' => 'html'), "config_owner='core' AND config_cat = 'main' AND config_name = 'parser'");
	}
	if ($db->query("SELECT COUNT(*) FROM $db_core WHERE ct_code = 'page'")->fetchColumn() == 1)
	{
		// Set as default parser for pages
		$db->update($db_config, array('config_value' => 'html'), "config_owner='module' AND config_cat = 'page' AND config_name = 'parser'");
	}
}
?>
