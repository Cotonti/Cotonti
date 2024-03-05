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
 *
 * @var int $titleLength
 * @var XTemplate $recentItems
 */

defined('COT_CODE') or die('Wrong URL');

global $i18n_notmain;

if ($i18n_notmain && !empty($pag['ipage_title'])) {
	// Overwrite some tags
	if ($titleLength > 0 && mb_strlen($pag['ipage_title']) > $titleLength) {
			$pag['ipage_title'] = (cot_string_truncate($pag['ipage_title'], $titleLength, false)) . '...';
	}
    $recentItems->assign([
		'PAGE_ROW_TITLE' => htmlspecialchars($pag['ipage_title']),
	]);
    if (isset(Cot::$cfg['legacyMode']) && Cot::$cfg['legacyMode']) {
        $recentItems->assign([
            // @deprecated in 0.9.24
            'PAGE_ROW_SHORTTITLE' => htmlspecialchars($pag['ipage_title']),
        ]);
    }
}
