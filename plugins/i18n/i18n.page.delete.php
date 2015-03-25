<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.edit.delete.done
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

require_once cot_incfile('i18n', 'plug');
global $db_i18n_pages;

$db->delete($db_i18n_pages, "ipage_id = ?", array($id));
