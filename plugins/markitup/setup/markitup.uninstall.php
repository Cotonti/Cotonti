<?php
/**
 * markItUp! uninstall handler
 *
 * @package markitup
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (cot_plugin_active('bbcode'))
{
	// Remove plugin bbcodes
	require_once cot_incfile('bbcode', 'plug');

	cot_bbcode_remove(0, 'markitup');
	cot_bbcode_clearcache();
}
?>
