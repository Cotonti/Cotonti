<?php
/**
 * 1.0.11 in release 0.9.23 Delete page_rating field
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('page', 'module');

//global $db_pages;

if (Cot::$db->fieldExists(Cot::$db->pages, 'page_rating')) {
    if ((int) Cot::$db->query('SELECT COUNT(*) FROM ' . Cot::$db->pages . ' WHERE `page_rating` != 0')->fetchColumn() <= 0) {
        Cot::$db->query('ALTER TABLE ' . Cot::$db->pages . ' DROP `page_rating`');
        if (isset($cot_extrafields[Cot::$db->pages]['rating'])) cot_extrafield_remove(Cot::$db->pages, 'rating');
    }
}