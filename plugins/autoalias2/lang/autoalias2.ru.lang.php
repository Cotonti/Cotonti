<?php
/**
 * Russian language file
 *
 * @package AutoAlias
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Plugin Info
 */

$L['AutoAlias2'] = 'AutoAlias 2';
$L['info_desc'] = 'Создание алиаса при его отсутствии из заголовка страницы';

/**
 * Plugin Config
 */

$L['cfg_translit'] = 'Транслитерировать нелатинские символы, если возможно';
$L['cfg_prepend_id'] = 'Добавить ID страницы в начало алиаса';
$L['cfg_on_duplicate'] = 'Число, добавляемое к неуникальному алиасу (если отключено добавление ID)';
$L['cfg_sep'] = 'Разделитель слов';
$L['cfg_lowercase'] = 'Переводить алиас в строчные буквы';

/**
 * Plugin Admin
 */

$L['aliases_written'] = 'Алиасов записано: {$count}';
$L['create_aliases'] = 'Создать алиасы из заголовков, если отсутствуют';
