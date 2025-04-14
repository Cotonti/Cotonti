<?php
/**
 * Comments system for Cotonti
 * Service
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

declare(strict_types=1);

namespace cot\plugins\comments\inc;

use Cot;
use cot\extensions\ExtensionsService;
use cot\modules\forums\inc\ForumsDictionary;
use cot\services\ItemService;
use cot\traits\GetInstanceTrait;

class CommentsService
{
    use GetInstanceTrait;

    private static $cacheCount = [];

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

    /**
     * Checks if comments are enabled for specific extension and category
     *
     * @param string $extensionCode Extension code
     * @param ?string $categoryCode Category code or empty if checking the entire area
     * @return bool
     *
     * @todo third parameter array with item data. check $itemData['enable_comments']
     */
    public function isEnabled(string $extensionCode, ?string $categoryCode = null): bool
    {
        if (ExtensionsService::getInstance()->isModuleActive($extensionCode)) {
            // if extension is module
            if (
                !empty($categoryCode)
                && isset(Cot::$cfg[$extensionCode]['cat_' . $categoryCode]['enable_comments'])
            ) {
                return (bool) Cot::$cfg[$extensionCode]['cat_' . $categoryCode]['enable_comments'];
            }

            if (isset(Cot::$cfg[$extensionCode]['cat___default']['enable_comments'])) {
                return (bool) Cot::$cfg[$extensionCode]['cat___default']['enable_comments'];
            }

            // Check the extension's configuration
            if (isset(Cot::$cfg[$extensionCode]['enable_comments'])) {
                return (bool) Cot::$cfg[$extensionCode]['enable_comments'];
            }
        } else {
            // if extension is plugin
            if (isset(Cot::$cfg['plugin'][$extensionCode]['enable_comments'])) {
                return (bool) Cot::$cfg['plugin'][$extensionCode]['enable_comments'];
            }
        }

        return true;
    }

    /**
     * Delete related cache
     * The method retrieves a commented object using \cot\services\ItemService::get() and clears the cache for its page.
     * Additional URL parameters may be passed, and the cache will be cleared for them as well.
     *
     * @param array $comment Comment data
     * @param ?string $additionalExtensionCode Commented item extension code (To clear static cache)
     * @param ?array $additionalItemUrlParams Commented item url params (To clear static cache)
     * @param ?string $additionalItemUrl Commented item url (To clear static cache)
     */
    public function clearRelatedCache(
        array $comment,
        ?string $additionalExtensionCode = null,
        ?array $additionalItemUrlParams = null,
        ?string $additionalItemUrl = null
    ): void {
        if (!Cot::$cache) {
            return;
        }

        $excludeAreas = ['admin', 'index', 'login', 'message', 'plug', 'comments'];
        $ciExtensionCode = $additionalExtensionCode;
        if (in_array($ciExtensionCode, $excludeAreas)) {
            $ciExtensionCode = null;
        }

        if (!empty($ciExtensionCode)) {
            $this->processClearCache(
                $ciExtensionCode,
                cot_url($ciExtensionCode, $additionalItemUrlParams)
            );
        }

        if (!empty($additionalItemUrl)) {
            Cot::$cache->static->clearByUri($additionalItemUrl);
        }

        if ($comment['com_area'] !== CommentsDictionary::SOURCE_COMMENT) {
            $commentedItem = ItemService::getInstance()->get($comment['com_area'], $comment['com_code']);
            if ($commentedItem !== null && !empty($commentedItem->url)) {
                $extensionCode = $commentedItem->extensionCode;
                if ($ciExtensionCode !== $extensionCode) {
                    $this->processClearCache(
                        $extensionCode,
                        str_replace(COT_ABSOLUTE_URL, '', $commentedItem->url)
                    );
                }
            }
        }

        if (Cot::$cfg['cache_index']) {
            Cot::$cache->static->clear('index');
        }
    }

    private function processClearCache(string $extensionCode, string $uri): void
    {
        $staticCacheIsEnabled = false;
        if (!in_array($extensionCode, ['admin', 'index', 'login', 'message', 'plug'])) {
            $staticCacheParam = 'cache_' . $extensionCode;
            $staticCacheIsEnabled = !isset(Cot::$cfg[$staticCacheParam]) || Cot::$cfg[$staticCacheParam];
        }

        // Clear commented Item url cache
        if ($staticCacheIsEnabled) {
            Cot::$cache->static->clearByUri($uri);
        }
    }

    /**
     * @param int|string|null $sourceId
     */
    public function getCount(string $source, $sourceId = null, ?array $itemData = null, bool $useCache = true): int
    {
        if ($source === '') {
            return 0;
        }

        $key = $source . '_' . ((string) $sourceId);

        if ($useCache && isset(self::$cacheCount[$key])) {
            return self::$cacheCount[$key];
        }

        if (isset($itemData['com_count'])) {
            $result = (int) $itemData['com_count'];
            self::$cacheCount[$key] = $result;

            return $result;
        }

        if (isset($itemData['comments_count'])) {
            $result = (int) $itemData['comments_count'];
            self::$cacheCount[$key] = $result;

            return $result;
        }

        $result = CommentsRepository::getInstance()->getCountBySourceId($source, $sourceId, false);
        self::$cacheCount[$key] = $result;

        return $result;
    }

    public function isNeedToProcessItemDelete(string $source): bool
    {
        if (in_array($source, $this->getNonCommentableSources())) {
            return false;
        }

        return CommentsRepository::getInstance()->getCountBySourceId($source) > 0;
    }

    /**
     * @param array $comment
     * @param bool $asList
     * @return ($asList is true ? list<string> : array<string, list<string>>)
     */
    public function validate(array $comment, bool $asList = false): array
    {
        $errors = [];
        $textLength = mb_strlen($comment['com_text']);
        if ($textLength < Cot::$cfg['plugin']['comments']['minsize']) {
            $errors['text'][] = Cot::$L['comments_tooShort'];
        }

        if (
            Cot::$cfg['plugin']['comments']['commentsize']
            && $textLength > Cot::$cfg['plugin']['comments']['commentsize']
        ) {
            $errors['text'][] = Cot::$L['comments_tooShort'];
        }

        if (empty($comment['com_area'])) {
            $errors['area'][] = 'Source (area) should not be empty';
        }

        if (empty($comment['com_code'])) {
            $errors['code'][] = 'Source (area) should not be empty';
        }

        if ($comment['com_authorid'] === null || $comment['com_authorid'] === '') {
            $errors['code'][] = 'Author ID should not be empty';
        }

        /* == Hook == */
        foreach (cot_getextplugins('comments.validate') as $pl) {
            include $pl;
        }
        /* ===== */

        if ($errors === [] || !$asList) {
            return $errors;
        }

        $result = [];
        foreach ($errors as $list) {
            foreach ($list as $error) {
                $result[] = $error;
            }
        }
        return $result;
    }

    /**
     * @param array $comment Comment Data
     */
    public function validateWithMessages(array $comment, $inputNamePrefix = 'comment_'): bool
    {
        $errors = $this->validate($comment);

        if (empty($errors)) {
            return true;
        }

        foreach ($errors as $attribute => $errorsList) {
            foreach ($errorsList as $error) {
                cot_error($error, $inputNamePrefix . $attribute);
            }
        }

        return false;
    }
}
