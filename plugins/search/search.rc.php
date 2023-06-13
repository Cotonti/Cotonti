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
if (Cot::$cfg['jquery']) {
	Resources::addFile(Cot::$cfg['plugins_dir'] . '/search/js/hl.min.js');
}
