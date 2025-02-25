<?php
/**
 * Tags service
 * @package Tags
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\tags\inc;

use cot\modules\forums\inc\ForumsDictionary;
use cot\plugins\comments\inc\CommentsDictionary;
use cot\traits\GetInstanceTrait;

class TagsService
{
    use GetInstanceTrait;

    /**
     * Sources that cannot have tags.
     * To avoid unnecessary database queries
     */
    private function getNonTaggableSources(): array
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
        if (in_array($source, $this->getNonTaggableSources())) {
            return false;
        }

        return TagReferencesRepository::getInstance()->getCountBySource($source) > 0;
    }
}