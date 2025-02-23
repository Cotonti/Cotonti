<?php
/**
 * Forums topics control service
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\forums\inc;

use Cot;
use cot\traits\GetInstanceTrait;

class ForumsTopicsService
{
    use GetInstanceTrait;

    /**
     * Deletes outdated topics
     * @param string $categoryCode Category code (Section)
     * @return int Deleted topics count
     */
    public function prune(string $categoryCode): int
    {
        if (
            !isset(Cot::$cfg['forums']['cat_' . $categoryCode])
            || empty(Cot::$cfg['forums']['cat_' . $categoryCode]['autoprune'])
        ) {
            return 0;
        }

        $autoPruneDays = (int) Cot::$cfg['forums']['cat_' . $categoryCode]['autoprune'];
        if ($autoPruneDays < 1) {
            return 0;
        }

        $limit = Cot::$sys['now'] - ($autoPruneDays * 86400);
        $condition = "ft_cat = :cat AND ft_updated < $limit AND ft_sticky = 0";
        $params = ['cat' => $categoryCode];

        $topics = ForumsTopicsRepository::getInstance()->getByCondition($condition, $params);
        if (empty($topics)) {
            return 0;
        }

        $result = 0;

        $controlService = ForumsTopicsControlService::getInstance();
        foreach ($topics as $topic) {
            if ($controlService->performDelete($topic)) {
                $message = 'Deleted outdated topic #' . $topic['ft_id'];
                cot_log($message, 'forums', 'delete topic', 'done');
                $result++;
            }
        }

        return $result;
    }
}