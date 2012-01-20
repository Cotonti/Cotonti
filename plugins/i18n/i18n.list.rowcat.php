<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.rowcat.loop
[END_COT_EXT]
==================== */

/**
 * Redefines category tags in a list of subcategories
 *
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if ($i18n_enabled && $i18n_notmain)
{
	$x_i18n = cot_i18n_get_cat($x, $i18n_locale);
	
	if ($x_i18n)
	{
		$urlparams = (!$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != $cfg['defaultlang'])
			? "c=$x&l=$i18n_locale" : "c=$x";
		$t->assign(array(
			'LIST_ROWCAT_URL' => cot_url('page', $urlparams),
			'LIST_ROWCAT_TITLE' => $x_i18n['title'],
			'LIST_ROWCAT_DESC' => $x_i18n['desc'],
		));
	}
}

?>
