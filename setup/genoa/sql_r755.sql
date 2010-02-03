/* r755 Ticket #172 Move essential markItUp bbcodes to default package*/
DELETE FROM `sed_bbcode` WHERE `bbc_name` LIKE 'h_';
DELETE FROM `sed_bbcode` WHERE `bbc_name` = 'list';
DELETE FROM `sed_bbcode` WHERE `bbc_name` = 'li';
DELETE FROM `sed_bbcode` WHERE `bbc_name` = 'li_short';

INSERT INTO `sed_bbcode` (`bbc_name`, `bbc_mode`, `bbc_pattern`, `bbc_replacement`, `bbc_container`, `bbc_enabled`,
	`bbc_priority`, `bbc_plug`, `bbc_postrender`)
VALUES
	('h','pcre','\\[h([1-6])\\](.+?)\\[/h\\1\\]','<h$1>$2</h$1>',1,1,128,'',0),
	('list','str','[list]','<ul>',1,1,128,'',0),
	('list','str','[/list]','</ul>',1,1,128,'',0),
	('ol','str','[ol]','<ol>',1,1,128,'',0),
	('ol','str','[/ol]','</ol>',1,1,128,'',0),
	('li','str','[li]','<li>',1,1,128,'',0),
	('li','str','[/li]','</li>',1,1,128,'',0),
	('li_short','pcre','\\[\\*\\](.*?)\\n','<li>$1</li>',0,1,128,'',0);