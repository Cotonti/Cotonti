<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=index.main
[END_COT_EXT]
==================== */

/**
 * Category preload and title setup
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var bool $i18n_read Is current user has read permission for i18n plugin
 * @var bool $i18n_notmain is current locate is not main (not Cot::$cfg['defaultlang'])
 * @var string $i18n_locale current locale
 * @see plugins/i18n/i18n.input.php
 */

defined('COT_CODE') or die('Wrong URL');

if (
    $i18n_read
    && (!Cot::$cfg['plugin']['i18n']['omitmain'] || $i18n_notmain)
) {
    $canonicalUrlParams['l'] = $i18n_locale;
}
