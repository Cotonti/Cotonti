<?php
/**
 * Update to v. 0.9.23
 */
defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

$exists = (int) Cot::$db->query(
    'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(Cot::$db->config)
        . " WHERE `config_owner` = 'core' AND `config_cat` = 'main' AND `config_name` = 'loggerlevel'"
)->fetchColumn();
if (!$exists) {
    Cot::$db->query(
        'INSERT INTO ' . Cot::$db->quoteTableName(Cot::$db->config)
        . ' (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, '
            . '`config_default`, `config_variants`, `config_text`) '
        . " VALUES "
        . "('core', 'main', '08', 'loggerlevel', 2, 'sec+adm+ext', 'sec+adm+ext', "
            . "'none,sec,adm,ext,sec+adm,sec+ext,adm+ext,sec+adm+ext,all', '')"
    );
}

Cot::$db->query(
    'ALTER TABLE ' . Cot::$db->quoteTableName(Cot::$db->logger) . " MODIFY `log_group` varchar(64) DEFAULT 'adm'"
);

if (!Cot::$db->fieldExists(Cot::$db->logger, 'log_uid')) {
    Cot::$db->query(
        'ALTER TABLE ' . Cot::$db->quoteTableName(Cot::$db->logger)
        . " ADD `log_uid` int UNSIGNED NOT NULL DEFAULT 0, "
        . " ADD `log_type` varchar(32) DEFAULT '', "
        . " ADD `log_status` varchar(24) DEFAULT '', "
        . " ADD `log_uri` varchar(255) DEFAULT '' "
    );
}

Cot::$db->update(Cot::$db->logger, ['log_group' => 'forums'], "`log_group` = 'for'");
Cot::$db->update(Cot::$db->logger, ['log_group' => 'users'], "`log_group` = 'usr'");
Cot::$db->update(Cot::$db->logger, ['log_group' => 'page'], "`log_group` = 'pag'");

Cot::$db->update(
    Cot::$db->config,
    ['config_default' => 'UTC'],
    "`config_owner` = 'core' AND `config_cat` = 'locale' AND `config_name` = 'defaulttimezone'"
);
Cot::$db->update(
    Cot::$db->config,
    ['config_value' => 'UTC'],
    "`config_owner` = 'core' AND `config_cat` = 'locale' AND `config_name` = 'defaulttimezone' "
      . " AND (`config_value` = 'GMT' OR `config_value` = '0' OR `config_value` IS NULL)"
);
Cot::$db->update(
    Cot::$db->config,
    ['config_value' => 'Europe/Kiev'],
    "`config_owner` = 'core' AND `config_cat` = 'locale' AND `config_name` = 'defaulttimezone' "
        . " AND `config_value` = 'Europe/Kyiv'"
);
