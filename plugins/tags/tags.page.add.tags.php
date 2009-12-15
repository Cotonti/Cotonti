<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.add.tags
File=tags.page.add.tags
Hooks=page.add.tags
Tags=page.add.tpl:{PAGEADD_FORM_TAGS},{PAGEADD_TOP_TAGS},{PAGEADD_TOP_TAGS_HINT}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Part of plug tags
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster - Vladimir Sibirov
 * @copyright All rights reserved. 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($cfg['plugin']['tags']['pages'] && sed_auth('plug', 'tags', 'W'))
{
	if($cfg['jquery'] && $cfg['turnajax'])
	{
		$autocomplete = '<script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/jquery.autocomplete.js"></script>
		<script type="text/javascript">
		//<![CDATA[
		$(document).ready(function(){
		$(".tags").autocomplete("plug.php?r=tags", {multiple: true, minChars: 3});
		});
		//]]>
		</script>';
	}

	require_once(sed_langfile('tags'));
	$t->assign(array(
	'PAGEADD_TOP_TAGS' => $L['Tags'],
	'PAGEADD_TOP_TAGS_HINT' => $L['tags_comma_separated'],
	'PAGEADD_FORM_TAGS' => $autocomplete.'<input type="text" name="rtags" size="56" class="tags" />'
	));
	$t->parse('MAIN.TAGS');
}

?>