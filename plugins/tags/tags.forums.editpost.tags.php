<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=tags
Part=forums.editpost.tags
File=tags.forums.editpost.tags
Hooks=forums.editpost.tags
Tags=forums.editpost.tpl:{FORUMS_EDITPOST_FORM_TAGS},{FORUMS_EDITPOST_TOP_TAGS},{FORUMS_EDITPOST_TOP_TAGS_HINT}
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

if($cfg['plugin']['tags']['forums'] && sed_auth('plug', 'tags', 'W') && $is_first_post)
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
	$tags = sed_tag_list($q, 'forums');
	$tags = implode(', ', $tags);
	$t->assign(array(
	'FORUMS_EDITPOST_TOP_TAGS' => $L['Tags'],
	'FORUMS_EDITPOST_TOP_TAGS_HINT' => $L['tags_comma_separated'],
	'FORUMS_EDITPOST_FORM_TAGS' => $autocomplete.'<input type="text" name="rtags" size="56" class="autotags" value="' . $tags . '" />'
	));
	$t->parse('MAIN.FORUMS_EDITPOST_TAGS');
}

?>