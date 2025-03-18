<?php
/**
 * Installation handler
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\extensions\ExtensionsService;

defined('COT_CODE') or die('Wrong URL');

// Tags integration
if (ExtensionsService::getInstance()->isInstalled('tags')) {
    global $L, $R; // for included file

    require_once cot_incfile('i18n', 'plug');

    cot_i18n_installTagsIntegration();
}
