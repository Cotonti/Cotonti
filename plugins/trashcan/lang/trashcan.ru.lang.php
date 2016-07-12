<?php
/**
 * Russian Language File for Trashcan
 *
 * @package TrashCan
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$L['Trash'] = 'Удаленное';
$L['Trashcan'] = 'Корзина';
$L['core_trash'] = &$L['Trashcan'];

/**
 * Config Section
 * Trash Subsection
 */

$L['cfg_trash_forum'] = 'Удалять в корзину форумы';
$L['cfg_trash_forum_hint'] = ' ';
$L['cfg_trash_page'] = 'Удалять в корзину страницы';
$L['cfg_trash_page_hint'] = ' ';
$L['cfg_trash_pm'] = 'Удалять в корзину личные сообщения';
$L['cfg_trash_pm_hint'] = ' ';
$L['cfg_trash_prunedelay'] = 'Очищать корзину через';
$L['cfg_trash_prunedelay_hint'] = 'дней (0 - отключить очистку корзины)';
$L['cfg_trash_user'] = 'Удалять в корзину учетные записи пользователей';
$L['cfg_trash_user_hint'] = ' ';
$L['cfg_trash_comment'] = 'Удалять в корзину комментарии';
$L['cfg_trash_comment_hint'] = ' ';

$L['info_desc'] = 'Удаление контента в корзину с возможностью восстановления';

/**
 * TrashCan Section
 */

$L['adm_help_trashcan'] = "Записи, удаленные пользователями и модераторами<br />\n- удалить окончательно: окончательно удалить запись из базы данных<br />\n- восстановить: вернуть запись в базу данных<br />\n<b>Внимание:</b><br />\n- восстанавливая тему форума, вы восстанавливаете и все сообщения в ней<br />\n- восстанавливая сообщение в удаленной теме, вы восстанавливаете саму тему и (если это возможно) все дочерние сообщения";
$L['adm_trashcan_deleted'] = "Элемент удален";
$L['adm_trashcan_prune'] = "Корзина очищена";
$L['adm_trashcan_restored'] = "Элемент восстановлен";
