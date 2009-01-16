<?PHP
/* ====================
[BEGIN_SED]
File=plugins/seditio/seditio.setup.php
Version=0.0.2
Updated=2009-jan-02
Type=Plugin
Author=Trustmaster
Description=Cotonti - Website engine http://www.cotonti.com Copyright (c) Cotonti Team 2009 BSD License
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=seditio
Name=Seditio Compatibility
Description=Seditio Compatibility Plugin
Version=0.0.2
Date=2008-jan-02
Author=Trustmaster
Copyright=Partial copyright (c) 2008 Cotonti Team
Notes=BSD License
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

	sed_bbcode_add('thumb', 'pcre', '\[thumb=((?:http://|https://|ftp://)?[^"\';:\?]+\.(?:jpg|jpeg|gif|png))\]([^"\';:\?]+\.(?:jpg|jpeg|gif|png))\[/thumb\]','<a href="pfs.php?m=view&amp;v=$2"><img src="$1" alt="" /></a>', true, 128, 'seditio');
	sed_bbcode_add('t', 'pcre', '\[t=((?:http://|https://|ftp://)?[^"\';:\?]+\.(?:jpg|jpeg|gif|png))\]((?:http://|https://|ftp://)?[^"\';:\?]+\.(?:jpg|jpeg|gif|png))\[/t\]','<a href="$2"><img src="$1" alt="" /></a>', true, 128, 'seditio');
	sed_bbcode_add('pfs', 'pcre', '\[pfs\]([^\s"\'&;\?\(\[]+)\[/pfs\]', '<a href="'.$cfg['pfs_dir'].'$1"><img src="images/admin/pfs.gif" alt="" /> $1</a>', true, 128, 'seditio');
	sed_bbcode_add('style', 'pcre', '\[style=([1-9])\](.+?)\[/style\]', '<span class="bbstyle$1">$2</span>', true, 128, 'seditio');
	sed_bbcode_add('user', 'pcre', '\[user=(\d+)\](.+?)\[/user\]', '<a href="users.php?m=details&id=$1">$2</a>', true, 128, 'seditio');
	sed_bbcode_add('page', 'pcre', '\[page=(\d+)\](.+?)\[/page\]', '<a href="page.php?id=$1">$2</a>', true, 128, 'seditio');
	sed_bbcode_add('page', 'pcre', '\[page\](\d+)\[/page\]', '<a href="page.php?id=$1">'.$L['Page'].' #$1</a>', true, 128, 'seditio');
	sed_bbcode_add('group', 'pcre', '\[group=(\d+)\](.+?)\[/group\]', '<a href="users.php?g=$1">$2</a>', true, 128, 'seditio');
	sed_bbcode_add('topic', 'pcre', '\[topic\](\d+)\[/topic\]', '<a href="forums.php?m=posts&q=$1">'.$L['Topic'].' #$1</a>', true, 128, 'seditio');
	sed_bbcode_add('post', 'pcre', '\[post\](\d+)\[/post\]', '<a href="forums.php?m=posts&p=$1#$1">'.$L['Post'].' #$1</a>', true, 128, 'seditio');
	sed_bbcode_add('pm', 'pcre', '\[pm\](\d+)\[/pm\]', '<a href="pm.php?m=send&to=$1"><img src="skins/'.$skin.'/img/system/icon-pm.png" alt=""></a>', true, 128, 'seditio');
	sed_bbcode_add('flag', 'pcre', '\[f\]([a-z][a-z])\[/f\]', '<a href="users.php?f=country_$1"><img src="images/flags/f-$1.gif" alt="" /></a>', true, 128, 'seditio');
	sed_bbcode_add('ac', 'pcre', '\[ac=([^\[]+)\](.+?)\[/ac\]', '<acronym title="$1">$2</acronym>', true, 128, 'seditio');
	sed_bbcode_add('c1c2c3', 'pcre', '\[c1\:([\d%]+)\](.*?)\[c2\:([\d%]+)\](.*?)\[c3\]', '<table style="margin:0; vertical-align:top; width:100%;"><tr><td style="padding:8px; vertical-align:top; width:$1%;">$2</td><td  style="padding:8px; vertical-align:top; width:$3%;">$4</td></tr></table>', true, 128, 'seditio');

	if($cfg['parser_vid'])
	{
		sed_bbcode_add('youtube', 'pcre', '\[youtube=([^\s"\';&\?\(\[]+)\]', '<object width="425" height="350">
<param name="movie" value="http://www.youtube.com/v/$1"></param>
<embed src="http://www.youtube.com/v/$1" type="application/x-shockwave-flash" width="425" height="350"></embed>
</object>', true, 128, 'seditio');
		sed_bbcode_add('googlevideo', 'pcre', '\[googlevideo=([^\s"\';&\?\(\[]+)\]', '<embed style="width:425px; height:326px;" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=$1&hl=en-GB"> </embed>', true, 128, 'seditio');
		sed_bbcode_add('metacafe', 'pcre', '\[metacafe=([^\s"\';&\?\(\[]+)\]', '<embed style="width:425px; height:345px;" src="http://www.metacafe.com/fplayer/$1" width="400" height="345" wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>', true, 128, 'seditio');
		sed_bbcode_add('flash', 'pcre', '\[flash\]([^\s"\';&\?\(\[]+\.swf)\[/flash\]', '<embed src="$1" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>', true, 128, 'seditio');
		sed_bbcode_add('flash', 'pcre', '\[flash w=(\d+) h=(\d+)\]([^\s"\';&\?\(\[]+\.swf)\[/flash\]', '<embed src="$3" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="$1" height="$2"></embed>', true, 128, 'seditio');
		sed_bbcode_add('divx', 'pcre', '\[divx\]([^\s"\';&\?\(\[]+\.divx)\[/divx\]', '<embed type="video/divx" src="$1" pluginspage="http://go.divx.com/plugin/download/" showpostplaybackad="false" custommode="Stage6"  object width="450" height="400"></embed>', true, 128, 'seditio');
		sed_bbcode_add('divx', 'pcre', '\[divx w=(\d+) h=(\d+)\]([^\s"\';&\?\(\[]+\.divx)\[/divx\]', '<embed type="video/divx" src="$3" pluginspage="http://go.divx.com/plugin/download/" showpostplaybackad="false" custommode="Stage6"  object width="$1" height="$2"></embed>', true, 128, 'seditio');
	}
}
elseif($action == 'uninstall')
{
	// Remove plugin bbcodes
	sed_bbcode_remove(0, 'seditio');
}

?>