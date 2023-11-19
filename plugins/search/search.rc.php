<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
 * Static head resources for search
 *
 * @package Search
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (!defined('COT_ADMIN')) {
    if (Cot::$cfg['headrc_consolidate']) {
        Resources::addFile(Cot::$cfg['plugins_dir'] . '/search/js/highlight.js');
    } elseif (!empty($_GET['highlight'])) {
        Resources::linkFileFooter(Cot::$cfg['plugins_dir'] . '/search/js/highlight.js');
    }
}
