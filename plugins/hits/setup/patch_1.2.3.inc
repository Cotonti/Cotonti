<?php
/**
 * Update to v. 1.2.3
 */
defined('COT_CODE') or die('Wrong URL');

global $db_stats;

if (empty($db_stats)) {
    // Registering tables
    Cot::$db->registerTable('stats');
}

$exists = $tmp = Cot::$db->query("SELECT COUNT(*) FROM $db_stats WHERE stat_name = 'maxusers'")->fetchColumn();
if (!$exists) {
    $value = !empty(Cot::$sys['whosonline_all_count']) ? (int) Cot::$sys['whosonline_all_count'] : 0;
    Cot::$db->insert($db_stats, ['stat_name' => 'maxusers', 'stat_value' => $value]);
}

Cot::$db->delete($db_stats, "stat_name = 'textboxerprev'");
Cot::$db->delete($db_stats, "stat_name = 'version'");