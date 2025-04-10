<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=structure.getUrl
[END_COT_EXT]
==================== */

/**
 * Forum structure url
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $structureArea
 * @var string $categoryCode
 * @var string $result
 */

defined('COT_CODE') or die('Wrong URL');

if ($structureArea === 'forums') {
    $result = cot_url('forums', ['m' => 'topics', 's' => $categoryCode]);
}
