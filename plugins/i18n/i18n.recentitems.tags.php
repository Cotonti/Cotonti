<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=recentitems.recentpages.tags
[END_COT_EXT]
==================== */

/**
 * Modifies recentitems loop tags
 *
 * @package i18n
 * @version 0.9.3
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2012
 * @license BSD License
 */

defined('COT_CODE') or die('Wrong URL');

if ($i18n_notmain && !empty($pag['ipage_title']))
{
	// Overwrite some tags
	if ((int)$titlelength > 0 && mb_strlen($pag['ipage_title']) > $titlelength)
	{
			$pag['ipage_title'] = (cot_string_truncate($pag['ipage_title'], $titlelength, false)) . '...';
	}
	$recentitems->assign(array(
		'PAGE_ROW_SHORTTITLE' => htmlspecialchars($pag['ipage_title'])
	));
}

?>
