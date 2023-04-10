<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Referers
 *
 * @package Referers
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

Cot::$db->registerTable('referers');

Cot::$sys['referer'] = '';
if (!empty($_SERVER['HTTP_REFERER']))  Cot::$sys['referer'] = mb_substr($_SERVER['HTTP_REFERER'], 0, 255);

if (!empty(Cot::$sys['referer'])
	&& mb_stripos(Cot::$sys['referer'], Cot::$cfg['mainurl']) === false
	&& (empty(Cot::$cfg['hostip']) || mb_stripos(Cot::$sys['referer'], Cot::$cfg['hostip']) === false)
	&& mb_stripos(Cot::$sys['referer'], str_ireplace('//www.', '//', Cot::$cfg['mainurl'])) === false
	&& mb_stripos(str_ireplace('//www.', '//', Cot::$sys['referer']), Cot::$cfg['mainurl']) === false)
{

    $now = Cot::$sys['now'];
	Cot::$db->query("INSERT INTO $db_referers
				(ref_url, ref_count, ref_date)
			VALUES
				('".Cot::$db->prep(Cot::$sys['referer'])."', 1, {$now})
			ON DUPLICATE KEY UPDATE
				ref_count=ref_count+1, ref_date={$now}");
}
