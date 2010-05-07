<?php
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=seditio
Name=Seditio Compatibility
Description=Seditio Compatibility Plugin
Version=0.7.0
Date=2010-jan-03
Author=Trustmaster, Cotonti Team
Copyright=Partial copyright (c) Cotonti Team 2009-2010
Notes=BSD License
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=RW
Lock_members=12345A
[END_SED_EXTPLUGIN]
==================== */

/**
 * Seditio Compatibility: setup
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Trustmaster, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL.');

/**
 * Detects the existence of BBCode in db
 *
 * @global $db_bbcode
 * @param string $name Name of BBCode
 * @return bool
 */
function sedc_detect_bbcode($name)
{
	global $db_bbcode;

	return sed_sql_numrows(sed_sql_query("SELECT bbc_name FROM $db_bbcode WHERE bbc_name = '$name' LIMIT 1")) > 0;
}

if ($action == 'install')
{
	// Installing new bbcodes
	sed_bbcode_remove(0, 'seditio');
	sed_bbcode_add('hr', 'str', '[hr]', '<hr />', false, 128, 'seditio');
	sed_bbcode_add('br', 'str', '[br]', '<br />', false, 128, 'seditio');
	sed_bbcode_add('nbspnbsp', 'str', '[__]', '&nbsp; &nbsp;', false, 128, 'seditio');
	sed_bbcode_add('red', 'str', '[red]', '<span style="color:#F93737">', true, 128, 'seditio');
	sed_bbcode_add('red', 'str', '[/red]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('white', 'str', '[white]', '<span style="color:#FFFFFF">', true, 128, 'seditio');
	sed_bbcode_add('white', 'str', '[/white]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('green', 'str', '[green]', '<span style="color:#09DD09">', true, 128, 'seditio');
	sed_bbcode_add('green', 'str', '[/green]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('blue', 'str', '[blue]', '<span style="color:#018BFF">', true, 128, 'seditio');
	sed_bbcode_add('blue', 'str', '[/blue]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('orange', 'str', '[orange]', '<span style="color:#FF9900">', true, 128, 'seditio');
	sed_bbcode_add('orange', 'str', '[/orange]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('yellow', 'str', '[yellow]', '<span style="color:#FFFF00">', true, 128, 'seditio');
	sed_bbcode_add('yellow', 'str', '[/yellow]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('purple', 'str', '[purple]', '<span style="color:#A22ADA">', true, 128, 'seditio');
	sed_bbcode_add('purple', 'str', '[/purple]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('black', 'str', '[black]', '<span style="color:#000000">', true, 128, 'seditio');
	sed_bbcode_add('black', 'str', '[/black]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('grey', 'str', '[grey]', '<span style="color:#B9B9B9">', true, 128, 'seditio');
	sed_bbcode_add('grey', 'str', '[/grey]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('pink', 'str', '[pink]', '<span style="color:#FFC0FF">', true, 128, 'seditio');
	sed_bbcode_add('pink', 'str', '[/pink]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('sky', 'str', '[sky]', '<span style="color:#D1F4F9">', true, 128, 'seditio');
	sed_bbcode_add('sky', 'str', '[/sky]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('sea', 'str', '[sea]', '<span style="color:#171A97">', true, 128, 'seditio');
	sed_bbcode_add('sea', 'str', '[/sea]', '</span>', true, 128, 'seditio');
	sed_bbcode_add('del', 'str', '[del]', '<del>', true, 128, 'seditio');
	sed_bbcode_add('del', 'str', '[/del]', '</del>', true, 128, 'seditio');
	sed_bbcode_add('colleft', 'str', '[colleft]', '<div class="colleft">', true, 128, 'seditio');
	sed_bbcode_add('colleft', 'str', '[/colleft]', '</div>', true, 128, 'seditio');
	sed_bbcode_add('colright', 'str', '[colright]', '<div class="colright">', true, 128, 'seditio');
	sed_bbcode_add('colright', 'str', '[/colright]', '</div>', true, 128, 'seditio');

	sed_bbcode_add('thumb', 'pcre', '\[thumb=((?:http://|https://|ftp://)?[^\]"\';:\?]+\.(?:jpg|jpeg|gif|png))\]([^\]"\';:\?]+\.(?:jpg|jpeg|gif|png))\[/thumb\]','<a href="pfs.php?m=view&amp;v=$2"><img src="$1" alt="" /></a>', true, 128, 'seditio');
	sed_bbcode_add('t', 'pcre', '\[t=((?:http://|https://|ftp://)?[^"\';:\?]+\.(?:jpg|jpeg|gif|png))\]((?:http://|https://|ftp://)?[^"\';:\?]+\.(?:jpg|jpeg|gif|png))\[/t\]','<a href="$2"><img src="$1" alt="" /></a>', true, 128, 'seditio');
	sed_bbcode_add('pfs', 'pcre', '\[pfs\]([^\s"\'&;\?\(\[]+)\[/pfs\]', '<a href="'.$cfg['pfs_dir'].'$1">' . $R['admin_icon_pfs'] . ' $1</a>', true, 128, 'seditio');
	sed_bbcode_add('style', 'pcre', '\[style=([1-9])\](.+?)\[/style\]', '<span class="bbstyle$1">$2</span>', true, 128, 'seditio');
	sed_bbcode_add('user', 'pcre', '\[user=(\d+)\](.+?)\[/user\]', '<a href="users.php?m=details&id=$1">$2</a>', true, 128, 'seditio');
	sed_bbcode_add('page', 'pcre', '\[page=(\d+)\](.+?)\[/page\]', '<a href="page.php?id=$1">$2</a>', true, 128, 'seditio');
	sed_bbcode_add('page', 'pcre', '\[page\](\d+)\[/page\]', '<a href="page.php?id=$1">'.$L['Page'].' #$1</a>', true, 128, 'seditio');
	sed_bbcode_add('group', 'pcre', '\[group=(\d+)\](.+?)\[/group\]', '<a href="users.php?g=$1">$2</a>', true, 128, 'seditio');
	sed_bbcode_add('topic', 'pcre', '\[topic\](\d+)\[/topic\]', '<a href="forums.php?m=posts&q=$1">'.$L['Topic'].' #$1</a>', true, 128, 'seditio');
	sed_bbcode_add('post', 'pcre', '\[post\](\d+)\[/post\]', '<a href="forums.php?m=posts&p=$1#$1">'.$L['Post'].' #$1</a>', true, 128, 'seditio');
	sed_bbcode_add('pm', 'pcre', '\[pm\](\d+)\[/pm\]', '<a href="pm.php?m=send&to=$1">' . $R['pm_icon'] . '</a>', true, 128, 'seditio');
	sed_bbcode_add('flag', 'pcre', '\[f\]([a-z][a-z])\[/f\]', '<a href="users.php?f=country_$1"><img src="images/flags/f-$1.gif" alt="" /></a>', true, 128, 'seditio');
	sed_bbcode_add('ac', 'pcre', '\[ac=([^\[]+)\](.+?)\[/ac\]', '<acronym title="$1">$2</acronym>', true, 128, 'seditio');
	sed_bbcode_add('c1c2c3', 'pcre', '\[c1\:([\d%]+)\](.*?)\[c2\:([\d%]+)\](.*?)\[c3\]', '<table style="margin:0; vertical-align:top; width:100%;"><tr><td style="padding:8px; vertical-align:top; width:$1%;">$2</td><td  style="padding:8px; vertical-align:top; width:$3%;">$4</td></tr></table>', true, 128, 'seditio');

	if (!sedc_detect_bbcode('youtube'))
	{
		sed_bbcode_add('youtube', 'pcre', '\[youtube=([^\s"\';&\?\(\[]+)\]', '<object type="application/x-shockwave-flash" data="http://www.youtube.com/v/$1" width="425" height="344"><param name="movie" value="http://www.youtube.com/v/$1" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /></object>', false, 128, 'seditio');
	}
	if (!sedc_detect_bbcode('googlevideo'))
	{
		sed_bbcode_add('googlevideo', 'pcre', '\[googlevideo=([^\s"\';&\?\(\[]+)\]', '<object type="application/x-shockwave-flash" data="http://video.google.com/googleplayer.swf?docid=$1&amp;hl=en&amp;fs=true" width="400" height="326"><param name="movie" value="http://video.google.com/googleplayer.swf?docid=$1&amp;hl=en&amp;fs=true" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /></object>', false, 128, 'seditio');
	}
	if (!sedc_detect_bbcode('metacafe'))
	{
		sed_bbcode_add('metacafe', 'pcre', '\[metacafe=([^\s"\';&\?\(\[]+)\]', '<object type="application/x-shockwave-flash" data="http://www.metacafe.com/fplayer/$1" width="400" height="345"><param name="movie" value="http://www.metacafe.com/fplayer/$1" /><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /></object>', false, 128, 'seditio');
	}
}
elseif ($action == 'uninstall')
{
	// Remove plugin bbcodes
	sed_bbcode_remove(0, 'seditio');
}

?>