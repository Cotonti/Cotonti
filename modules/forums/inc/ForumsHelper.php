<?php
/**
 * Forum helper
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\forums\inc;

use cot\traits\GetInstanceTrait;

class ForumsHelper
{
    use GetInstanceTrait;

    /**
     * Get section Url
     * @param string $categoryCode
     * @param bool $htmlspecialcharsBypass If TRUE, will not convert & to &amp; and so on.
     * @param bool $absolute
     * @return string
     */
    public function getSectionUrl(string $categoryCode, bool $htmlspecialcharsBypass = false, bool $absolute = false): string
    {
        $params = ['m' => 'topics', 's' => $categoryCode];

        if ($absolute) {
            return cot_absoluteUrl('forums', $params, '', $htmlspecialcharsBypass);
        }

        return cot_url('forums', $params, '', $htmlspecialcharsBypass);
    }

    /**
     * Get post Url
     * @param array $data Post data
     * @param bool $htmlspecialcharsBypass If TRUE, will not convert & to &amp; and so on.
     * @param bool $absolute
     * @return string
     */
    public function getPostUrl(array $data, bool $htmlspecialcharsBypass = false, bool $absolute = false): string
    {
        $params = ['m' => 'posts', 'id' => $data['fp_id']];

        if ($absolute) {
            return cot_absoluteUrl('forums', $params, '', $htmlspecialcharsBypass);
        }

        return cot_url('forums', $params, '', $htmlspecialcharsBypass);
    }
}