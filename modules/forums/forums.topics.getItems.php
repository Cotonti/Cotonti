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
use cot\modules\forums\inc\ForumsTopicsHelper;
use cot\modules\forums\inc\ForumsTopicsRepository;

defined('COT_CODE') or die('Wrong URL');

/**
 * Forums get topic items
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

if ($source !== ForumsDictionary::SOURCE_TOPIC || empty($sourceIds)) {
    return;
}

// for include file
global $L, $R, $Ls;

require_once cot_incfile('forums', ExtensionsDictionary::TYPE_MODULE);

$topicsIds = [];
foreach ($sourceIds as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $topicsIds[] = $id;
    }
}
$topicsIds = array_unique($topicsIds);

$condition = ['id' => 'ft_id IN (' . implode(',', $topicsIds) . ')'];
$conditionPrivateTopic = cot_forums_sqlExcludePrivateTopics();
if ($conditionPrivateTopic !== '') {
    $condition['privateTopic'] = $conditionPrivateTopic;
}
$forumTopics = ForumsTopicsRepository::getInstance()->getByCondition($condition);

$topicsHelper = ForumsTopicsHelper::getInstance();
$helper = ForumsHelper::getInstance();

foreach ($forumTopics as $topic) {
    $dto = new ItemDto(
        ForumsDictionary::SOURCE_TOPIC,
        $topic['ft_id'],
        Cot::$L['forums_topic'],
        $topic['ft_movedto'] > 0
            ? Cot::$L['Moved'] . ': ' . $topic['ft_title']
            : ($topic['ft_mode'] === 1 ? '# ' . $topic['ft_title'] : $topic['ft_title']),
        $topicsHelper->preview($topic),
        $topicsHelper->getUrl($topic, false, true),
        $topic['ft_firstposterid']
    );

    if ($withFullItemData) {
        $dto->data = $topic;
    }
    $dto->categoryCode = $topic['ft_cat'];
    $dto->categoryTitle = 'Unknown';

    if (isset(Cot::$structure['forums'][$topic['ft_cat']])) {
        $dto->categoryUrl = $helper->getSectionUrl($topic['ft_cat']);
        $dto->categoryTitle = Cot::$structure['forums'][$topic['ft_cat']]['title'];
    }

    $result[$dto->sourceId] = $dto;
}

/* === Hook === */
foreach (cot_getextplugins('forums.topics.getItems') as $pl) {
    include $pl;
}
/* ===== */

