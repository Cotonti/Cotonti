<?php
/**
 * Russian Language File for BBcode management
 *
 * @package bbcode
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

$L['adm_bbcode'] = 'BBCode';
$L['adm_bbcodes'] = 'BBCodes';
$L['adm_bbcodes_added'] = 'Новый BBCode успешно добавлен.';
$L['adm_bbcodes_clearcache'] = 'Очистить HTML-кэш';
$L['adm_bbcodes_clearcache_confirm'] = 'Это очистит кэш всех страниц и сообщений. Продолжить?';
$L['adm_bbcodes_clearcache_done'] = 'HTML-кэш очищен.';
$L['adm_bbcodes_confirm'] = 'Удалить данный BBCode?';
$L['adm_bbcodes_container'] = 'Контейнер';
$L['adm_bbcodes_convert_comments'] = 'Конвертировать комментарии в HTML';
$L['adm_bbcodes_convert_complete'] = 'Конвертирование завершено';
$L['adm_bbcodes_convert_confirm'] = 'Вы уверены? Операция необратима! Если не уверены, сначала сделайте бекап базы данных.';
$L['adm_bbcodes_convert_forums'] = 'Конвертировать форумы в HTML';
$L['adm_bbcodes_convert_page'] = 'Конвертировать страницы в HTML';
$L['adm_bbcodes_convert_pm'] = 'Конвертировать личные сообщения в HTML';
$L['adm_bbcodes_convert_users'] = 'Конвертировать подписи пользователей в HTML';
$L['adm_bbcodes_mode'] = 'Режим';
$L['adm_bbcodes_new'] = 'Новый BBCode';
$L['adm_bbcodes_other'] = 'Другие действия';
$L['adm_bbcodes_pattern'] = 'Шаблон';
$L['adm_bbcodes_postrender'] = 'Пост-рендер';
$L['adm_bbcodes_priority'] = 'Приоритет';
$L['adm_bbcodes_removed'] = 'BBCode удален.';
$L['adm_bbcodes_replacement'] = 'Замена';
$L['adm_bbcodes_updated'] = 'BBCode обновлен.';
$L['adm_help_bbcodes'] = <<<HTM
<ul>
<li><strong>Имя</strong> - Название BBcode (только буквы латинского алфавита, цифры и подчеркивание)</li>
<li><strong>Режим</strong> - Режим парсинга, один из: &laquo;str&raquo; (str_replace), &laquo;ereg&raquo; (eregi_replace), &laquo;pcre&raquo; (preg_replace) или &laquo;callback&raquo; (preg_replace_callback)</li>
<li><strong>Шаблон</strong> - Строка BBCode или регулярное выражение</li>
<li><strong>Замена</strong> - Строка замены, регулярная замена или тело функции обратного вызова</li>
<li><strong>Контейнер</strong> - Является ли BBCode контейнером (например, [bbcode]Какой-то текст[/bbcode])</li>
<li><strong>Приоритет</strong> - Приоритет BBCode от 0 до 255. BBCode с меньшим приоритетом обрабатывается в первую очередь, стандартный средний приоритет -- 128.</li>
<li><strong>Плагин</strong> - Код плагина/части, которой принадлежит BBCode. Только для плагинов.</li>
<li><strong>Пост-рендер</strong> - Применять BBCode к сформированному HTML-кэшу. Используйте только если ваш callback-код делает какие-то вычисления на каждом запросе.</li>
</ul>
HTM;

$L['cfg_smilies'] = array('Включить смайлики', '');

$L['info_desc'] = 'Включает поддержку ББ-кодов на сайте. Администратор может настраивать бб-коды с помощью утилиты. Также добавляет поддержку кодов смайликов.';

?>
