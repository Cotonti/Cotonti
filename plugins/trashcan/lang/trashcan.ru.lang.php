<?php
/**
 * Russian Language File for Trashcan
 *
 * @package Trashcan
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$L['Trash'] = 'Удаленное';
$L['Trashcan'] = 'Корзина';
$L['core_trash'] = &$L['Trashcan'];

/**
 * Config Section
 * Trash Subsection
 */

$L['cfg_trash_forum'] = array('Удалять в корзину форумы', ' ');
$L['cfg_trash_page'] = array('Удалять в корзину страницы', ' ');
$L['cfg_trash_pm'] = array('Удалять в корзину личные сообщения', ' ');
$L['cfg_trash_prunedelay'] = array('Очищать корзину через', 'дней (0 - отключить очистку корзины)');
$L['cfg_trash_user'] = array('Удалять в корзину учетные записи пользователей', ' ');
$L['cfg_trash_comment'] = array('Удалять в корзину комментарии', ' ');

$L['info_desc'] = 'Удаление контента в корзину с возможностью восстановления';

/**
 * TrashCan Section
 */

$L['adm_help_trashcan'] = 'Записи, удаленные пользователями и модераторами<br />
- удалить окончательно: окончательно удалить запись из базы данных<br />
- восстановить: вернуть запись в базу данных<br />
<b>Внимание:</b><br />
- восстанавливая тему форума, вы восстанавливаете и все сообщения в ней<br />
- восстанавливая сообщение в удаленной теме, вы восстанавливаете саму тему и (если это возможно) все дочерние сообщения';
$L['adm_trashcan_deleted'] = "Элемент удален";
$L['adm_trashcan_prune'] = "Корзина очищена";
$L['adm_trashcan_restored'] = "Элемент восстановлен";

?>
