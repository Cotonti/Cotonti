<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=news.first
Order=5
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

$i18n_enabled = $i18n_read && cot_i18n_enabled($cat);

if ($i18n_enabled && $i18n_notmain)
{
	// Get cat i18n
	$cat_i18n = cot_i18n_get_cat($cat, $i18n_locale);

	// Modify url
	if (!$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != $cfg['defaultlang'])
	{
		$news_link_params .= '&l=' . $i18n_locale;
	}

	// Modify query
	$news_join_columns .= ',i18n.*';
	$news_join_tables .= " LEFT JOIN $db_i18n_pages AS i18n ON i18n.ipage_id=p.page_id AND i18n.ipage_locale='$i18n_locale' AND i18n.ipage_id IS NOT NULL";
}
