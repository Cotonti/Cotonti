<?php
/**
 * Update to v. 1.0.13
 */
defined('COT_CODE') or die('Wrong URL');

global $db_i18n_structure, $db_i18n_pages, $db_pages;

// Registering tables
if (empty(Cot::$db->i18n_structure)) {
    Cot::$db->registerTable('i18n_structure');
    Cot::$db->registerTable('i18n_pages');
}
if (empty(Cot::$db->pages)) {
    Cot::$db->registerTable('pages');
}

function i18nPatchRemoveAllFKs(string $tableName): void
{
    $sql = "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS "
        . " WHERE TABLE_SCHEMA = '" . Cot::$cfg['mysqldb']. "' "
        . " AND TABLE_NAME = '{$tableName}' AND CONSTRAINT_TYPE = 'FOREIGN KEY'";

    $result = Cot::$db->query($sql)->fetchAll();

    $queries = [];
    if (!empty($result)) {
        foreach ($result as $row) {
            if (empty($row['CONSTRAINT_NAME'])) {
                continue;
            }

            $queries[] = 'ALTER TABLE ' . Cot::$db->quoteT($tableName) . " DROP FOREIGN KEY {$row['CONSTRAINT_NAME']};";
        }
    }
    if ($queries !== []) {
        Cot::$db->query(implode("\n", $queries));
    }
}


$sql = "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS "
    . " WHERE TABLE_SCHEMA = '" . Cot::$cfg['mysqldb']. "' "
    . " AND TABLE_NAME = '" . Cot::$db->i18n_pages . "' AND CONSTRAINT_TYPE = 'FOREIGN KEY' "
    . " AND CONSTRAINT_NAME = 'fk_translation_pages';";

$foreignKeyExists = Cot::$db->query($sql)->fetchColumn();
if (!$foreignKeyExists) {
    // Try to remove all unneeded foreign keys
    i18nPatchRemoveAllFKs(Cot::$db->i18n_pages);

    // Add new foreign key
    $sql = 'ALTER TABLE ' . Cot::$db->quoteT(Cot::$db->i18n_pages) . ' ADD CONSTRAINT fk_translation_pages '
        . ' FOREIGN KEY (ipage_id) REFERENCES ' . Cot::$db->quoteT(Cot::$db->pages) . '(page_id) ON DELETE RESTRICT';
    Cot::$db->query($sql);
}

i18nPatchRemoveAllFKs(Cot::$db->i18n_structure);
