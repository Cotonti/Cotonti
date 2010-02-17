<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=header
File=tags.header
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Tags: supplimentary files connection
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['pages']
	&& (defined('SED_INDEX') || defined('SED_LIST') || defined('SED_PAGE'))
	|| $cfg['plugin']['tags']['forums'] && defined('SED_FORUMS')
	|| defined('SED_PLUG'))
{
	require_once sed_langfile('tags', 'plug');
	require_once $cfg['plugins_dir'].'/tags/inc/resources.php';
	$out['compopup'] .= $R['tags_code_style'];
	if ($cfg['jquery'] && $cfg['turnajax'] && $cfg['plugin']['tags']['autocomplete'] > 0
		&& in_array($m, array('edit', 'editpost', 'posts', 'newtopic'))
		&& sed_auth('plug', 'tags', 'W'))
	{
		$out['compopup'] .= '<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
$(".autotags").autocomplete("'.sed_url('plug', 'r=tags').'", {multiple: true, minChars: '.$cfg['plugin']['tags']['autocomplete'].'});
});
//]]>
</script>';
	}
}

?>