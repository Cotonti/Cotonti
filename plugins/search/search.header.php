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
 * @package search
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
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

?>