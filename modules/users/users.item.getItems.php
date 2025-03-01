<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=item.getItems
[END_COT_EXT]
==================== */

declare(strict_types = 1);

use cot\dto\ItemDto;
use cot\modules\users\inc\UsersDictionary;
use cot\users\UsersHelper;
use cot\users\UsersRepository;

defined('COT_CODE') or die('Wrong URL');

/**
 * Users module
 *
 * @package Users
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var string $source
 * @var list<int|numeric-string> $sourceIds
 * @var bool $withFullItemData
 * @var list<ItemDto> $result
 */

if ($source !== UsersDictionary::SOURCE_USER || empty($sourceIds)) {
    return;
}

$usersIds = [];
foreach ($sourceIds as $id) {
    $id = (int) $id;
    if ($id > 0) {
        $usersIds[] = $id;
    }
}
$usersIds = array_unique($usersIds);

$condition = 'user_id IN (' . implode(',', $usersIds) . ')';
$users = UsersRepository::getInstance()->getByCondition($condition);

$usersHelper = UsersHelper::getInstance();

foreach ($users as $row) {
    $dto = new ItemDto(
        UsersDictionary::SOURCE_USER,
        $row['user_id'],
        Cot::$L['User'],
        $usersHelper->getFullName($row),
        '',
        $usersHelper->getUrl($row, '',false,true)
    );

    if ($withFullItemData) {
        $dto->data = $row;
    }

    $result[$dto->sourceId] = $dto;
}

/* === Hook === */
foreach (cot_getextplugins('users.item.getItems.done') as $pl) {
    include $pl;
}
/* ===== */
