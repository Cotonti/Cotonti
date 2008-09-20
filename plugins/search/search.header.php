<?php
/* ====================
[BEGIN_SED]
File=plugins/search/search.header.php
Version=121
Updated=2008-aug-26
Type=Plugin
Author=oc
Description=
[END_SED]
[BEGIN_SED_EXTPLUGIN]
Code=search
Part=header
File=search.header
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * @package Seditio-N
 * @version 0.0.2
 * @author oc
 * @copyright (c) 2008 Cotonti Team
 * @license BSD license
 */
 
	if (!empty($highlight))
		{
		$out['compopup'] .= '<script type="text/javascript" src="'.$cfg['plugins_dir'].'/search/js/hl.js"></script>
		<script type="text/javascript">$(document).ready(function() {$("div.fmsg").each(function() {';

		$highlight = explode(' ', $highlight);
		foreach ($highlight as $key=>$value)
			{ $out['compopup'] .= '$.highlight(this, \''.$value.'\');'; }
	
		$out['compopup'] .= '});});</script>';
		}
		
?>