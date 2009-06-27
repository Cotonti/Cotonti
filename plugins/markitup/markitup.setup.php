<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=markitup
Name=MarkItUp!
Description=jQuery BBcode editor
Version=0.0.6
Date=2009-jan-03
Author=Jay Salvat
Copyright=
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345A
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
skin=01:string::markitup:Skin of editor (plugins/markitup/skins/xxxxx)
autorefresh=02:radio::0:Enable preview auto-refresh
chili=03:radio::0:Enable Chili tags
[END_SED_EXTPLUGIN_CONFIG]
==================== */

/**
 * jQuery BBcode editor
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

if($action == 'install')
{
	// Installing new bbcodes
	sed_bbcode_remove(0, 'markitup');
	
	sed_bbcode_add('size', 'pcre', '\[size=([1-2][0-9])\](.+?)\[/size\]', '<span style="font-size:$1pt">$2</span>', true, 128, 'markitup');
	sed_bbcode_add('table', 'str', '[table]', '<table>', true, 128, 'markitup');
	sed_bbcode_add('table', 'str', '[/table]', '</table>', true, 128, 'markitup');
	sed_bbcode_add('tr', 'str', '[tr]', '<tr>', true, 128, 'markitup');
	sed_bbcode_add('tr', 'str', '[/tr]', '</tr>', true, 128, 'markitup');
	sed_bbcode_add('th', 'str', '[th]', '<th>', true, 128, 'markitup');
	sed_bbcode_add('th', 'str', '[/th]', '</th>', true, 128, 'markitup');
	sed_bbcode_add('td', 'str', '[td]', '<td>', true, 128, 'markitup');
	sed_bbcode_add('td', 'str', '[/td]', '</td>', true, 128, 'markitup');
	sed_bbcode_add('hide', 'callback', '\[hide\](.+?)\[/hide\]', 'return $usr["id"] > 0 ? $input[1] : "<div class=\"hidden\">".$L["Hidden"]."</div>";', true, 150, 'markitup', true);
	sed_bbcode_add('spoiler', 'pcre', '\[spoiler\](.+?)\[/spoiler\]', '<div style="margin:4px 0px 4px 0px"><input type="button" value="'.$L['Show'].'" onclick="if(this.parentNode.getElementsByTagName(\'div\')[0].style.display != \'\') { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'\'; } else { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'none\'; }" /><div style="display:none" class="spoiler">$1</div></div>', true, 130, 'markitup');
	sed_bbcode_add('spoiler', 'pcre', '\[spoiler=([^\]]+)\](.+?)\[/spoiler\]', '<div style="margin:4px 0px 4px 0px"><input type="button" value="$1" onclick="if(this.parentNode.getElementsByTagName(\'div\')[0].style.display != \'\') { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'\'; } else { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'none\'; }" /><div style="display:none" class="spoiler">$2</div></div>', true, 130, 'markitup');

	sed_cache_clearhtml();
}
elseif($action == 'uninstall')
{
	// Remove plugin bbcodes
	sed_bbcode_remove(0, 'markitup');
}

?>