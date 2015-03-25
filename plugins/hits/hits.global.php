<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=global
[END_COT_EXT]
==================== */

/**
 * Hits counter
 *
 * @package Hits
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (!defined('COT_ADMIN') && ($cfg['plugin']['hits']['adminhits'] || $usr['maingrp'] != COT_GROUP_SUPERADMINS))
{
	require_once cot_incfile('hits', 'plug');

	if ($cache && $cache->mem)
	{
		$hits = $cache->mem->inc('hits', 'system');
		$cfg['plugin']['hits']['hit_precision'] > 0 || $cfg['plugin']['hits']['hit_precision'] = 100;
		if ($hits % $cfg['plugin']['hits']['hit_precision'] == 0)
		{
			cot_stat_inc('totalpages', $cfg['plugin']['hits']['hit_precision']);
			cot_stat_inc($sys['day'], $cfg['plugin']['hits']['hit_precision']);
		}
	}
	else
	{
		cot_stat_inc('totalpages');
		cot_stat_update($sys['day']);
	}
}
