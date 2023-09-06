<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.structure.loop
[END_COT_EXT]
==================== */

/**
 * Forum structure
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var XTemplate $t
 * @var string $n Extension code
 * @var string $structureCode
 * @var array $row Structure row
 * @var int $structureLevel Category nesting level
 */

defined('COT_CODE') or die('Wrong URL');

if ($n == 'forums' && $structureLevel > 1) {
    $t->assign([
        'ADMIN_STRUCTURE_JUMPTO_URL' => cot_url('forums', ['m' => 'topics', 's' => $structureCode]),
    ]);
}


