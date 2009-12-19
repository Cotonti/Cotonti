<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=page.edit.tags
File=tags.page.edit.tags
Hooks=page.edit.tags
Tags=page.edit.tpl:{PAGEEDIT_FORM_TAGS},{PAGEEDIT_TOP_TAGS},{PAGEEDIT_TOP_TAGS_HINT}
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
	if($cfg['jquery'] && $cfg['turnajax'] && $cfg['plugin']['tags']['autocomplete']>0)
	{
		$autocomplete = '<script type="text/javascript" src="'.$cfg['plugins_dir'].'/tags/js/jquery.autocomplete.js"></script>
		<script type="text/javascript">
		//<![CDATA[
		$(document).ready(function(){
		$(".autotags").autocomplete("plug.php?r=tags", {multiple: true, minChars: '.$cfg['plugin']['tags']['autocomplete'].'});
		});
		//]]>
		</script>';
	}

	require_once(sed_langfile('tags'));
	$tags = sed_tag_list($id);
	$tags = implode(', ', $tags);
	$t->assign(array(
	'PAGEEDIT_TOP_TAGS' => $L['Tags'],
	'PAGEEDIT_TOP_TAGS_HINT' => $L['tags_comma_separated'],
	'PAGEEDIT_FORM_TAGS' => $autocomplete.'<input type="text" name="rtags" value="' . $tags . '"  size="56" class="autotags" />'
	));
	$t->parse('MAIN.TAGS');
}

?>