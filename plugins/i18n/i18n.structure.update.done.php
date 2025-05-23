<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=structure.update.done
[END_COT_EXT]
==================== */

/**
 * Update category translations
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

global $db_i18n_structure;

if ($extension === 'page' && $old_data['structure_code'] != $new_data['structure_code']) {
    Cot::$db->update($db_i18n_structure, ['istructure_code' => $new_data['structure_code']],
        "istructure_code=".Cot::$db->quote($old_data['structure_code']));
    cot_log('Move translate from category "' . $old_data['structure_code'] . '" to category "' .
        $new_data['structure_code'] . '"', 'i18n', 'structure', 'edit');
}
