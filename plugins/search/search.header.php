<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
Tags=header.tpl:{HEADER_HEAD}
[END_COT_EXT]
==================== */

/**
 * Dynamic head resources for search
 *
 * @package Search
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (!empty($highlight) && $cfg['jquery'])
{
	$search_embed = '$(document).ready(function() {$("body").each(function() {';

	$highlight = explode(' ', $highlight);
	foreach ($highlight as $key=>$value)
	{
		$search_embed .= '$.highlight(this, "'.mb_strtoupper($value).'");';
	}

	$search_embed .= '});});';
	cot_rc_embed($search_embed);
}
