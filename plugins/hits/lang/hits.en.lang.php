<?php
/**
 * English Language File for Hits Plugin
 *
 * @package Hits
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

/**
 * Plugin Config
 */

$L['cfg_adminhits'] = 'Count administrator hits';
$L['cfg_timeback'] = 'Period for stats count';
$L['cfg_timeback_hint'] = '(in days)';
$L['cfg_disableactivitystats'] = 'Do not display activity stats for period';
$L['cfg_disableactivitystats_hint'] = '(displayed on the administration panel home page)';
$L['cfg_hit_precision'] = 'Optimized hit counter precision';
$L['cfg_hit_precision_hint'] = '(bigger values minimizes server load)';

$L['info_desc'] = 'Simple hit statistic recommended for small sites';

/**
 * Plugin Body
 */

$L['hits_maxhits'] = 'Maximum hitcount was reached %1$s, %2$s pages displayed this day.';

$L['hits_byyear'] = 'By year';
$L['hits_bymonth'] = 'By month';
$L['hits_byweek'] = 'By week';

$L['hits_hits'] = 'Hits for the past {$days}';
$L['hits_activity'] = 'Activity for the past {$days}';
