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

if ($extension == 'page' && $old_data['structure_code'] != $new_data['structure_code']) {
    cot::$db->update($db_i18n_structure, array('istructure_code' => $new_data['structure_code']),
        "istructure_code=".cot::$db->quote($old_data['structure_code']));
}
