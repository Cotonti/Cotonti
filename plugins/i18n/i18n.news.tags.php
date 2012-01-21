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
 * @package i18n
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
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
?>
