<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.first
[END_COT_EXT]
==================== */

/**
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// SEO. To avoid page duplicate by search engines
if (!empty($i18n_notmain)) {
    if (
        cot::$env['ext'] != 'index' &&
        (cot::$env['ext'] != 'page' || empty($i18n_enabled))
    ) {
        cot::$sys['noindex'] = true;
    }
}