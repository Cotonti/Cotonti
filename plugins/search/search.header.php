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

defined('SED_CODE') or die('Wrong URL');

if (!empty($highlight))
{
	$out['compopup'] .= '<script type="text/javascript" src="'.$cfg['plugins_dir'].'/search/js/hl.js"></script>
		<script type="text/javascript">$(document).ready(function() {$("div.fmsg").each(function() {';

	$highlight = explode(' ', $highlight);
	foreach ($highlight as $key=>$value)
	{
		$out['compopup'] .= '$.highlight(this, \''.strtoupper($value).'\');';
	}

	$out['compopup'] .= '});});</script>';
}

?>