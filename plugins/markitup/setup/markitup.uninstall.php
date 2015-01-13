<?php
/**
 * markItUp! uninstall handler
 *
 * @package MarItUp
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (cot_plugin_active('bbcode'))
{
	// Remove plugin bbcodes
	require_once cot_incfile('bbcode', 'plug');

	cot_bbcode_remove(0, 'markitup');
	cot_bbcode_clearcache();
}
