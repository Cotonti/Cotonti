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
 *
 * @var XTemplate $t
 */

defined('COT_CODE') or die('Wrong URL');

if ($i18n_enabled && $i18n_notmain) {
	$x_i18n = cot_i18n_get_cat($x, $i18n_locale);

	if ($x_i18n) {
		$urlparams = (!Cot::$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != Cot::$cfg['defaultlang'])
			? ['c' => $x, 'l' => $i18n_locale]
            : ['c' => $x];

		$t->assign([
            'LIST_CAT_ROW_URL' => cot_url('page', $urlparams),
            'LIST_CAT_ROW_TITLE' => htmlspecialchars($x_i18n['title']),
            'LIST_CAT_ROW_DESCRIPTION' => $x_i18n['desc'],
		]);
        if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
            $t->assign([
                // @deprecated in 0.9.24
                'LIST_ROWCAT_URL' => cot_url('page', $urlparams),
                'LIST_ROWCAT_TITLE' => $x_i18n['title'],
                'LIST_ROWCAT_DESC' => $x_i18n['desc'],
            ]);
        }
	}
}
