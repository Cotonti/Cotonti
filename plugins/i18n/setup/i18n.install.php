<?php
/**
 * Installation handler
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Tags integration
if (cot_extension_installed('tags')) {
    global $L, $R; // for included file

    require_once cot_incfile('i18n', 'plug');

    cot_i18n_installTagsIntegration();
}
