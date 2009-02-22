<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=chili
Name=Chili Highlighter
Description=jQuery code highlighter
Version=2.2/0.0.1
Date=2009-jan-03
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

/**
 * jQuery code highlighter
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Trustmaster, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2009
 * @license BSD
 */

if(!defined('SED_CODE')){die('Wrong URL.');}

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