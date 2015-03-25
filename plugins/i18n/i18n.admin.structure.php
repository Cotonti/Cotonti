<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=admin.structure.tags
Tags=admin.structure.tpl:{ADMIN_STRUCTURE_I18N_LINK}
[END_COT_EXT]
==================== */

/**
 * Locale selection
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

$t->assign(array(
	'ADMIN_STRUCTURE_I18N_LINK' => cot_rc_link(cot_url('plug', 'e=i18n&m=structure'), $L['i18n_structure']),
	'ADMIN_STRUCTURE_I18N_URL' => cot_url('plug', 'e=i18n&m=structure')
));
