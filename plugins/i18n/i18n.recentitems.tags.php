<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=recentitems.recentpages.tags
[END_COT_EXT]
==================== */

/**
 * Modifies recentitems loop tags
 *
 * @package I18n
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if ($i18n_notmain && !empty($pag['ipage_title'])) {
	// Overwrite some tags
	if ((int) $titlelength > 0 && mb_strlen($pag['ipage_title']) > $titlelength) {
			$pag['ipage_title'] = (cot_string_truncate($pag['ipage_title'], $titlelength, false)) . '...';
	}
	$recentitems->assign([
		'PAGE_ROW_TITLE' => htmlspecialchars($pag['ipage_title']),

        // @deprecated in 0.9.24
        'PAGE_ROW_SHORTTITLE' => htmlspecialchars($pag['ipage_title']),
        // /@deprecated
	]);
}
