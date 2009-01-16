<?PHP
/* ====================
[BEGIN_SED]
File=plugins/chili/chili.setup.php
Version=0.0.1
Updated=2008-aug-30
Type=Plugin
Author=Trustmaster
Description=Cotonti - Website engine http://www.cotonti.com Copyright (c) Cotonti Team 2009 BSD License
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=chili
Name=Chili Highlighter
Description=jQuery code highlighter
Version=2.2/0.0.1
Date=2008-aug-30
Author=Andrea Ercolino
Copyright=
Notes=
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345A
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

if($action == 'install')
{
	// Installing new bbcodes
	sed_bbcode_remove(0, 'chili');
	sed_bbcode_add('highlight', 'callback', '\[highlight=([\w\-]+)\](.*?)\[/highlight\]', 'return \'<div class="highlight"><pre class="\'.$input[1].\'">\'.sed_bbcode_cdata($input[2]).\'</pre></div>\';', true, 3, 'chili');
}
elseif($action == 'uninstall')
{
	// Remove plugin bbcodes
	sed_bbcode_remove(0, 'chili');
}

?>