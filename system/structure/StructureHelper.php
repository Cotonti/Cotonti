<?php
/**
 * Structure Helper
 *
 * @package Structure
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\structure;

use cot\traits\GetInstanceTrait;

class StructureHelper
{
    use GetInstanceTrait;

    public function getUrl(string $structureArea, string $categoryCode): string
    {
        $result = cot_url($structureArea, ['c' => $categoryCode]);

        /* === Hook === */
        foreach (cot_getextplugins('structure.getUrl') as $pl) {
            include $pl;
        }
        /* ===== */

        return $result;
    }
}