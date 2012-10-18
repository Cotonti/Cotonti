/* r568 Enable comments/ratings for structure */
ALTER TABLE sed_structure ADD COLUMN structure_comments tinyint(1) NOT NULL default 1;
ALTER TABLE sed_structure ADD COLUMN structure_ratings tinyint(1) NOT NULL default 1;

/* r621 Enable comments/ratings for structure */
ALTER TABLE sed_polls ADD COLUMN poll_code varchar(16) NOT NULL default '';
UPDATE sed_polls, sed_forum_topics SET sed_polls.poll_code=sed_forum_topics.ft_id
WHERE sed_polls.poll_id=sed_forum_topics.ft_poll;

/* r629 Unlock all polls */
UPDATE sed_polls SET poll_state='0';

/* r683 Search pagination */
INSERT INTO sed_config VALUES ('plug', 'search', '1', 'results', 2, '25', '5,10,15,20,25,50,100', 'Results listed in a single page');

/* r684 Increase lengths */
ALTER TABLE sed_forum_topics CHANGE ft_title ft_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE sed_forum_topics CHANGE ft_desc ft_desc VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE sed_forum_sections CHANGE fs_lt_title fs_lt_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE sed_forum_structure CHANGE fn_title fn_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE sed_pfs_folders CHANGE pff_title pff_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

ALTER TABLE sed_pm CHANGE pm_title pm_title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '';

/* r733 Post merge timeout */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('core', 'forums', '12', 'mergetimeout', 2, '0');

/* r745 News plugin updates */
INSERT INTO `sed_config` ( `config_owner` , `config_cat` , `config_order` , `config_name` , `config_type` , `config_value` , `config_default` , `config_text` ) VALUES ('plug', 'news', '2', 'othercat', '1', '', '', 'Extra category codes, comma separated');

UPDATE `sed_config` SET `config_order` = '3' WHERE `config_owner` = 'plug' AND  `config_cat` = 'news' AND `config_name` = 'maxpages' LIMIT 1 ;

/* r751 MEDIUMTEXT page text expansion */
ALTER TABLE `sed_pages` MODIFY `page_text` MEDIUMTEXT collate utf8_unicode_ci;
ALTER TABLE `sed_pages` MODIFY `page_html` MEDIUMTEXT collate utf8_unicode_ci;

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

/* r758 LDU legacy removal */
ALTER TABLE `sed_users` DROP `user_gallerycount`;
ALTER TABLE `sed_users` DROP `user_jrnupdated`;
ALTER TABLE `sed_users` DROP `user_jrnpagescount`;

/* r768 Add pagination settings */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'comments', '04', 'maxcommentsperpage', 2, '15', '', ''),
('core', 'forums', '13', 'maxpostsperpage', 2, '15', '', ''),
('core', 'page', '05', 'maxlistsperpage', 2, '15', '', ''),
('core', 'pfs', '06', 'maxpfsperpage', 2, '15', '', ''),
('core', 'pm', '11', 'maxpmperpage', 2, '15', '', '');

/* r772 PFS popup closure config option */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'pfs', '11', 'pfs_winclose', 3, '0', '', '');

/* r801 search plugin configuration update */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('plug', 'search', '8', 'addfields', 1, '', '', 'Additional pages fields for search, separated by commas. Example "page_extra1,page_extra2,page_key"'),
('plug', 'search', '6', 'showtext_ext', 3, '1', '', 'Show text in result for extended search'),
('plug', 'search', '5', 'showtext', 3, '1', '', 'Show text in result for general search'),
('plug', 'search', '4', 'maxitems_ext', 2, '100', '15,30,50,80,100,150,200,300', 'Maximum results lines for extended search'),
('plug', 'search', '1', 'maxwords', 2, '5', '3,5,8,10', 'Maximum search words'),
('plug', 'search', '2', 'maxsigns', 2, '40', '20,30,40,50,60,70,80', 'Maximum signs in query'),
('plug', 'search', '2', 'minsigns', 2, '3', '2,3,4,5', 'Min. signs in query'),
('plug', 'search', '3', 'maxitems', 2, '50', '15,30,50,80,100,150,200', 'Maximum results lines for general search');

/* r813 add missing config for tags plugin (if updating from 0.0.5) */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('plug', 'tags', '12', 'index', 2, 'pages', 'pages,forums,all', 'Index page tag cloud area');

/* r826 Add size of comment */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'comments', '05', 'commentsize', 2, '0', '', '');

/* r830 Add turn-off for ajax */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'main', '31', 'turnajax', 3, '1', '1', '');

/* r831 Option to disable email protection in user profile */
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES
('core', 'users', '10', 'user_email_noprotection', 3, '0', '', '');
