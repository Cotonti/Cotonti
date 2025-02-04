<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=page.delete.done
[END_COT_EXT]
==================== */

use cot\extensions\ExtensionsDictionary;

/**
 * Removes page translations on page delete
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @var int $id Deleting page id
 */

defined('COT_CODE') or die('Wrong URL');

require_once cot_incfile('i18n', ExtensionsDictionary::TYPE_PLUGIN);

Cot::$db->delete(Cot::$db->i18n_pages, 'ipage_id = ?', $id);
