<?PHP
/**
 * Russian Language File for RecentItems Plugin
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

$L['cfg_fd'] = array('Режим отображения раздела форума, в котором опубликовано сообщение', '&laquo;Standard&raquo; &mdash; раздел по умолчанию<br />&laquo;Parent only&raquo; &mdash; только родительский раздел<br />&laquo;Subforums with Master Forums&raquo; &mdash; родительский раздел и подфорум<br />&laquo;Just Topics&raquo; &mdash; только тема');
$L['cfg_maxpages'] = array('Количество ссылок на новые страницы');
$L['cfg_maxtopics'] = array('Количество ссылок на новые сообщения на форумах');
$L['cfg_redundancy'] = array('Дополнительный коэффициент загрузки', 'Данный параметр указывает во сколько раз требуется увеличить количество последних записей в базе данных по сравнению с указанными в настройках. Это позволяет избежать ошибок, которые могут возникнуть при большом количестве страниц с ограничением доступа или &laquo;приватных&raquo; тем на форуме. <br />Рекомендуемое значение &mdash; не менее 2. <br />Слишком большое значение данного коэффициента устанавливать не желательно, так как это может привести к снижению скорости работы сайта.');

?>