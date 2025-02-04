<?php
/**
 * Update to v. 0.9.26
 */
defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

global $db_server_events, $db_server_events_observers, $db_plugins, $db_core;

if (empty($db_server_events) || empty($db_server_events_observers) || empty($db_plugins) || empty($db_core)) {
    // Registering tables
    Cot::$db->registerTable('server_events');
    Cot::$db->registerTable('server_events_observers');
    Cot::$db->registerTable('plugins');
    Cot::$db->registerTable('core');
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
        . 'INDEX server_events_observers_updated_at_idx (' . Cot::$db->quoteColumnName('updated_at') . ') '
        . ');'
    );
}

if (!Cot::$db->indexExists(Cot::$db->plugins,  'plugins_code_idx')) {
    Cot::$db->addIndex(Cot::$db->plugins,  'plugins_code_idx', ['pl_code']);
}

Cot::$db->delete(Cot::$db->core, "ct_code IN ('admin',  'message')");

$exists = (int) Cot::$db->query(
    'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(Cot::$db->config)
    . " WHERE config_owner = 'core' AND config_cat = 'main' AND config_name = 'serverEvents'"
)->fetchColumn();
if (!$exists) {
    Cot::$db->update(
        Cot::$db->config,
        ['config_order' => 11],
        "config_owner = 'core' AND config_cat = 'main' AND config_name = 'loggerlevel'"
    );
    Cot::$db->query(
        'INSERT INTO ' . Cot::$db->quoteTableName(Cot::$db->config)
        . ' (config_owner, config_cat, config_order, config_name, config_type, config_value, config_default, '
        . 'config_variants, config_text) '
        . " VALUES "
        . "('core', 'main', '09', 'serverEvents', 2, 'sse', 'sse', 'none,sse,ajax', '')"
    );
}