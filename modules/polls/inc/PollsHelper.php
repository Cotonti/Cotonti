<?php
/**
 * Polls helper
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\polls\inc;

use cot\traits\GetInstanceTrait;

class PollsHelper
{
    use GetInstanceTrait;

    /**
     * Get poll Url
     * @param array $data Poll data
     * @param bool $htmlspecialcharsBypass If TRUE, will not convert & to &amp; and so on.
     * @param bool $absolute
     * @return string
     */
    public function getUrl(array $data, bool $htmlspecialcharsBypass = false, bool $absolute = false): string
    {
        $params = ['id' => $data['poll_id']];

        if ($absolute) {
            return cot_absoluteUrl('polls', $params, '', $htmlspecialcharsBypass);
        }

        return cot_url('polls', $params, '', $htmlspecialcharsBypass);
    }
}