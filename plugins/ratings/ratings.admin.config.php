<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.config.edit.main,admin.structure.first
[END_COT_EXT]
==================== */

/**
 * Loads language string for enable_ratings option in Admin/Config
 *
 * @package Ratings
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_langfile('ratings', $type);
