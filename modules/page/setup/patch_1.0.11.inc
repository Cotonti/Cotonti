<?php
/**
 * 1.0.11 in release 0.9.23 Delete page_rating field
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');

if (
    Cot::$db->fieldExists(Cot::$db->pages, 'page_rating')
    && !isset(Cot::$extrafields[Cot::$db->pages]['rating'])
    && (Cot::$db->query('SELECT * FROM ' . Cot::$db->pages . ' WHERE `page_rating` != 0 LIMIT 1')->rowCount() < 1)
) {
    Cot::$db->query('ALTER TABLE ' . Cot::$db->pages . ' DROP `page_rating`');
}