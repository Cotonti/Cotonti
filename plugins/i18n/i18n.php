<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Standalone item translation tool
 *
 * @package i18n
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2010
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

cot_block($i18n_write);

cot_require_api('forms');

if ($m == 'structure')
{
	include cot_incfile('i18n', 'structure', true);
}
elseif ($m == 'page')
{
	include cot_incfile('i18n', 'page', true);
}
else
{
	/* === Hook === */
	foreach (cot_getextplugins('i18n.standalone') as $pl)
	{
		include $pl;
	}
	/* =============*/
}
?>
