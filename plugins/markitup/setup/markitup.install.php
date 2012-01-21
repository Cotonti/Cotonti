<?php
/**
 * markItUp! install handler
 *
 * @package markitup
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Installing new bbcodes
if (cot_plugin_active('bbcode'))
{
	require_once cot_incfile('bbcode', 'plug');
	
	cot_bbcode_remove(0, 'markitup');

	cot_bbcode_add('size', 'pcre', '\[size=([1-2][0-9])\](.+?)\[/size\]', '<span style="font-size:$1pt">$2</span>', true, 128, 'markitup');
	cot_bbcode_add('table', 'str', '[table]', '<table>', true, 128, 'markitup');
	cot_bbcode_add('table', 'str', '[/table]', '</table>', true, 128, 'markitup');
	cot_bbcode_add('tr', 'str', '[tr]', '<tr>', true, 128, 'markitup');
	cot_bbcode_add('tr', 'str', '[/tr]', '</tr>', true, 128, 'markitup');
	cot_bbcode_add('th', 'str', '[th]', '<th>', true, 128, 'markitup');
	cot_bbcode_add('th', 'str', '[/th]', '</th>', true, 128, 'markitup');
	cot_bbcode_add('td', 'str', '[td]', '<td>', true, 128, 'markitup');
	cot_bbcode_add('td', 'str', '[/td]', '</td>', true, 128, 'markitup');
	cot_bbcode_add('hide', 'callback', '\[hide\](.+?)\[/hide\]', 'return $usr["id"] > 0 ? $input[1] : "<div class=\"hidden\">".$L["Hidden"]."</div>";', true, 150, 'markitup', true);
	cot_bbcode_add('spoiler', 'pcre', '\[spoiler\](.+?)\[/spoiler\]', '<div style="margin:4px 0px 4px 0px"><input type="button" value="'.$L['Show'].'" onclick="if(this.parentNode.getElementsByTagName(\'div\')[0].style.display != \'\') { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'\'; } else { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'none\'; }" /><div style="display:none" class="spoiler">$1</div></div>', true, 130, 'markitup');
	cot_bbcode_add('spoiler', 'pcre', '\[spoiler=([^\]]+)\](.+?)\[/spoiler\]', '<div style="margin:4px 0px 4px 0px"><input type="button" value="$1" onclick="if(this.parentNode.getElementsByTagName(\'div\')[0].style.display != \'\') { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'\'; } else { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'none\'; }" /><div style="display:none" class="spoiler">$2</div></div>', true, 130, 'markitup');

	cot_bbcode_clearcache();
}
?>
