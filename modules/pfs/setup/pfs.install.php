<?php
/**
 * Installation handler
 *
 * @package PFS
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Add groups fields if missing
$dbres = Cot::$db->query(
    'SHOW COLUMNS FROM ' . Cot::$db->groups .
    ' WHERE ' . Cot::$db->quoteColumnName('Field') . " = 'grp_pfs_maxfile'"
);
if ($dbres->rowCount() == 0) {
    Cot::$db->query('ALTER TABLE ' . Cot::$db->groups . ' ADD COLUMN grp_pfs_maxfile INT NOT NULL DEFAULT 0');
}
$dbres->closeCursor();

$dbres = Cot::$db->query(
    'SHOW COLUMNS FROM ' . Cot::$db->groups .
    ' WHERE ' . Cot::$db->quoteColumnName('Field') . " = 'grp_pfs_maxtotal'");
if ($dbres->rowCount() == 0) {
    Cot::$db->query('ALTER TABLE ' . Cot::$db->groups . ' ADD COLUMN grp_pfs_maxtotal INT NOT NULL DEFAULT 0');
}
$dbres->closeCursor();

// 100Mb for users
Cot::$db->update(
    Cot::$db->groups,
    ['grp_pfs_maxfile' => 100000, 'grp_pfs_maxtotal' => 100000],
    'grp_id = ' . COT_GROUP_MEMBERS
);

// 1Gb for admins
Cot::$db->update(
    Cot::$db->groups,
    ['grp_pfs_maxfile' => 1000000, 'grp_pfs_maxtotal' => 1000000],
    'grp_id = ' . COT_GROUP_SUPERADMINS
);