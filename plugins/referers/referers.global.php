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

cot::$db->registerTable('referers');

cot::$sys['referer'] = '';
if (!empty($_SERVER['HTTP_REFERER']))  cot::$sys['referer'] = mb_substr($_SERVER['HTTP_REFERER'], 0, 255);

if (!empty(cot::$sys['referer'])
	&& mb_stripos(cot::$sys['referer'], cot::$cfg['mainurl']) === false
	&& (empty(cot::$cfg['hostip']) || mb_stripos(cot::$sys['referer'], cot::$cfg['hostip']) === false)
	&& mb_stripos(cot::$sys['referer'], str_ireplace('//www.', '//', cot::$cfg['mainurl'])) === false
	&& mb_stripos(str_ireplace('//www.', '//', cot::$sys['referer']), cot::$cfg['mainurl']) === false)
{

    $now = cot::$sys['now'];
	cot::$db->query("INSERT INTO $db_referers
				(ref_url, ref_count, ref_date)
			VALUES
				('".cot::$db->prep(cot::$sys['referer'])."', 1, {$now})
			ON DUPLICATE KEY UPDATE
				ref_count=ref_count+1, ref_date={$now}");
}
