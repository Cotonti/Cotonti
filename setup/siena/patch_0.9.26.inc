<?php
/**
 * Update to v. 0.9.26
 */
defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

global $db_server_events, $db_server_events_observers;

if (empty($db_server_events) || empty($db_server_events_observers)) {
    // Registering tables
    Cot::$db->registerTable('server_events');
    Cot::$db->registerTable('server_events_observers');
}

if (!Cot::$db->tableExists(Cot::$db->server_events)) {
    Cot::$db->query(
        'CREATE TABLE ' . Cot::$db->quoteTableName(Cot::$db->server_events) . ' ( '
        . Cot::$db->quoteColumnName('id') . ' BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, '
        . Cot::$db->quoteColumnName('user_id') . ' INT UNSIGNED NOT NULL, '
        . Cot::$db->quoteColumnName('event') . ' VARCHAR(100) NOT NULL, '
        . Cot::$db->quoteColumnName('data') . ' TEXT NOT NULL, '
        . Cot::$db->quoteColumnName('created_at') . ' DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, '
        . 'PRIMARY KEY (' . Cot::$db->quoteColumnName('id') . '),'
        . 'INDEX server_events_user_id_idx (' . Cot::$db->quoteColumnName('user_id') . ')'
        . ');'

        . 'CREATE TABLE ' . Cot::$db->quoteTableName(Cot::$db->server_events_observers) . ' ( '
        . Cot::$db->quoteColumnName('id') . ' INT UNSIGNED NOT NULL AUTO_INCREMENT, '
        . Cot::$db->quoteColumnName('user_id') . ' INT UNSIGNED NOT NULL, '
        . Cot::$db->quoteColumnName('token') . ' VARCHAR(255) NOT NULL, '
        . Cot::$db->quoteColumnName('created_at') . ' DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, '
        . Cot::$db->quoteColumnName('updated_at') . ' DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, '
        . Cot::$db->quoteColumnName('last_event_id') . ' BIGINT UNSIGNED NOT NULL DEFAULT 0, '
        . 'PRIMARY KEY (' . Cot::$db->quoteColumnName('id') . '), '
        . 'INDEX server_events_observers_user_id_idx (' . Cot::$db->quoteColumnName('user_id') . '), '
        . 'INDEX `server_events_observers_created_at_idx` (' . Cot::$db->quoteColumnName('updated_at') . ') '
        . ');'
    );
}