<?php
/**
 * Plugin install script
 *
 * @package Seditio-N
 * @version 0.0.1
 * @author Trustmaster
 * @copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

// Installing new bbcodes
sed_bbcode_remove(0, 'markitup');
sed_bbcode_add('h1', 'str', '[h1]', '<h1>', true, 128, 'markitup');
sed_bbcode_add('h1', 'str', '[/h1]', '</h1>', true, 128, 'markitup');
sed_bbcode_add('h2', 'str', '[h2]', '<h2>', true, 128, 'markitup');
sed_bbcode_add('h2', 'str', '[/h2]', '</h2>', true, 128, 'markitup');
sed_bbcode_add('h3', 'str', '[h3]', '<h3>', true, 128, 'markitup');
sed_bbcode_add('h3', 'str', '[/h3]', '</h3>', true, 128, 'markitup');
sed_bbcode_add('h4', 'str', '[h4]', '<h4>', true, 128, 'markitup');
sed_bbcode_add('h4', 'str', '[/h4]', '</h4>', true, 128, 'markitup');
sed_bbcode_add('h5', 'str', '[h5]', '<h5>', true, 128, 'markitup');
sed_bbcode_add('h5', 'str', '[/h5]', '</h5>', true, 128, 'markitup');
sed_bbcode_add('h6', 'str', '[h6]', '<h6>', true, 128, 'markitup');
sed_bbcode_add('h6', 'str', '[/h6]', '</h6>', true, 128, 'markitup');
sed_bbcode_add('size', 'pcre', '\[size=([1-2][0-9])\](.+?)\[/size\]', '<span style="font-size:$1pt">$2</span>', true, 128, 'markitup');
sed_bbcode_add('list', 'pcre', '\[list\](.+?)\[/list\]', '<ul>$1</ul>', true, 128, 'markitup');
sed_bbcode_add('list', 'pcre', '\[list=(\w)\](.+?)\[/list\]', '<ol type="$1">$2</ol>', true, 128, 'markitup');
sed_bbcode_add('li', 'str', '[li]', '<li>', true, 128, 'markitup');
sed_bbcode_add('li', 'str', '[/li]', '</li>', true, 128, 'markitup');
sed_bbcode_add('table', 'str', '[table]', '<table>', true, 128, 'markitup');
sed_bbcode_add('table', 'str', '[/table]', '</table>', true, 128, 'markitup');
sed_bbcode_add('tr', 'str', '[tr]', '<tr>', true, 128, 'markitup');
sed_bbcode_add('tr', 'str', '[/tr]', '</tr>', true, 128, 'markitup');
sed_bbcode_add('th', 'str', '[th]', '<th>', true, 128, 'markitup');
sed_bbcode_add('th', 'str', '[/th]', '</th>', true, 128, 'markitup');
sed_bbcode_add('td', 'str', '[td]', '<td>', true, 128, 'markitup');
sed_bbcode_add('td', 'str', '[/td]', '</td>', true, 128, 'markitup');
sed_bbcode_add('hide', 'callback', '\[hide\](.+?)\[/hide\]', 'return $usr["id"] > 0 ? $input[1] : "<div class=\"hidden\">".$L["Hidden"]."</div>";', true, 150, 'markitup', true);
sed_bbcode_add('spoiler', 'pcre', '\[spoiler=([^\]]+)\](.+?)\[/spoiler\]', '<div style="margin:4px 0px 4px 0px"><input type="button" value="$1" onclick="if(this.parentNode.getElementsByTagName(\'div\')[0].style.display != \'\') { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'\'; } else { this.parentNode.getElementsByTagName(\'div\')[0].style.display = \'none\'; }" /><div style="display:none" class="spoiler">$2</div></div>', true, 130, 'markitup');
?>