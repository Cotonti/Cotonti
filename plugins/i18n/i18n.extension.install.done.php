<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=extension.install.done
[END_COT_EXT]
==================== */

/**
 * Adds i18n support to tags when installing the tags plugin after i18n
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $name Extension code
 */

defined('COT_CODE') or die('Wrong URL');

if ($name == 'tags') {
    global $L, $R; // for included file

    require_once cot_incfile('i18n', 'plug');

    cot_i18n_installTagsIntegration();
}
