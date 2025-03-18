<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=structure.delete.first
[END_COT_EXT]
==================== */

/**
 * Removes category translations
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var array $category Category data
 */

defined('COT_CODE') or die('Wrong URL');

if ($category['structure_area'] !== 'page') {
    return;
}

Cot::$db->delete(Cot::$db->i18n_structure, 'istructure_code = ?', $category['structure_code']);
cot_log(
    'Deleted translate for category "' . $category['structure_code'] . '"',
    'i18n',
    'structure',
    'delete'
);
