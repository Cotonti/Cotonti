<?PHP
/**
 * Russian Language File for News Plugin
 *
 * @package Cotonti
 * @version 0.6.2
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_category'] = array('Коды категорий новостной ленты, разделенные запятыми',
 'Для вывода на главной странице главной(первая в списке) категории новостей используйте тэг {INDEX_NEWS} в файле index.tpl.<br />
Вывод дополнительных(остальные) категорий  новостей осуществляется при помощи тэга <strong>{INDEX_NEWS_КОДКАТЕГОРИИ}</strong> в файле index.tpl.
При помощи шаблона <strong>news.кодкатегории.tpl</strong> можно настроить отображение новостной ленты для каждой из категорий.');
$L['cfg_maxpages'] = array('Количество отображаемых страниц');
$L['cfg_addpagination'] = array('Включить переключение страниц для дополнительных категорий');

?>