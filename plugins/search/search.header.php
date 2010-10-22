<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
[END_COT_EXT]
==================== */

/**
 * @package search
 * @version 0.7.0
 * @author oc
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if (!empty($highlight))
{
	cot_headrc_file($cfg['plugins_dir'].'/search/js/hl.js');
	$search_embed = '$(document).ready(function() {$("div.fmsg").each(function() {';

	$highlight = explode(' ', $highlight);
	foreach ($highlight as $key=>$value)
	{
		$search_embed .= '$.highlight(this, \''.strtoupper($value).'\');';
	}

	$search_embed .= '});});';
	cot_headrc_embed('search.highlight', $search_embed, 'request');
}

?>