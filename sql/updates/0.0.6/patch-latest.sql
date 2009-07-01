/* r547 Anti bump enhancement */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('core', 'forums', '12', 'antibumpforums', 3, '0'), ('core', 'forums', '12', 'mergeforumposts', 3, '1');

/* r568 Enable comments/ratings for structure */
ALTER TABLE sed_structure ADD COLUMN structure_comments tinyint(1) NOT NULL default '1';
ALTER TABLE sed_structure ADD COLUMN structure_ratings tinyint(1) NOT NULL default '1';

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
INSERT INTO `sed_config` ( `config_owner` , `config_cat` , `config_order` , `config_name` , `config_type` , `config_value` , `config_default` , `config_text` ) VALUES ('plug', 'news', '2', 'othetcat', '1', '', '', 'Extra category codes, comma separated');

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
	('h','pcre','\\[h([1-6])\\](.+?)\\[/h$1\\]','<h$1>$2</h$1>',1,1,128,'',0),
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