<?php
/**
 * Comments system for Cotonti
 * Service
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\inc;

use cot\modules\forums\inc\ForumsDictionary;
use cot\traits\GetInstanceTrait;

class CommentsService
{
    use GetInstanceTrait;

    /**
     * Sources that cannot have comments.
     * To avoid unnecessary database queries
     */
    private function getNonCommentableSources(): array
    {
        $result = [];

        if (class_exists(ForumsDictionary::class)) {
            $result[] = ForumsDictionary::SOURCE_POST;
            $result[] = ForumsDictionary::SOURCE_TOPIC;
        }

        return $result;
    }

    public function isNeedToProcessItemDelete(string $source): bool
    {
        if (in_array($source, $this->getNonCommentableSources())) {
            return false;
        }

        return CommentsRepository::getInstance()->getCountBySource($source) > 0;
    }
}