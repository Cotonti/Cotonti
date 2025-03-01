<?php
/**
 * Polls service
 * @package Polls
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\modules\polls\inc;

use cot\modules\forums\inc\ForumsDictionary;
use cot\plugins\comments\inc\CommentsDictionary;
use cot\traits\GetInstanceTrait;

class PollsService
{
    use GetInstanceTrait;

    /**
     * Sources that cannot have polls.
     * To avoid unnecessary database queries
     */
    private function getNonPollableSources(): array
    {
        $result = [];

        if (class_exists(ForumsDictionary::class)) {
            $result[] = ForumsDictionary::SOURCE_POST;
        }

        if (class_exists(CommentsDictionary::class)) {
            $result[] = CommentsDictionary::SOURCE_COMMENT;
        }

        return $result;
    }

    public function isNeedToProcessItemDelete(string $source): bool
    {
        if (in_array($source, $this->getNonPollableSources())) {
            return false;
        }

        return PollsRepository::getInstance()->getCountBySource($source) > 0;
    }
}