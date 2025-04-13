<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rss.create
[END_COT_EXT]
==================== */

declare(strict_types=1);

use cot\exceptions\NotFoundHttpException;
use cot\extensions\ExtensionsService;
use cot\modules\page\inc\PageDictionary;
use cot\modules\page\inc\PageRepository;
use cot\plugins\comments\inc\CommentsRepository;
use cot\users\UsersHelper;
use cot\users\UsersRepository;

/**
 * Comments system for Cotonti
 * Pages comments rss feed
 *
 * Example of feeds:
 *  cot_url(rss, m=comments&id=XX)   Show comments from page "XX", where XX - page ID
 *  cot_url(rss, m=comments)         Show comments from all pages
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $m Rss mode (area)
 * @var bool $default_mode
 * @var string $rss_title
 * @var string $rss_description
 * @var list<array{title: string, description: string, pubDate: string, link: string, fields: ?string}> $items Rss items
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('comments', 'plug');
require_once cot_incfile('page', 'module');

if ($m !== 'comments' || !ExtensionsService::getInstance()->isModuleActive('page')) {
    return;
}

$default_mode = false;

$rss_title = Cot::$L['comments_comments'] . ': ' . Cot::$cfg['maintitle'];
$rss_description = Cot::$L['comments_rssForPages'];

$id = cot_import('id', 'G', 'INT');

$commentsRssLimit = !empty(Cot::$cfg['rss']['rss_maxitems']) ? (int) Cot::$cfg['rss']['rss_maxitems'] : 0;
if ($commentsRssLimit < 1) {
    $commentsRssLimit = 200;
}

$comments = CommentsRepository::getInstance()->getBySourceId(
    PageDictionary::SOURCE_PAGE,
    !empty($id) ? (string) $id : null,
    'com_id DESC',
    $commentsRssLimit
);

if (empty($comments)) {
    return;
}

$pagesIds = [];
$usersIds = [];
foreach ($comments as $comment) {
    if (!empty($comment['com_code'])) {
        $pageId = (int) $comment['com_code'];
        if (!in_array($pageId, $pagesIds)) {
            $pagesIds[] = $pageId;
        }
    }
    if (!empty($comment['com_authorid'])) {
        if (!in_array($comment['com_authorid'], $usersIds)) {
            $usersIds[] = $comment['com_authorid'];
        }
    }
}

$commentedPages = [];
$commentAuthors = [];

if (!empty($pagesIds)) {
    $commentRelatedItems = PageRepository::getInstance()
        ->getByCondition(['page_id IN (' . implode(',', $pagesIds) . ')']);
    if (!empty($commentRelatedItems)) {
        foreach ($commentRelatedItems as $item) {
            $commentedPages[$item['page_id']] = $item;
        }
    }
}

if (!empty($usersIds)) {
    $commentRelatedItems = UsersRepository::getInstance()
        ->getByCondition(['user_id IN (' . implode(',', $usersIds) . ')']);
    if (!empty($commentRelatedItems)) {
        foreach ($commentRelatedItems as $item) {
            $commentAuthors[$item['user_id']] = $item;
        }
    }
}
unset($commentRelatedItems);

if (
    !empty($id)
    && (
        empty($commentedPages[$id])
        || !cot_auth('page', $commentedPages[$id]['page_cat'], 'R')
    )
) {
    throw new NotFoundHttpException();
}

$usersHelper = UsersHelper::getInstance();

foreach ($comments as $comment) {
    $commentedPage = !empty($commentedPages[$comment['com_code']])
        ? $commentedPages[$comment['com_code']]
        : null;

    if ($commentedPage !== null && !cot_auth('page', $commentedPage['page_cat'], 'R')) {
        continue;
    }

    $commentsAuthorName = !empty($commentAuthors[$comment['com_authorid']])
        ? $usersHelper->getFullName($commentAuthors[$comment['com_authorid']])
        : $comment['com_author'];

    if (empty($id)) {
        $commentsTitle = Cot::$L['comments_commentOnPage'];
        if (!empty($commentedPage)) {
            $commentsTitle .= ' "' . $commentedPage['page_title'] . '"';
        } else {
            $commentsTitle .= ' deleted item';
        }
        $commentsTitle .= ' ' . Cot::$L['comments_rssFrom'] . ' ' . $commentsAuthorName;

    } else {
        $commentsTitle = Cot::$L['comments_rssFromUser'] . ' ' . $commentsAuthorName;
    }

    $commentsText = cot_parse($comment['com_text'], Cot::$cfg['plugin']['comments']['markup']);
    if ((int) Cot::$cfg['plugin']['comments']['rss_commentMaxSymbols'] > 0) {
        $commentsText = cot_string_truncate(
            $commentsText,
            Cot::$cfg['plugin']['comments']['rss_commentMaxSymbols'],
            true,
            false,
            '...'
        );
    }

    $commentsLink = !empty($commentedPage)
        ? cot_page_url($commentedPage, [], '#c' . $comment['com_id'], true)
        : '';
    if (!cot_url_check($commentsLink)) {
        $commentsLink = COT_ABSOLUTE_URL . $commentsLink;
    }

    $item = [
        'title' => $commentsTitle,
        'description' => $commentsText,
        'link' => $commentsLink,
        'pubDate' => cot_date('r', $comment['com_date'])
    ];

    $items[] = $item;
}

if (!empty($id)) {
    $rss_title = Cot::$L['comments_rssCommentsOnPage'] . ' "' . $commentedPages[$id]['page_title'] . '"';
    $rss_description = Cot::$L['comments_rssForPage'];

    $pageUrl = cot_page_url($commentedPage, [], '', true);
    if (!cot_url_check($pageUrl)) {
        $pageUrl = COT_ABSOLUTE_URL . $pageUrl;
    }

    // Attach original page text as last item
    $item = [
        'title' => Cot::$L['comments_rssOriginal'],
        'description' => cot_parse_page_text($commentedPages[$id]['page_text'], $pageUrl, $commentedPages[$id]['page_parser']),
        'link' => $pageUrl,
        'pubDate' => cot_date('r', $commentedPages[$id]['page_date']),
        'fields' => cot_generate_pagetags($commentedPages[$id]),
    ];

    $items[] = $item;
}
