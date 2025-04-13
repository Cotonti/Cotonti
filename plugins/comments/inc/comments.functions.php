<?php
/**
 * Comments system for Cotonti
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

use cot\plugins\comments\inc\CommentsService;

defined('COT_CODE') or die('Wrong URL');

// Requirements
global $R, $L;
require_once cot_incfile('users', 'module');
require_once cot_langfile('comments', 'plug');
require_once cot_incfile('comments', 'plug', 'resources');
require_once cot_incfile('forms');

// Table names
Cot::$db->registerTable('com');
cot_extrafields_register_table('com');

/**
 * Returns number of comments for item
 * For use in templates
 *
 * @param string $source Target item source
 * @param int|string|null $sourceId Target item ID
 * @param ?array $itemData Item database row entry (optional)
 * @return int
 *
 * @see CommentsService::getCount()
 */
function cot_commentsCount(string $source, $sourceId, ?array $itemData = null): int
{
	return CommentsService::getInstance()->getCount($source, $sourceId, $itemData);
}

/**
 * Checks if comments are enabled for specific extension and category
 * For use in templates
 *
 * @param string $extensionCode
 * @param string $categoryCode
 * @return bool
 * @see CommentsService::isEnabled()
 */
function cot_commentsEnabled(string $extensionCode, string $categoryCode): bool
{
    return CommentsService::getInstance()->isEnabled($extensionCode, $categoryCode);
}

/**
 * Generates a link to the comments for a given item
 *
 * @param ?string $extensionCode Module or plugin code
 * @param array|string $urlParams Target URL params for cot_url()
 * @param string $source Item source
 * @param string|int $sourceId Item identifier
 * @param ?string $categoryCode Item category code (optional)
 * @param ?array $row Database row entry (optional)
 * @return string Rendered HTML output for comments link
 * @see CommentsService::getCount()
 */
function cot_commentsLink(
    string $extensionCode,
    $urlParams,
    string $source,
    $sourceId,
    ?string $categoryCode = null,
    ?array $row = null
): string {
    $commentsService = CommentsService::getInstance();

    if (!empty($extensionCode) && !$commentsService->isEnabled($extensionCode, $categoryCode)) {
        return '';
    }

    return cot_rc(
        'comments_link',
        [
            'url' => cot_url($extensionCode, $urlParams, '#comments'),
            'count' => Cot::$cfg['plugin']['comments']['countcomments']
                ? $commentsService->getCount($source, $sourceId, $row)
                : '',
	    ]
    );
}
