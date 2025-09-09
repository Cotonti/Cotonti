<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
 * Head resources
 *
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\extensions\ExtensionsService;

defined('COT_CODE') or die('Wrong URL');

if (Cot::$cfg['plugin']['tags']['css']) {
    Resources::addFile(Cot::$cfg['plugins_dir'] . '/tags/tpl/tags.css', 'css');
}
