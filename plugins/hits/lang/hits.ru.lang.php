<?php
/**
 * Russian Language File for Hits Plugin
 *
 * @package Hits
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_adminhits'] = 'Считать посещения администраторов';
$L['cfg_timeback'] = 'Период для подсчета статистики';
$L['cfg_timeback_hint'] = '(в сутках)';
$L['cfg_disableactivitystats'] = 'Отключить показ данных об активности за период';
$L['cfg_disableactivitystats_hint'] = '(отображается на главной странице админ-панели)';
$L['cfg_hit_precision'] = 'Точность оптимизированного счётчика просмотров';
$L['cfg_hit_precision_hint'] = '(чем больше значение, тем меньше нагрузка на сервер)';

$L['info_desc'] = 'Простая статистика просмотров &mdash; для небольших сайтов';

/**
 * Plugin Body
 */

$L['hits_maxhits'] = 'Максимальное количество хитов (%2$s) зафиксировано %1$s';

$L['hits_byyear'] = 'По годам';
$L['hits_bymonth'] = 'По месяцам';
$L['hits_byweek'] = 'По неделям';

$L['hits_hits'] = 'Посещаемость за последние {$days}';
$L['hits_activity'] = 'Активность за последние {$days}';
