<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
[END_COT_EXT]
==================== */

/**
 * Tags: supplimentary files connection
 *
 * @package tags
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

if ($cfg['plugin']['tags']['pages']
	&& (defined('COT_INDEX') || defined('COT_LIST') || defined('COT_PAGE'))
	|| $cfg['plugin']['tags']['forums'] && defined('COT_FORUMS')
	|| defined('COT_PLUG'))
{
	cot_require('tags', true);
	/*$out['compopup'] .= $R['tags_code_style'];*/
	if ($cfg['jquery'] && $cfg['turnajax'] && $cfg['plugin']['tags']['autocomplete'] > 0
		&& in_array($m, array('edit', 'editpost', 'posts', 'newtopic'))
		&& cot_auth('plug', 'tags', 'W'))
	{
		cot_headrc_file('js/jquery.autocomplete.js');
		cot_headrc_embed('tags.autocomplete', '$(document).ready(function(){
$(".autotags").autocomplete("'.cot_url('plug', 'r=tags').'", {multiple: true, minChars: '.$cfg['plugin']['tags']['autocomplete'].'});
});');
	}
}

?>