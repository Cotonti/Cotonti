<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.list.rowcat.loop
[END_COT_EXT]
==================== */

/**
 * Redefines category tags in a list of subcategories
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
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
