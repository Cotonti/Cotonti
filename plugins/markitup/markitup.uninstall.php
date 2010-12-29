<?php
/**
 * markItUp! uninstall handler
 *
 * @package markitup
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (cot_plugin_active('bbcode'))
{
	// Remove plugin bbcodes
	cot_bbcode_remove(0, 'markitup');
	cot_bbcode_clearcache();
}
?>
