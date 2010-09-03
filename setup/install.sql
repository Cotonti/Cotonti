CREATE TABLE `sed_auth` (
  `auth_id` int NOT NULL auto_increment,
  `auth_groupid` int NOT NULL default '0',
  `auth_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `auth_option` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `auth_rights` tinyint unsigned NOT NULL default '0',
  `auth_rights_lock` tinyint unsigned NOT NULL default '0',
  `auth_setbyuserid` int unsigned NOT NULL default '0',
  PRIMARY KEY  (`auth_id`),
  KEY `auth_groupid` (`auth_groupid`),
  KEY `auth_code` (`auth_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sed_auth` (`auth_groupid`, `auth_code`, `auth_option`, `auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES
(1, 'admin', 'a', 0, 255, 1),
(2, 'admin', 'a', 0, 255, 1),
(3, 'admin', 'a', 0, 255, 1),
(4, 'admin', 'a', 0, 255, 1),
(5, 'admin', 'a', 255, 255, 1),
(6, 'admin', 'a', 1, 0, 1),
(1, 'message', 'a', 1, 255, 1),
(2, 'message', 'a', 1, 255, 1),
(3, 'message', 'a', 1, 255, 1),
(4, 'message', 'a', 1, 255, 1),
(5, 'message', 'a', 255, 255, 1),
(6, 'message', 'a', 131, 0, 1),
(1, 'ratings', 'a', 1, 254, 1),
(2, 'ratings', 'a', 1, 254, 1),
(3, 'ratings', 'a', 0, 255, 1),
(4, 'ratings', 'a', 3, 128, 1),
(5, 'ratings', 'a', 255, 255, 1),
(6, 'ratings', 'a', 131, 0, 1),
(1, 'users', 'a', 0, 254, 1),
(2, 'users', 'a', 0, 254, 1),
(3, 'users', 'a', 0, 255, 1),
(4, 'users', 'a', 3, 128, 1),
(5, 'users', 'a', 255, 255, 1),
(6, 'users', 'a', 3, 0, 1),
(1, 'structure', 'a', 0, 255, 1),
(2, 'structure', 'a', 0, 255, 1),
(3, 'structure', 'a', 0, 255, 1),
(4, 'structure', 'a', 0, 255, 1),
(5, 'structure', 'a', 255, 255, 1),
(6, 'structure', 'a', 1, 0, 1);

CREATE TABLE `sed_banlist` (
  `banlist_id` int NOT NULL auto_increment,
  `banlist_ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `banlist_email` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `banlist_reason` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `banlist_expire` int default '0',
  PRIMARY KEY  (`banlist_id`),
  KEY `banlist_ip` (`banlist_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_bbcode` (
  `bbc_id` int NOT NULL auto_increment,
  `bbc_name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `bbc_mode` enum('str','ereg','pcre','callback') collate utf8_unicode_ci NOT NULL default 'str',
  `bbc_pattern` varchar(255) collate utf8_unicode_ci NOT NULL,
  `bbc_replacement` text collate utf8_unicode_ci NOT NULL,
  `bbc_container` tinyint NOT NULL default '1',
  `bbc_enabled` tinyint NOT NULL default '1',
  `bbc_priority` tinyint unsigned NOT NULL default '128',
  `bbc_plug` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `bbc_postrender` tinyint NOT NULL default '0',
  PRIMARY KEY  (`bbc_id`),
  KEY `bbc_enabled` (`bbc_enabled`),
  KEY `bbc_priority` (`bbc_priority`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sed_bbcode` (`bbc_name`, `bbc_mode`, `bbc_pattern`, `bbc_replacement`, `bbc_container`, `bbc_enabled`, `bbc_priority`, `bbc_plug`, `bbc_postrender`) VALUES
('b', 'str', '[b]', '<strong>', 1, 1, 128, '', 0),
('b', 'str', '[/b]', '</strong>', 0, 1, 128, '', 0),
('i', 'str', '[i]', '<em>', 1, 1, 128, '', 0),
('i', 'str', '[/i]', '</em>', 1, 1, 128, '', 0),
('u', 'str', '[u]', '<span style="text-decoration:underline">', 1, 1, 128, '', 0),
('u', 'str', '[/u]', '</span>', 1, 1, 128, '', 0),
('s', 'str', '[s]', '<span style="text-decoration:line-through">', 1, 1, 128, '', 0),
('s', 'str', '[/s]', '</span>', 1, 1, 128, '', 0),
('center', 'str', '[center]', '<div style="text-align:center">', 1, 1, 128, '', 0),
('center', 'str', '[/center]', '</div>', 1, 1, 128, '', 0),
('left', 'str', '[left]', '<div style="text-align:left">', 1, 1, 128, '', 0),
('left', 'str', '[/left]', '</div>', 1, 1, 128, '', 0),
('right', 'str', '[right]', '<div style="text-align:right">', 1, 1, 128, '', 0),
('right', 'str', '[/right]', '</div>', 1, 1, 128, '', 0),
('justify', 'str', '[justify]', '<div style="text-align:justify">', 1, 1, 128, '', 0),
('justify', 'str', '[/justify]', '</div>', 1, 1, 128, '', 0),
('pre', 'str', '[pre]', '<pre>', 1, 1, 128, '', 0),
('pre', 'str', '[/pre]', '</pre>', 0, 1, 128, '', 0),
('nbsp', 'str', '[_]', '&nbsp;', 0, 1, 128, '', 0),
('email', 'callback', '\\[email=(\\w[\\._\\w\\-]+@[\\w\\.\\-]+\\.[a-z]+)\\](.+?)\\[/email\\]', 'return sed_obfuscate(''<a href="mailto:''.$input[1].''">''.$input[2].''</a>'');', 1, 1, 128, '', 0),
('quote', 'pcre', '\\[quote=(.+?)\\](.+?)\\[/quote\\]', '<blockquote><strong>$1:</strong><hr />$2</blockquote>', 1, 1, 128, '', 0),
('quote', 'pcre', '\\[quote\\](.+?)\\[/quote\\]', '<blockquote>$1</blockquote>', 1, 1, 128, '', 0),
('color', 'pcre', '\\[color=(#?\\w+)\\](.+?)\\[/color\\]', '<span style="color:$1">$2</span>', 1, 1, 128, '', 0),
('img', 'pcre', '\\[img\\]((?:http://|https://|ftp://)?[^"\\'';:\\?\\[]+\\.(?:jpg|jpeg|gif|png))\\[/img\\]', '<img src="$1" alt="" />', 1, 1, 128, '', 0),
('img', 'pcre', '\\[img=((?:http://|https://|ftp://)?[^\\]"\\'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\]((?:http://|https://|ftp://)?[^\\]"\\'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\[/img\\]', '<a href="$1"><img src="$2" alt="" /></a>', 1, 1, 128, '', 0),
('url', 'pcre', '\\[url=((?:http://|https://|ftp://)?[^\\s"\\'':\\[]+)\\](.+?)\\[/url\\]', '<a href="$1">$2</a>', 1, 1, 128, '', 0),
('url', 'pcre', '\\[url\\]((?:http://|https://|ftp://)?[^\\s"\\'':]+)\\[/url\\]', '<a href="$1">$1</a>', 1, 1, 128, '', 0),
('code', 'callback', '\\[code\\](.+?)\\[/code\\]', 'return ''<pre class="code">''.sed_bbcode_cdata($input[1]).''</pre>'';', 1, 1, 1, '', 0),
('more', 'str', '[more]', '<!--more-->', 1, 1, 128, '', 0),
('more', 'str', '[/more]', '&nbsp;', 1, 1, 128, '', 0),
('size', 'pcre', '\\[size=([1-2][0-9])\\](.+?)\\[/size\\]', '<span style="font-size:$1pt">$2</span>', 1, 1, 128, 'markitup', 0),
('h','pcre','\\[h([1-6])\\](.+?)\\[/h\\1\\]','<h$1>$2</h$1>',1,1,128,'',0),
('list','str','[list]','<ul>',1,1,128,'',0),
('list','str','[/list]','</ul>',1,1,128,'',0),
('ol','str','[ol]','<ol>',1,1,128,'',0),
('ol','str','[/ol]','</ol>',1,1,128,'',0),
('li','str','[li]','<li>',1,1,128,'',0),
('li','str','[/li]','</li>',1,1,128,'',0),
('li_short','pcre','\\[\\*\\](.*?)\\n','<li>$1</li>',0,1,128,'',0),
('table', 'str', '[table]', '<table>', 1, 1, 128, 'markitup', 0),
('table', 'str', '[/table]', '</table>', 1, 1, 128, 'markitup', 0),
('tr', 'str', '[tr]', '<tr>', 1, 1, 128, 'markitup', 0),
('tr', 'str', '[/tr]', '</tr>', 1, 1, 128, 'markitup', 0),
('th', 'str', '[th]', '<th>', 1, 1, 128, 'markitup', 0),
('th', 'str', '[/th]', '</th>', 1, 1, 128, 'markitup', 0),
('td', 'str', '[td]', '<td>', 1, 1, 128, 'markitup', 0),
('td', 'str', '[/td]', '</td>', 1, 1, 128, 'markitup', 0),
('hide', 'callback', '\\[hide\\](.+?)\\[/hide\\]', 'return $usr["id"] > 0 ? $input[1] : "<div class=\\"hidden\\">".$L["Hidden"]."</div>";', 1, 1, 150, 'markitup', 1),
('spoiler','pcre','\\[spoiler\\](.+?)\\[/spoiler\\]','<div style=\"margin:4px 0px 4px 0px\"><input type=\"button\" value=\"Show\" onclick=\"if(this.parentNode.getElementsByTagName(''div'')[0].style.display != '''') { this.parentNode.getElementsByTagName(''div'')[0].style.display = ''''; } else { this.parentNode.getElementsByTagName(''div'')[0].style.display = ''none''; }\" /><div style=\"display:none\" class=\"spoiler\">$1</div></div>',1,1,130,'markitup',0),
('spoiler', 'pcre', '\\[spoiler=([^\\]]+)\\](.+?)\\[/spoiler\\]', '<div style="margin:4px 0px 4px 0px"><input type="button" value="$1" onclick="if(this.parentNode.getElementsByTagName(''div'')[0].style.display != '''') { this.parentNode.getElementsByTagName(''div'')[0].style.display = ''''; } else { this.parentNode.getElementsByTagName(''div'')[0].style.display = ''none''; }" /><div style="display:none" class="spoiler">$2</div></div>', 1, 1, 130, 'markitup', 0),
('thumb', 'pcre', '\\[thumb=(.*?[^"\\'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\](.*?[^"\\'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\[/thumb\\]', '<a href="datas/users/$2"><img src="$1" alt="$2" /></a>', 1, 1, 128, '', 0),
('thumb', 'pcre', '\\[thumb\\](.*?[^"\\'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\[/thumb\\]', '<a href="datas/users/$1"><img src="datas/thumbs/$1" /></a>', 1, 1, 128, '', 0),
('pfs', 'pcre', '\\[pfs\\](.*?[^"\\'';:\\?]+\\.(?:jpg|jpeg|gif|png|zip|rar|7z|pdf|txt))\\[/pfs\\]', '<strong><a href="datas/users/$1">$1</a></strong>', 1, 1, 128, '', 0);

CREATE TABLE `sed_cache` (
  `c_name` varchar(120) collate utf8_unicode_ci NOT NULL,
  `c_realm` varchar(80) collate utf8_unicode_ci NOT NULL default 'cot',
  `c_expire` int NOT NULL default '0',
  `c_auto` tinyint NOT NULL default '1',
  `c_value` text collate utf8_unicode_ci,
  PRIMARY KEY  (`c_name`, `c_realm`),
  KEY (`c_realm`),
  KEY (`c_name`),
  KEY (`c_expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_cache_bindings` (
  `c_event` VARCHAR(80) collate utf8_unicode_ci NOT NULL,
  `c_id` VARCHAR(120) collate utf8_unicode_ci NOT NULL,
  `c_realm` VARCHAR(80) collate utf8_unicode_ci NOT NULL DEFAULT 'cot',
  `c_type` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_event`, `c_id`, `c_realm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_config` (
  `config_owner` varchar(24) collate utf8_unicode_ci NOT NULL default 'core',
  `config_cat` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `config_order` char(2) collate utf8_unicode_ci NOT NULL default '00',
  `config_name` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `config_type` tinyint NOT NULL default '0',
  `config_value` text collate utf8_unicode_ci NOT NULL,
  `config_default` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `config_variants` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `config_text` varchar(255) collate utf8_unicode_ci NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('core','email','01','email_type',2,'mail(Standard)','mail(Standard)','mail(Standard),smtp',''),
('core','email','02','smtp_address',1,'','','',''),
('core','email','03','smtp_port',1,'25','25','',''),
('core','email','04','smtp_login',1,'','','',''),
('core','email','05','smtp_password',1,'','','',''),
('core','email','06','smtp_uses_ssl',3,'0','0','',''),
('core','lang','10','forcedefaultlang',3,'0','0','',''),
('core','main','02','adminemail',1,'admin@mysite.com','admin@mysite.com','',''),
('core','main','05','clustermode',3,'0','0','',''),
('core','main','03','hostip',1,'999.999.999.999','999.999.999.999','',''),
('core','main','07','maintenance',3,'0','0','',''),
('core','main','08','maintenancereason',1,'','','',''),
('core','main','09','devmode',3,'0','0','',''),
('core','main','10','cookiedomain',1,'','','',''),
('core','main','10','cookiepath',1,'','','',''),
('core','main','10','cookielifetime',2,'5184000','5184000','1800,3600,7200,14400,28800,43200,86400,172800,259200,604800,1296000,2592000,5184000',''),
('core','main','20','shieldenabled',3,'0','0','',''),
('core','main','20','shieldtadjust',2,'100','100','10,25,50,75,100,125,150,200,300,400,600,800',''),
('core','main','20','shieldzhammer',2,'25','25','5,10,15,20,25,30,40,50,100',''),
('core','main','29','redirbkonlogin',3,'1','1','',''),
('core','main','29','redirbkonlogout',3,'0','0','',''),
('core','main','30','jquery',3,'1','1','',''),
('core','main','31','turnajax',3,'1','1','',''),
('core','menus','10','topline',0,'','','',''),
('core','menus','10','banner',0,'','','',''),
('core','menus','10','bottomline',0,'','','',''),
('core','menus','15','menu1',0,'<ul>\n<li><a href=\"index.php\">Home</a></li>\n<li><a href=\"index.php?z=forums\">Forums</a></li>\n<li><a href=\"index.php?z=page&amp;c=articles\">Articles</a></li>\n<li><a href=\"index.php?e=search\">Search</a></li>\n</ul>','<ul>\n<li><a href=\"index.php\">Home</a></li>\n<li><a href=\"index.php?z=forums\">Forums</a></li>\n<li><a href=\"index.php?z=page&c=articles\">Articles</a></li>\n<li><a href=\"index.php?e=search\">Search</a></li>\n</ul>','',''),
('core','menus','15','menu2',0,'','','',''),
('core','menus','15','menu3',0,'','','',''),
('core','menus','15','menu4',0,'','','',''),
('core','menus','15','menu5',0,'','','',''),
('core','menus','15','menu6',0,'','','',''),
('core','menus','15','menu7',0,'','','',''),
('core','menus','15','menu8',0,'','','',''),
('core','menus','15','menu9',0,'','','',''),
('core','menus','20','freetext1',0,'','','',''),
('core','menus','20','freetext2',0,'','','',''),
('core','menus','20','freetext3',0,'','','',''),
('core','menus','20','freetext4',0,'','','',''),
('core','menus','20','freetext5',0,'','','',''),
('core','menus','20','freetext6',0,'','','',''),
('core','menus','20','freetext7',0,'','','',''),
('core','menus','20','freetext8',0,'','','',''),
('core','menus','20','freetext9',0,'','','',''),
('core','parser','10','parser_custom',3,'0','0','',''),
('core','parser','10','parser_cache',3,'1','1','',''),
('core','parser','10','parser_disable',3,'0','0','',''),
('core','parser','20','parsebbcodeusertext',3,'1','1','',''),
('core','parser','20','parsebbcodepm',3,'1','1','',''),
('core','parser','20','parsebbcodeforums',3,'1','1','',''),
('core','parser','20','parsebbcodepages',3,'1','1','',''),
('core','parser','30','parsesmiliesusertext',3,'0','0','',''),
('core','parser','30','parsesmiliespm',3,'1','1','',''),
('core','parser','30','parsesmiliesforums',3,'1','1','',''),
('core','parser','30','parsesmiliespages',3,'0','0','',''),
('core','performance','06','gzip',3,'1','1','',''),
('core','performance','12','disablehitstats',3,'0','0','',''),
('core','performance','13','disableactivitystats',3,'0','0','',''),
('core','performance','14','disabledbstats',3,'0','0','',''),
('core','performance','20','hit_percision',2,'100','10,100,1000','','Optimized hit counter precision'),
('core','performance','31','cache_page',3,'0','0','',''),
('core','performance','32','cache_index',3,'0','0','',''),
('core','performance','33','cache_forums',3,'0','0','',''),
('core','ratings','01','disable_ratings',3,'0','0','',''),
('core','ratings','02','ratings_allowchange',3,'0','0','',''),
('core','skin','02','forcedefaultskin',3,'0','0','',''),
('core','skin','03','homebreadcrumb',3,'0','0','',''),
('core','skin','08','metakeywords',1,'','','',''),
('core','skin','08','separator',1,'/','/','',''),
('core','skin','15','disablesysinfos',3,'0','0','',''),
('core','skin','15','keepcrbottom',3,'1','1','',''),
('core','skin','15','showsqlstats',3,'0','0','',''),
('core','skin','21','msg_separate',3,'0','0','','Show messages separately for each source'),
('core','structure','05','maxlistsperpage',2,'15','15','5,10,15,20,25,30,40,50,60,70,100,200,500',''),
('core','structure','05','maxrowsperpage',2,'15','15','5,10,15,20,25,30,40,50,60,70,100,200,500',''),
('core','time','11','dateformat',1,'Y-m-d H:i','Y-m-d H:i','',''),
('core','time','11','formatmonthday',1,'m-d','m-d','',''),
('core','time','11','formatyearmonthday',1,'Y-m-d','Y-m-d','',''),
('core','time','11','formatmonthdayhourmin',1,'m-d H:i','m-d H:i','',''),
('core','time','11','servertimezone',1,'0','0','',''),
('core','time','12','defaulttimezone',1,'0','0','',''),
('core','time','14','timedout',2,'1200','1200','30,60,120,300,600,900,1200,1800,2400,3600',''),
('core','title','01','maintitle',1,'Title of your site','Title of your site','',''),
('core','title','02','subtitle',1,'Subtitle','Subtitle','',''),
('core','title','03','title_forum_main',1,'{FORUM}','{FORUM}','',''),
('core','title','04','title_forum_topics',1,'{SECTION} - {FORUM}','{SECTION} - {FORUM}','',''),
('core','title','05','title_forum_posts',1,'{TITLE} - {SECTION} - {FORUM}','{TITLE} - {SECTION} - {FORUM}','',''),
('core','title','06','title_forum_newtopic',1,'{SECTION} - {FORUM}','{SECTION} - {FORUM}','',''),
('core','title','07','title_forum_editpost',1,'{SECTION} - {FORUM}','{SECTION} - {FORUM}','',''),
('core','title','08','title_list',1,'{TITLE}','{TITLE}','',''),
('core','title','09','title_page',1,'{TITLE}','{TITLE}','',''),
('core','title','10','title_pfs',1,'{PFS}','{PFS}','',''),
('core','title','11','title_pm_main',1,'{PM}','{PM}','',''),
('core','title','12','title_pm_send',1,'{PM}','{PM}','',''),
('core','title','13','title_users_main',1,'{USERS}','{USERS}','',''),
('core','title','14','title_users_details',1,'{USER} : {NAME}','{USER} - {NAME}','',''),
('core','title','15','title_users_profile',1,'{PROFILE}','{PROFILE}','',''),
('core','title','16','title_users_edit',1,'{NAME}','{NAME}','',''),
('core','title','17','title_users_pasrec',1,'{PASSRECOVER}','{PASSRECOVER}','',''),
('core','title','18','title_header',1,'{SUBTITLE} - {MAINTITLE}','{SUBTITLE} - {MAINTITLE}','',''),
('core','title','19','title_header_index',1,'{MAINTITLE} - {DESCRIPTION}','{MAINTITLE} - {DESCRIPTION}','',''),
('core','trash','01','trash_prunedelay',2,'7','7','0,1,2,3,4,5,7,10,15,20,30,45,60,90,120',''),
('core','trash','11','trash_forum',3,'1','1','',''),
('core','trash','12','trash_page',3,'1','1','',''),
('core','trash','13','trash_pm',3,'1','1','',''),
('core','trash','14','trash_user',3,'1','1','',''),
('core','users','01','disablereg',3,'0','0','',''),
('core','users','03','disablewhosonline',3,'0','0','',''),
('core','users','05','maxusersperpage',2,'50','50','5,10,15,20,25,30,40,50,60,70,100,200,500',''),
('core','users','07','regrequireadmin',3,'0','0','',''),
('core','users','10','regnoactivation',3,'0','0','',''),
('core','users','10','useremailchange',3,'0','0','',''),
('core','users','10','user_email_noprotection',3,'0','0','',''),
('core','users','11','usertextimg',3,'0','0','',''),
('core','users','12','av_maxsize',1,'8000','8000','',''),
('core','users','12','av_maxx',1,'64','64','',''),
('core','users','12','av_maxy',1,'64','64','',''),
('core','users','12','usertextmax',1,'300','300','',''),
('core','users','13','sig_maxsize',1,'32000','32000','',''),
('core','users','13','sig_maxx',1,'550','550','',''),
('core','users','13','sig_maxy',1,'100','100','',''),
('core','users','14','ph_maxsize',1,'32000','32000','',''),
('core','users','14','ph_maxx',1,'128','128','',''),
('core','users','14','ph_maxy',1,'128','128','',''),
('core','users','21','forcerememberme',3,'0','0','',''),
('core','version','01','revision',0,'','','','');

CREATE TABLE `sed_core` (
  `ct_id` mediumint NOT NULL auto_increment,
  `ct_code` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `ct_title` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `ct_version` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `ct_state` tinyint unsigned NOT NULL default '1',
  `ct_lock` tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (`ct_id`),
  KEY `ct_code` (`ct_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sed_core` (`ct_code`, `ct_title`, `ct_version`, `ct_state`, `ct_lock`) VALUES
('admin', 'Administration panel', '0.7.0', 1, 1),
('message', 'Messages', '0.7.0', 1, 1),
('users', 'Users', '0.7.0', 1, 1);

CREATE TABLE `sed_extra_fields` (
  `field_location` varchar(255) collate utf8_unicode_ci NOT NULL,
  `field_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `field_type` varchar(255) collate utf8_unicode_ci NOT NULL,
  `field_html` text collate utf8_unicode_ci NOT NULL,
  `field_variants` text collate utf8_unicode_ci NOT NULL,
  `field_default` text collate utf8_unicode_ci NOT NULL,
  `field_required` tinyint(1) unsigned NOT NULL default '0',
  `field_parse` varchar(32) collate utf8_unicode_ci NOT NULL default 'HTML',
  `field_description` text collate utf8_unicode_ci NOT NULL,
  KEY `field_location` (`field_location`),
  KEY `field_name` (`field_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_groups` (
  `grp_id` int NOT NULL auto_increment,
  `grp_alias` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `grp_level` tinyint NOT NULL default '1',
  `grp_disabled` tinyint NOT NULL default '0',
  `grp_hidden` tinyint NOT NULL default '0',
  `grp_title` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `grp_desc` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `grp_icon` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `grp_pfs_maxfile` int NOT NULL default '0',
  `grp_pfs_maxtotal` int NOT NULL default '0',
  `grp_ownerid` int NOT NULL default '0',
  `grp_maintenance` tinyint NOT NULL default '0',
  PRIMARY KEY  (`grp_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7;


INSERT INTO `sed_groups` (`grp_id`, `grp_alias`, `grp_level`, `grp_disabled`, `grp_hidden`, `grp_title`, `grp_desc`, `grp_icon`, `grp_pfs_maxfile`, `grp_pfs_maxtotal`, `grp_ownerid`, `grp_maintenance`) VALUES
(1, 'guests', 0, 0, 0, 'Guests', '', '', 0, 0, 1, 0),
(2, 'inactive', 1, 0, 0, 'Inactive', '', '', 0, 0, 1, 0),
(3, 'banned', 1, 0, 0, 'Banned', '', '', 0, 0, 1, 0),
(4, 'members', 1, 0, 0, 'Members', '', '', 0, 0, 1, 0),
(5, 'administrators', 99, 0, 0, 'Administrators', '', '', 256, 1024, 1, 1),
(6, 'moderators', 50, 0, 0, 'Moderators', '', '', 256, 1024, 1, 1);

CREATE TABLE `sed_groups_users` (
  `gru_userid` int NOT NULL default '0',
  `gru_groupid` int NOT NULL default '0',
  `gru_state` tinyint NOT NULL default '0',
  `gru_extra1` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `gru_extra2` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  UNIQUE KEY `gru_groupid` (`gru_groupid`,`gru_userid`),
  KEY `gru_userid` (`gru_userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `sed_logger` (
  `log_id` mediumint NOT NULL auto_increment,
  `log_date` int NOT NULL default '0',
  `log_ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `log_name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `log_group` varchar(4) collate utf8_unicode_ci NOT NULL default 'def',
  `log_text` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_online` (
  `online_id` int NOT NULL auto_increment,
  `online_ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `online_name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `online_lastseen` int NOT NULL default '0',
  `online_location` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `online_subloc` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `online_userid` int NOT NULL default '0',
  `online_shield` int NOT NULL default '0',
  `online_action` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `online_hammer` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`online_id`),
  KEY `online_lastseen` (`online_lastseen`),
  KEY `online_userid` (`online_userid`),
  KEY `online_name` (`online_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_plugins` (
  `pl_id` mediumint NOT NULL auto_increment,
  `pl_hook` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `pl_code` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `pl_part` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `pl_title` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pl_file` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `pl_order` tinyint unsigned NOT NULL default '10',
  `pl_active` tinyint unsigned NOT NULL default '1',
  `pl_module` tinyint unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (`pl_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_rated` (
  `rated_id` int unsigned NOT NULL auto_increment,
  `rated_code` varchar(32) collate utf8_unicode_ci default NULL,
  `rated_userid` int default NULL,
  `rated_value` tinyint unsigned NOT NULL default '0',
  PRIMARY KEY  (`rated_id`),
  KEY `rated_code` (`rated_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_ratings` (
  `rating_id` int NOT NULL auto_increment,
  `rating_code` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `rating_state` tinyint NOT NULL default '0',
  `rating_average` decimal(5,2) NOT NULL default '0.00',
  `rating_creationdate` int NOT NULL default '0',
  `rating_text` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`rating_id`),
  KEY `rating_code` (`rating_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_referers` (
  `ref_url` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `ref_date` int unsigned NOT NULL default '0',
  `ref_count` int NOT NULL default '0',
  PRIMARY KEY  (`ref_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_stats` (
  `stat_name` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `stat_value` int NOT NULL default '0',
  PRIMARY KEY  (`stat_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `sed_stats` (`stat_name`, `stat_value`) VALUES
('totalpages', 0),
('totalmailsent', 0),
('totalmailpmnot', 0),
('totalpms', 0),
('totalantihammer', 0),
('textboxerprev', 0),
('version', 999);

CREATE TABLE `sed_structure` (
  `structure_id` mediumint NOT NULL auto_increment,
  `structure_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `structure_path` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `structure_tpl` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `structure_title` varchar(128) collate utf8_unicode_ci NOT NULL,
  `structure_desc` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `structure_icon` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `structure_group` tinyint NOT NULL default '0',
  `structure_order` varchar(32) collate utf8_unicode_ci NOT NULL default 'title.asc',
  `structure_pagecount` mediumint NOT NULL default '0',
  `structure_ratings` tinyint NOT NULL default 1,
  PRIMARY KEY  (`structure_id`),
  KEY `structure_path` (`structure_path`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_tags` (
  `tag` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_tag_references` (
  `tag` varchar(255) collate utf8_unicode_ci NOT NULL,
  `tag_item` int NOT NULL,
  `tag_area` varchar(64) collate utf8_unicode_ci NOT NULL default 'pages',
  PRIMARY KEY  (`tag`,`tag_area`,`tag_item`),
  KEY `tag_item` (`tag_item`),
  KEY `tag_area` (`tag_area`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_trash` (
  `tr_id` int NOT NULL auto_increment,
  `tr_date` int unsigned NOT NULL default '0',
  `tr_type` varchar(24) collate utf8_unicode_ci NOT NULL default '',
  `tr_title` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `tr_itemid` varchar(24) collate utf8_unicode_ci NOT NULL default '',
  `tr_trashedby` int NOT NULL default '0',
  `tr_datas` mediumblob,
  PRIMARY KEY  (`tr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `sed_updates` (
  `upd_param` VARCHAR(255) NOT NULL,
  `upd_value` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`upd_param`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `sed_updates` (`upd_param`, `upd_value`) VALUES
('revision', '$Rev$'),
('branch', 'siena');

CREATE TABLE `sed_users` (
  `user_id` int unsigned NOT NULL auto_increment,
  `user_banexpire` int default '0',
  `user_name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `user_password` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `user_maingrp` int NOT NULL default '4',
  `user_country` char(2) collate utf8_unicode_ci NOT NULL default '',
  `user_text` text collate utf8_unicode_ci NOT NULL,
  `user_avatar` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `user_photo` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `user_signature` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `user_occupation` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `user_location` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `user_timezone` decimal(2,1) NOT NULL default '0',
  `user_birthdate` DATE NOT NULL DEFAULT '0000-00-00',
  `user_gender` char(1) collate utf8_unicode_ci NOT NULL default 'U',
  `user_irc` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `user_msn` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `user_icq` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `user_website` varchar(128) collate utf8_unicode_ci NOT NULL default '',
  `user_email` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `user_hideemail` tinyint unsigned NOT NULL default '1',
  `user_pmnotify` tinyint unsigned NOT NULL default '0',
  `user_newpm` tinyint unsigned NOT NULL default '0',
  `user_skin` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `user_lang` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `user_regdate` int NOT NULL default '0',
  `user_lastlog` int NOT NULL default '0',
  `user_lastvisit` int NOT NULL default '0',
  `user_lastip` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `user_logcount` int unsigned NOT NULL default '0',
  `user_postcount` int default '0',
  `user_sid` char(32) collate utf8_unicode_ci NOT NULL default '',
  `user_lostpass` char(32) collate utf8_unicode_ci NOT NULL default '',
  `user_auth` text collate utf8_unicode_ci,
  `user_theme` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `user_token` char(16) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`user_id`),
  KEY `user_password` (`user_password`),
  KEY `user_regdate` (`user_regdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
