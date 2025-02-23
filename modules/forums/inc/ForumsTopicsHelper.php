<?php
/**
 * Forum topics repository
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\forums\inc;

use Cot;
use cot\traits\GetInstanceTrait;

defined('COT_CODE') or die('Wrong URL.');

class ForumsTopicsHelper
{
    use GetInstanceTrait;

    /**
     * Get topic url
     * @param array $data Topic data
     * @param bool $htmlspecialcharsBypass If TRUE, will not convert & to &amp; and so on.
     * @param bool $absolute return absolute url?
     * @return string
     */
    public function getUrl(array $data, bool $htmlspecialcharsBypass = false, bool $absolute = false): string
    {
        $params = ['m' => 'posts'];
        if ($data['ft_movedto'] > 0) {
            $params['q'] = $data['ft_movedto'];
        } else {
            $params['q'] = $data['ft_id'];
        }

        if ($absolute) {
            return cot_absoluteUrl('forums', $params, '', $htmlspecialcharsBypass);
        }

        return cot_url('forums', $params, '', $htmlspecialcharsBypass);
    }

    /**
     * @param array $data Topic data
     */
    public function preview(array $data): string
    {
        if (empty($data['ft_preview'])) {
            return '';
        }

        $allowBBCodes = isset(Cot::$cfg['forums']['cat_' . $data['ft_cat']])
            ? Cot::$cfg['forums']['cat_' . $data['ft_cat']]['allowbbcodes']
            : Cot::$cfg['forums']['cat___default']['allowbbcodes'];
        $topicPreview = trim(cot_parse($data['ft_preview'], $allowBBCodes));

        if (!empty($topicPreview)) {
            $topicPreview .= '...';
        }

        return $topicPreview;
    }
}