<?php
/**
 * Update to v. 1.0.10
 * Resync all categories
 */
defined('COT_CODE') or die('Wrong URL');

if (!function_exists('cot_page_updateStructureCounters')) {
    require_once cot_incfile('page', 'module');
}

$categories = Cot::$db->query('SELECT structure_code FROM ' . Cot::$db->structure . " WHERE structure_area='page'")
    ->fetchAll();
if (!empty($categories)) {
    foreach ($categories as $row) {
        cot_page_updateStructureCounters($row['structure_code']);
    }
}
