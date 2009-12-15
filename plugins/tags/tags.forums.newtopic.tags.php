<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.newtopic.tags
File=tags.forums.newtopic.tags
Hooks=forums.newtopic.tags
Tags=forums.newtopic.tpl:{FORUMS_NEWTOPIC_TOP_TAGS},{FORUMS_NEWTOPIC_TOP_TAGS_HINT},{FORUMS_NEWTOPIC_FORM_TAGS}
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

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W'))
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
	'FORUMS_NEWTOPIC_TOP_TAGS' => $L['Tags'],
	'FORUMS_NEWTOPIC_TOP_TAGS_HINT' => $L['tags_comma_separated'],
	'FORUMS_NEWTOPIC_FORM_TAGS' => $autocomplete.'<input type="text" name="rtags"  size="56" class="tags" />'
	));
	$t->parse('MAIN.FORUMS_NEWTOPIC_TAGS');
}

?>