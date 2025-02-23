<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=item.getItems
[END_COT_EXT]
==================== */

declare(strict_types = 1);

use cot\dto\ItemDto;
use cot\extensions\ExtensionsDictionary;
use cot\modules\forums\inc\ForumsDictionary;
use cot\modules\forums\inc\ForumsHelper;
use cot\modules\forums\inc\ForumsPostsRepository;
use cot\modules\forums\inc\ForumsTopicsHelper;
use cot\modules\forums\inc\ForumsTopicsRepository;

defined('COT_CODE') or die('Wrong URL');

/**
 * Forums get post items
 *
 * @package Forums
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $source
 * @var list<int|numeric-string> $sourceIds
 * @var bool $withFullItemData
 * @var list<ItemDto> $result
 */

if ($source !== ForumsDictionary::SOURCE_POST || empty($sourceIds)) {
    return;
}

// for include file
global $L, $R, $Ls;

require_once cot_incfile('forums', ExtensionsDictionary::TYPE_MODULE);

$postsIds = [];
foreach ($sourceIds as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $postsIds[] = $id;
    }
}
$postsIds = array_unique($postsIds);
if (empty($postsIds)) {
    return;
}

$postsTable = Cot::$db->quoteTableName(Cot::$db->forum_posts);
$topicTable = Cot::$db->quoteTableName(Cot::$db->forum_topics);

$condition = [
    'id' => 'fp_id IN (' . implode(',', $postsIds) . ')',
];
$conditionPrivateTopic = cot_forums_sqlExcludePrivateTopics();
if ($conditionPrivateTopic !== '') {
    $condition['privateTopic'] = $conditionPrivateTopic;
}

$sqlWhere = implode(' AND ', $condition);

$sqlSelect = $withFullItemData
    ? "{$postsTable}.*, {$topicTable}.ft_title, {$topicTable}.ft_desc, {$topicTable}.ft_cat"
    : "{$postsTable}.fp_id, {$postsTable}.fp_text, {$postsTable}.fp_posterid, {$topicTable}.ft_title";

$query = "SELECT $sqlSelect FROM $postsTable LEFT JOIN $topicTable ON {$postsTable}.fp_topicid = {$topicTable}.ft_id "
 . " WHERE $sqlWhere";

$forumPosts = Cot::$db->query($query)->fetchAll();
if (empty($forumPosts)) {
    return;
}

$helper = ForumsHelper::getInstance();

foreach ($forumPosts as $post) {
    $dto = new ItemDto(
        ForumsDictionary::SOURCE_TOPIC,
        $post['fp_id'],
        Cot::$L['forums_post'],
        $L['forums_postInTopic'] . ': "' . $post['ft_title'] . '"',
        cot_string_truncate(strip_tags($post['fp_text']), 120, false),
        $helper->getPostUrl($post, false, true),
        (int) $post['fp_posterid']
    );

    if ($withFullItemData) {
        $dto->data = $post;
    }
    $dto->titleHtml = $L['forums_postInTopic'] . ': "' . cot_rc_link($dto->url, $post['ft_title']) . '"';
    $dto->categoryCode = $post['ft_cat'];
    $dto->categoryTitle = 'Unknown';
    if (isset(Cot::$structure['forums'][$post['ft_cat']])) {
        $dto->categoryUrl = $helper->getSectionUrl($post['ft_cat']);
        $dto->categoryTitle = Cot::$structure['forums'][$post['ft_cat']]['title'];
    }

    $result[$dto->sourceId] = $dto;
}

/* === Hook === */
foreach (cot_getextplugins('forums.posts.getItems') as $pl) {
    include $pl;
}
/* ===== */

