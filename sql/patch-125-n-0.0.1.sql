/* More indexes for speed */
ALTER TABLE sed_pages ADD INDEX (page_alias), ADD INDEX (page_state), ADD INDEX (page_date);
ALTER TABLE sed_structure ADD INDEX (structure_path);
ALTER TABLE sed_users ADD INDEX (user_password), ADD INDEX (user_regdate);
ALTER TABLE sed_forum_topics ADD INDEX (ft_movedto);
ALTER TABLE sed_forum_posts ADD INDEX (fp_updated), ADD INDEX (fp_posterid), ADD INDEX (fp_sectionid);
ALTER TABLE sed_online ADD INDEX (online_userid), ADD INDEX (online_name);

/* Size limitation removal */
ALTER TABLE sed_auth MODIFY auth_code VARCHAR(255), MODIFY auth_option VARCHAR(255);
ALTER TABLE sed_pages MODIFY page_cat VARCHAR(255), MODIFY page_alias VARCHAR(255);
ALTER TABLE sed_structure MODIFY structure_code VARCHAR(255);

/* Rendered cache support */
ALTER TABLE sed_forum_posts ADD fp_html TEXT NOT NULL;
ALTER TABLE sed_pages ADD page_html TEXT NOT NULL;
ALTER TABLE sed_pm ADD pm_html TEXT NOT NULL;

/* New bbcodes feature */
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '10', 'parser_custom', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '10', 'parser_cache', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'page', '03', 'count_admin', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '30', 'jquery', 3, '1');

/* This table is used by parser only, editor part is separate */
DROP TABLE IF EXISTS sed_bbcode;
CREATE TABLE sed_bbcode (
	bbc_id INT NOT NULL AUTO_INCREMENT,
	bbc_name VARCHAR(100) NOT NULL,
	bbc_mode ENUM('str', 'ereg', 'pcre', 'callback') NOT NULL DEFAULT 'str',
	bbc_pattern VARCHAR(255) NOT NULL,
	bbc_replacement TEXT NOT NULL,
	bbc_container TINYINT NOT NULL DEFAULT 1,
	bbc_enabled TINYINT(1) NOT NULL DEFAULT 1,
	bbc_priority TINYINT UNSIGNED NOT NULL DEFAULT 128,
	bbc_plug VARCHAR(100) NOT NULL DEFAULT '',
	bbc_postrender TINYINT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (bbc_id),
	KEY (bbc_enabled),
	KEY (bbc_priority)
);

/* Basic bbcode package */
INSERT INTO `sed_bbcode` VALUES
(1,'b','str','[b]','<strong>',1,1,128,'',0),
(2,'b','str','[/b]','</strong>',0,1,128,'',0),(3,'i','str','[i]','<em>',1,1,128,'',0),
(4,'i','str','[/i]','</em>',1,1,128,'',0),
(5,'u','str','[u]','<span style=\"text-decoration:underline\">',1,1,128,'',0),
(6,'u','str','[/u]','</span>',1,1,128,'',0),
(7,'s','str','[s]','<span style=\"text-decoration:line-through\">',1,1,128,'',0),
(8,'s','str','[/s]','</span>',1,1,128,'',0),
(9,'center','str','[center]','<div style=\"text-align:center\">',1,1,128,'',0),
(10,'center','str','[/center]','</div>',1,1,128,'',0),
(11,'left','str','[left]','<div style=\"text-align:left\">',1,1,128,'',0),
(12,'left','str','[/left]','</div>',1,1,128,'',0),
(13,'right','str','[right]','<div style=\"text-align:right\">',1,1,128,'',0),
(14,'right','str','[/right]','</div>',1,1,128,'',0),
(15,'justify','str','[justify]','<div style=\"text-align:justify\">',1,1,128,'',0),
(16,'justify','str','[/justify]','</div>',1,1,128,'',0),
(17,'pre','str','[pre]','<pre>',1,1,128,'',0),
(18,'pre','str','[/pre]','</pre>',0,1,128,'',0),
(19,'nbsp','str','[_]','&nbsp;',0,1,128,'',0),
(31,'email','callback','\\[email=(\\w[\\._\\w\\-]+@[\\w\\.\\-]+\\.[a-z]+)\\](.+?)\\[/email\\]','return sed_obfuscate(''<a href="mailto:''.$input[1].''">''.$input[2].''</a>'');',1,1,128,'',0),
(26,'quote','pcre','\\[quote=(.+?)\\](.+?)\\[/quote\\]','<blockquote><strong>$1:</strong><hr />$2</blockquote>',1,1,128,'',0),
(24,'quote','pcre','\\[quote\\](.+?)\\[/quote\\]','<blockquote>$1</blockquote>',1,1,128,'',0),
(23,'color','pcre','\\[color=(#?\\w+)\\](.+?)\\[/color\\]','<span style=\"color:$1\">$2</span>',1,1,128,'',0),
(27,'img','pcre','\\[img\\]((?:http://|https://|ftp://)?[^"'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\[/img\\]','<img src=\"$1\" alt=\"\" />',1,1,128,'',0),
(28,'img','pcre','\\[img=((?:http://|https://|ftp://)?[^\\]"'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\]((?:http://|https://|ftp://)?[^"'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\[/img\\]','<a href=\"$1\"><img src=\"$2\" alt=\"\" /></a>',1,1,128,'',0),
(29,'url','pcre','\\[url=((?:http://|https://|ftp://)?[^\\s"'':\\[]+)\\](.+?)\\[/url\\]','<a href=\"$1\">$2</a>',1,1,128,'',0),
(30,'url','pcre','\\[url\\]((?:http://|https://|ftp://)?[^\\s"'':]+)\\[/url\\]','<a href=\"$1\">$1</a>',1,1,128,'',0),
(32,'code','callback','\\[code\\](.+?)\\[/code\\]','return ''<pre class="code">''.sed_bbcode_cdata($input[1]).''</pre>'';',1,1,1,'',0);

/* Members and extended downloads enablement */
ALTER TABLE sed_pages MODIFY page_file TINYINT DEFAULT NULL;

/* Subforums patch */
ALTER TABLE sed_forum_sections ADD COLUMN fs_masterid smallint(5) unsigned NOT NULL default '0';

/* Uninstall textboxer */
DELETE FROM sed_auth WHERE auth_option = 'textboxer2';
DELETE FROM sed_plugins WHERE pl_code = 'textboxer2';
DELETE FROM sed_config WHERE config_cat = 'textboxer2';

/* Patch recentitems config */
INSERT INTO sed_config VALUES ('plug', 'recentitems', 05, 'fd', 2, 'Standard', 'Standard, Parent only, Subforums with Master Forums, Just Topics', 'Topic path display');

/* Patch forum icons */
UPDATE sed_forum_sections SET fs_icon = 'images/admin/forums.gif' WHERE fs_icon = 'system/img/admin/forums.gif';