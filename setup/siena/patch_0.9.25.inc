<?php
/**
 * Update to v. 0.9.25
 */
defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

Cot::$db->update(
    Cot::$db->config,
    ['config_default' => '{MAINTITLE} - {DESCRIPTION} {SUBTITLE}'],
    "`config_owner` = 'core' AND `config_cat` = 'title' AND `config_name` = 'title_header_index'"
);

$exists = (int) Cot::$db->query(
    'SELECT COUNT(*) FROM ' . Cot::$db->quoteTableName(Cot::$db->config)
        . " WHERE `config_owner` = 'core' AND `config_cat` = 'security' AND `config_name` = 'logwronginput'"
)->fetchColumn();
if (!$exists) {
    Cot::$db->query(
        'INSERT INTO ' . Cot::$db->quoteTableName(Cot::$db->config)
        . ' (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, '
            . '`config_default`, `config_variants`, `config_text`) '
        . " VALUES "
        . "('core', 'security', '40', 'logwronginput', 3, '1', '1', '', '')"
    );
}