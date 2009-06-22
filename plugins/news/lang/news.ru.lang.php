<?PHP
/**
 * Russian Language File for News Plugin
 *
 * @package Cotonti
 * @version 0.1.0
 * @author Cotonti Translators Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_category'] = array('Код родительской категории новостной ленты', 'Для вывода на главной странице главной категории новостей используйте тэг {INDEX_NEWS} в файле index.tpl.');
$L['cfg_othercat'] = array('Коды дополнительных категорий, разделенные запятыми',
'Вывод дополнительных категорий  новостей осуществляется при помощи тэга {INDEX_NEWS_КОДКАТЕГОРИИ} в файле index.tpl. При помощи шаблона news.кодкатегории.tpl можно настроить отображение новостной ленты для каждой из категорий.');
$L['cfg_maxpages'] = array('Количество отображаемых страниц');

?>