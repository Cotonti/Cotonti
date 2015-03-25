<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=news.tags
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
       $news->assign(array(
               'PAGE_CATPATH' => cot_breadcrumbs(cot_i18n_build_catpath('page', $cat, $i18n_locale), $cfg['homebreadcrumb']),
               'PAGE_CATTITLE' => htmlspecialchars($cat_i18n['title']),
       ));
}
