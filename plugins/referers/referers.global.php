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
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2014
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

cot::$db->registerTable('referers');

$sys['referer'] = mb_substr($_SERVER['HTTP_REFERER'], 0, 255);

if (!empty($sys['referer'])
	&& mb_stripos($sys['referer'], $cfg['mainurl']) === false
	&& mb_stripos($sys['referer'], $cfg['hostip']) === false
	&& mb_stripos($sys['referer'], str_ireplace('//www.', '//', $cfg['mainurl'])) === false
	&& mb_stripos(str_ireplace('//www.', '//', $sys['referer']), $cfg['mainurl']) === false)
{
	$db->query("INSERT INTO $db_referers
				(ref_url, ref_count, ref_date)
			VALUES
				('".$db->prep($sys['referer'])."', 1, {$sys['now']})
			ON DUPLICATE KEY UPDATE
				ref_count=ref_count+1, ref_date={$sys['now']}");
}
