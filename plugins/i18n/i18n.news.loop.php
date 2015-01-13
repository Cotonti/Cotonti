<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=news.loop
[END_COT_EXT]
==================== */

/**
 * Modifies news selection to display
 * localized entries only
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($i18n_enabled && $i18n_notmain)
{
	// Overwrite some tags
	$i18n_urlp = (!$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != $cfg['defaultlang'])
		? '&l=' . $i18n_locale : '';

	$news->assign(array(
		'PAGE_ROW_NEWSPATH' => cot_rc_link(cot_url('index', 'c=' . $pag['page_cat'] . $i18n_urlp), htmlspecialchars($cat_i18n['title'])),
		'PAGE_ROW_CATDESC' => htmlspecialchars($cat_i18n['desc']),
	));
}
