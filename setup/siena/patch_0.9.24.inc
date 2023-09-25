<?php
/**
 * Update to v. 0.9.24
 */
defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

// Remove duplicate rows from the table `cot_config`
$duplicates = Cot::$db->query('SELECT COUNT(*) as cnt, config_owner, config_cat, config_name, config_subcat '
. 'FROM ' . Cot::$db->quoteTableName(Cot::$db->config) . ' '
. 'GROUP BY config_name, config_owner, config_cat,  config_subcat '
. 'HAVING COUNT(*) > 1')->fetchAll();
if (!empty($duplicates)) {
    foreach ($duplicates as $duplicate) {
        $limit = $duplicate['cnt'] - 1;
        if ($limit === 0) {
            continue;
        }
        Cot::$db->query(
            'DELETE FROM ' . Cot::$db->quoteTableName(Cot::$db->config) . ' '
            . "WHERE config_owner = '{$duplicate['config_owner']}' "
            . "AND config_cat = '{$duplicate['config_cat']}' "
            . "AND config_name = '{$duplicate['config_name']}' "
            . "AND config_subcat = '{$duplicate['config_subcat']}' "
            . "LIMIT {$limit}"
        );
    }
}

// Create unique index on `cot_config`
if (!Cot::$db->indexExists(Cot::$db->config, 'config_owner_cat_name_subcat_key')) {
    $tableData = Cot::$db->query('SHOW TABLE STATUS WHERE Name = ' . Cot::$db->quote(Cot::$db->config))
        ->fetch();
    if ($tableData['Engine'] !== 'InnoDB') {
        Cot::$db->query('ALTER TABLE ' . Cot::$db->quoteTableName(Cot::$db->config) . ' ENGINE = InnoDB');
    }

    Cot::$db->query(
        'CREATE UNIQUE INDEX config_owner_cat_name_subcat_key ON ' . Cot::$db->quoteTableName(Cot::$db->config)
        . ' (config_owner, config_cat, config_name, config_subcat)'
    );
}

// Remove obsolete core ratings configs. It can be found on very old sites
Cot::$db->delete(Cot::$db->config, "config_owner = 'core' AND config_cat = 'ratings'");

// Create index on pl_active field of `cot_plugins`
if (!Cot::$db->indexExists(Cot::$db->plugins, 'plugins_active_idx')) {
    Cot::$db->query(
        'ALTER TABLE ' . Cot::$db->quoteTableName(Cot::$db->plugins)
        . ' ADD INDEX plugins_active_idx (pl_active)'
    );
}
