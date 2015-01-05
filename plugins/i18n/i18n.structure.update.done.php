<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=structure.update.done
[END_COT_EXT]
==================== */

/**
 * Update category translations
 *
 * @package i18n
 * @version 0.9.18
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2015
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

global $db_i18n_structure;

if ($extension == 'page' && $old_data['structure_code'] != $new_data['structure_code']) {
    cot::$db->update($db_i18n_structure, array('istructure_code' => $new_data['structure_code']),
        "istructure_code=".cot::$db->quote($old_data['structure_code']));
}
