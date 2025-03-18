<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.structure.delete.done
[END_COT_EXT]
==================== */

/**
 * Removes category translations
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$db->delete($db_i18n_structure, "istructure_code = ?", $c);
cot_log('Deleted translate for category "' . $c . '"', 'i18n', 'structure', 'delete');
