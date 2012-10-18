/* r91 more fix, hooks fix */
INSERT INTO `sed_bbcode` VALUES (NULL,'more','str','[more]','<!--more-->',1,1,128,'',0),(NULL,'more','str','[/more]','&nbsp;',1,1,128,'',0);
INSERT INTO `sed_plugins` VALUES (NULL, 'header.main', 'search', 'header', 'Search', 'search.header', 10, 1), (NULL, 'page.first', 'search', 'page', 'Search', 'search.page.first', 10, 1), (NULL, 'forums.posts.first', 'search', 'forums', 'Search', 'search.forums.posts.first', 10, 1); 

/* r103 remove antibump */
DELETE FROM `sed_config` WHERE `config_owner` = 'core' AND `config_cat` = 'forums' AND `config_name` = 'antibumpforums' LIMIT 1;

/* r104 subforms enhancment */
ALTER TABLE `sed_forum_sections` ADD COLUMN `fs_mastername` varchar(128) NOT NULL;

/* r111 Subforums Enhancment */
ALTER TABLE `sed_forum_sections` ADD COLUMN `fs_allowviewers` tinyint(1) NOT NULL default '1';
ALTER TABLE `sed_users` ADD COLUMN `user_theme` VARCHAR(16) NOT NULL DEFAULT '';

/* r112 Title Mask Enhancments */
UPDATE `sed_config` SET `config_cat` = 'title' WHERE `config_cat` = 'main' AND `config_name` IN ('maintitle', 'subtitle') LIMIT 2;
UPDATE `sed_config` SET `config_order` = '01' WHERE `config_cat` = 'main' AND `config_name` = 'mainurl' LIMIT 1;
UPDATE `sed_config` SET `config_order` = '02' WHERE `config_cat` = 'main' AND `config_name` = 'adminemail' LIMIT 1;
UPDATE `sed_config` SET `config_order` = '03' WHERE `config_cat` = 'main' AND `config_name` = 'hostip' LIMIT 1;
UPDATE `sed_config` SET `config_order` = '04' WHERE `config_cat` = 'main' AND `config_name` = 'cache' LIMIT 1;
UPDATE `sed_config` SET `config_order` = '05' WHERE `config_cat` = 'main' AND `config_name` = 'clustermode' LIMIT 1;
UPDATE `sed_config` SET `config_order` = '06' WHERE `config_cat` = 'main' AND `config_name` = 'gzip' LIMIT 1;
UPDATE `sed_config` SET `config_order` = '07' WHERE `config_cat` = 'main' AND `config_name` = 'devmode' LIMIT 1;

INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('core', 'title', '03', 'title_forum_main', '01', '{FORUM}'),('core', 'title', '04', 'title_forum_topics', '01', '{FORUM} - {SECTION}'),('core', 'title', '05', 'title_forum_posts', '01', '{FORUM} - {TITLE}'),('core', 'title', '06', 'title_forum_newtopic', '01', '{FORUM} - {SECTION}'),('core', 'title', '07', 'title_forum_editpost', '01', '{FORUM} - {SECTION}'),('core', 'title', '08', 'title_list', '01', '{TITLE}'),('core', 'title', '09', 'title_page', '01', '{TITLE}'),('core', 'title', '10', 'title_pfs', '01', '{PFS}'),('core', 'title', '11', 'title_pm_main', '01', '{PM}'),('core', 'title', '12', 'title_pm_send', '01', '{PM}'),('core', 'title', '13', 'title_users_main', '01', '{USERS}'),('core', 'title', '14', 'title_users_details', '01', '{USER} : {NAME}'),('core', 'title', '15', 'title_users_profile', '01', '{PROFILE}'),('core', 'title', '16', 'title_users_edit', '01', '{NAME}'),('core', 'title', '17', 'title_header', '01', '{MAINTITLE} - {SUBTITLE}'),('core', 'title', '18', 'title_header_index', '01', '{MAINTITLE} - {DESCRIPTION}');

/* r128 page extra fields enhancment */
CREATE TABLE `sed_pages_extra_fields` (
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(255) NOT NULL,
  `field_html` text NOT NULL,
  `field_variants` text NOT NULL,
  UNIQUE KEY `field_name` (`field_name`)
) DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci TYPE=MyISAM;

/* r193 Some speed up for page listings */
ALTER TABLE sed_structure ADD COLUMN structure_pagecount mediumint(8) NOT NULL default '0';

/* r143 t#105, Forum topic preview */
ALTER TABLE sed_forum_topics ADD COLUMN ft_preview varchar(128) NOT NULL default '';

/* r145 for edited plugins - recentitems and  recentpolls->indexpolls */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('plug', 'indexpolls', '2', 'mode', '2', 'Recent polls'),('plug', 'indexpolls', '1', 'maxpolls', '2', '1'), ('plug', 'recentitems', 5, 'redundancy', 2, '2');
INSERT INTO `sed_plugins` VALUES (NULL, 'index.tags', 'indexpolls', 'main', 'Indexpolls', 'indexpolls', 10, 1);
INSERT INTO `sed_auth` VALUES (NULL, 1, 'plug', 'indexpolls', 1, 254, 1), (NULL, 2, 'plug', 'indexpolls', 1, 254, 1), (NULL, 3, 'plug', 'indexpolls', 0, 255, 1), (NULL, 4, 'plug', 'indexpolls', 1, 254, 1), (NULL, 5, 'plug', 'indexpolls', 255, 255, 1), (NULL, 6, 'plug', 'indexpolls', 1, 254, 1);
DELETE FROM `sed_config` WHERE `config_owner`='plug' AND `config_cat`='recentitems' AND `config_name`='maxpolls' LIMIT 1;
UPDATE `sed_config` SET `config_value` = 'UTF-8' WHERE `config_cat` = 'skin' AND `config_name` = 'charset' LIMIT 1;

/* r150 Adding markitup`s tags by default */
INSERT INTO `sed_bbcode` (`bbc_id`, `bbc_name`, `bbc_mode`, `bbc_pattern`, `bbc_replacement`, `bbc_container`, `bbc_enabled`, `bbc_priority`, `bbc_plug`, `bbc_postrender`) VALUES (NULL, 'h1', 'str', '[h1]', '<h1>', 1, 1, 128, 'markitup', 0), (NULL, 'h1', 'str', '[/h1]', '</h1>', 1, 1, 128, 'markitup', 0), (NULL, 'h2', 'str', '[h2]', '<h2>', 1, 1, 128, 'markitup', 0), (NULL, 'h2', 'str', '[/h2]', '</h2>', 1, 1, 128, 'markitup', 0), (NULL, 'h3', 'str', '[h3]', '<h3>', 1, 1, 128, 'markitup', 0), (NULL, 'h3', 'str', '[/h3]', '</h3>', 1, 1, 128, 'markitup', 0), (NULL, 'h4', 'str', '[h4]', '<h4>', 1, 1, 128, 'markitup', 0), (NULL, 'h4', 'str', '[/h4]', '</h4>', 1, 1, 128, 'markitup', 0), (NULL, 'h5', 'str', '[h5]', '<h5>', 1, 1, 128, 'markitup', 0), (NULL, 'h5', 'str', '[/h5]', '</h5>', 1, 1, 128, 'markitup', 0), (NULL, 'h6', 'str', '[h6]', '<h6>', 1, 1, 128, 'markitup', 0), (NULL, 'h6', 'str', '[/h6]', '</h6>', 1, 1, 128, 'markitup', 0), (NULL, 'size', 'pcre', '\\[size=([1-2][0-9])\\](.+?)\\[/size\\]', '<span style="font-size:$1pt">$2</span>', 1, 1, 128, 'markitup', 0), (NULL, 'list', 'pcre', '\\[list\\](.+?)\\[/list\\]', '<ul>$1</ul>', 1, 1, 128, 'markitup', 0), (NULL, 'list', 'pcre', '\\[list=(\\w)\\](.+?)\\[/list\\]', '<ol type="$1">$2</ol>', 1, 1, 128, 'markitup', 0), (NULL, 'li', 'str', '[li]', '<li>', 1, 1, 128, 'markitup', 0), (NULL, 'li', 'str', '[/li]', '</li>', 1, 1, 128, 'markitup', 0), (NULL, 'table', 'str', '[table]', '<table>', 1, 1, 128, 'markitup', 0), (NULL, 'table', 'str', '[/table]', '</table>', 1, 1, 128, 'markitup', 0),(NULL, 'tr', 'str', '[tr]', '<tr>', 1, 1, 128, 'markitup', 0), (NULL, 'tr', 'str', '[/tr]', '</tr>', 1, 1, 128, 'markitup', 0), (NULL, 'th', 'str', '[th]', '<th>', 1, 1, 128, 'markitup', 0), (NULL, 'th', 'str', '[/th]', '</th>', 1, 1, 128, 'markitup', 0), (NULL, 'td', 'str', '[td]', '<td>', 1, 1, 128, 'markitup', 0), (NULL, 'td', 'str', '[/td]', '</td>', 1, 1, 128, 'markitup', 0), (NULL, 'hide', 'callback', '\\[hide\\](.+?)\\[/hide\\]', 'return $usr["id"] > 0 ? $input[1] : "<div class=\\"hidden\\">".$L["Hidden"]."</div>";', 1, 1, 150, 'markitup', 1), (NULL, 'spoiler', 'pcre', '\\[spoiler=([^\\]]+)\\](.+?)\\[/spoiler\\]', '<div style="margin:4px 0px 4px 0px"><input type="button" value="$1" onclick="if(this.parentNode.getElementsByTagName(''div'')[0].style.display != '''') { this.parentNode.getElementsByTagName(''div'')[0].style.display = ''''; } else { this.parentNode.getElementsByTagName(''div'')[0].style.display = ''none''; }" /><div style="display:none" class="spoiler">$2</div></div>', 1, 1, 130, 'markitup', 0), (NULL, 'thumb', 'pcre', '\\[thumb=(.*?[^"\\'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\](.*?[^"\\'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\[/thumb\\]', '<a href="datas/users/$2"><img src="$1" alt="$2" /></a>', 1, 1, 128, '', 0), (NULL, 'thumb', 'pcre', '\\[thumb\\](.*?[^"\\'';:\\?]+\\.(?:jpg|jpeg|gif|png))\\[/thumb\\]', '<a href="datas/users/$1"><img src="datas/thumbs/$1" /></a>', 1, 1, 128, '', 0), (NULL, 'pfs', 'pcre', '\\[pfs\\](.*?[^"\\'';:\\?]+\\.(?:jpg|jpeg|gif|png|zip|rar|7z|pdf|txt))\\[/pfs\\]', '<strong><a href="$1">$1</a></strong>', 1, 1, 128, '', 0);

/* r153 Maintenance mode */
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '07', 'maintenance', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '07', 'maintenancereason', 1, '');

ALTER TABLE sed_groups ADD COLUMN grp_maintenance tinyint(1) NOT NULL default '0';

UPDATE sed_groups SET grp_maintenance = '1' WHERE grp_alias = 'administrators';
UPDATE sed_groups SET grp_maintenance = '1' WHERE grp_alias = 'moderators';

/* r155 Fix pfs bbcode */
UPDATE `sed_bbcode` SET `bbc_replacement` = '<strong><a href="datas/users/$1">$1</a></strong>' WHERE `bbc_id` = 64;

/* r187 Universal tag system scheme */

-- Just tags alone, required for autocomplete
CREATE TABLE `sed_tags` (
	`tag` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`tag`)
) DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci TYPE=MyISAM;

-- For tag references, search and other needs
CREATE TABLE `sed_tag_references` (
	`tag` VARCHAR(255) NOT NULL REFERENCES `sed_tags`(`tag`),
	`tag_item` INT NOT NULL,
	`tag_area` VARCHAR(50) NOT NULL DEFAULT 'pages',
	PRIMARY KEY (`tag`, `tag_area`, `tag_item`),
	KEY `tag_item`(`tag_item`),
	KEY `tag_area`(`tag_area`)
) DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci TYPE=MyISAM;

/* r206 Hardened auth system */
ALTER TABLE sed_users ADD user_hashsalt CHAR(16) NOT NULL DEFAULT '';

/* r212 Forum poll enhancements */
ALTER TABLE `sed_forum_sections` ADD COLUMN `fs_allowpolls` tinyint(1) NOT NULL default '0';

/* r224 Improved polls admin part */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('core', 'polls', '02', 'ip_id_polls', '2', 'ip'), ('core', 'polls', '04', 'del_dup_options', '3', '1'), ('core', 'polls', '03', 'max_options_polls', '1', '100');

/* r225 PFS file name conversion */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('core', 'pfs', '03', 'pfstimename', '3', '0');

/* r227 Option to display home link in breadcrumb */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('core', 'skin', '03', 'homebreadcrumb', '3', '0');

/* r230 Set default file download permission masks for pages */
UPDATE sed_auth SET auth_rights = auth_rights + 4 WHERE auth_code = 'page' AND auth_groupid != 5 AND auth_groupid != 3 AND auth_groupid != 2 AND NOT auth_rights & 4 = 4;
UPDATE sed_auth SET auth_rights_lock = auth_rights_lock - 4 WHERE auth_code = 'page' AND auth_groupid != 5 AND auth_groupid != 3 AND auth_groupid != 2 AND auth_groupid != 4 AND auth_rights_lock & 4 = 4;

/* r233 Smilies which are now in sets */
DROP TABLE sed_smilies;

/* r240 New universal extra fields system + extra fields for users */
RENAME TABLE sed_pages_extra_fields  TO sed_extra_fields ;
ALTER TABLE `sed_extra_fields` ADD `field_location` VARCHAR( 255 ) NOT NULL FIRST ;
ALTER TABLE `sed_extra_fields` ADD `field_description` TEXT NOT NULL ;
ALTER TABLE `sed_extra_fields` ADD INDEX ( `field_location` ) ; 
ALTER TABLE `sed_extra_fields` DROP INDEX `field_name`  ;
ALTER TABLE `sed_extra_fields` ADD INDEX ( `field_name` )  ;
INSERT INTO `sed_extra_fields` (`field_location`, `field_name`, `field_type`, `field_html`, `field_variants`, `field_description`) VALUES('pages', 'extra1', 'input', '<input class="text" type="text" maxlength="255" size="56" />', '', ''), ('pages', 'extra2', 'input', '<input class="text" type="text" maxlength="255" size="56" />', '', ''), ('pages', 'extra3', 'input', '<input class="text" type="text" maxlength="255" size="56" />', '', ''), ('pages', 'extra4', 'input', '<input class="text" type="text" maxlength="255" size="56" />', '', ''), ('pages', 'extra5', 'input', '<input class="text" type="text" maxlength="255" size="56" />', '', ''), ('users', 'extra1', 'input', '<input class="text" type="text" maxlength="255" size="56" />', '', ''), ('users', 'extra2', 'input', '<input class="text" type="text" maxlength="255" size="56" />', '', ''), ('users', 'extra3', 'input', '<input class="text" type="text" maxlength="255" size="56" />', '', ''), ('users', 'extra4', 'input', '<input class="text" type="text" maxlength="255" size="56" />', '', ''), ('users', 'extra5', 'input', '<input class="text" type="text" maxlength="255" size="56" />', '', ''), ('users', 'extra6', 'textarea', '<textarea cols="80" rows="6" ></textarea>', '', ''), ('users', 'extra7', 'textarea', '<textarea cols="80" rows="6" ></textarea>', '', ''), ('users', 'extra8', 'textarea', '<textarea cols="80" rows="6" ></textarea>', '', ''), ('users', 'extra9', 'textarea', '<textarea cols="80" rows="6" ></textarea>', '', '');
DELETE  FROM sed_config WHERE config_owner = 'core' AND config_cat = 'users' AND config_name LIKE 'extra%';

/* r241 Multiple choice in polls */
ALTER TABLE `sed_polls` ADD COLUMN `poll_multiple` tinyint(1) NOT NULL default '0';

/* r242 xhtml code removed from php for plug whosonline added avatar configs */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('plug', 'whosonline', '1', 'showavatars', 3, '1'), ('plug', 'whosonline', '2', 'miniavatar_x', 1, '16'), ('plug', 'whosonline', '3', 'miniavatar_y', 1, '16');

/* r243 Photo,avatar and signature resize optioning */
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '10', 'av_resize', 3, '0'), ('core', 'users', '10', 'ph_resize', 3, '0'), ('core', 'users', '10', 'sig_resize', 3, '0');

/* r255 PFS file check conifg -tags plugin default install*/
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '04', 'pfsfilecheck', 3, '1'), ('core', 'pfs', '05', 'pfsnomimepass', 3, '1');
DELETE FROM `sed_auth` WHERE `auth_groupid` = 'plug' AND `auth_code` = 'tags';
DELETE FROM `sed_config` WHERE `config_owner` = 'plug' AND `config_cat` = 'tags';
DELETE FROM `sed_plugins` WHERE `pl_code` = 'tags';
INSERT INTO `sed_auth` (`auth_id`, `auth_groupid`, `auth_code`, `auth_option`, `auth_rights`, `auth_rights_lock`, `auth_setbyuserid`) VALUES (NULL, 5, 'plug', 'tags', 255, 255, 1),(NULL, 6, 'plug', 'tags', 3, 124, 1),(NULL, 2, 'plug', 'tags', 1, 254, 1),(NULL, 3, 'plug', 'tags', 0, 255, 1),(NULL, 4, 'plug', 'tags', 3, 124, 1),(NULL, 1, 'plug', 'tags', 1, 254, 1);
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES ('plug', 'tags', '3', 'title', 3, '1', '', 'Capitalize first latters of keywords'),('plug', 'tags', '4', 'translit', 3, '0', '', 'Transliterate Tags in URLs'),('plug', 'tags', '6', 'order', 2, 'tag', 'tag,cnt', 'Cloud output order - alphabetical or descending frequency'),('plug', 'tags', '7', 'limit', 1, '0', '', 'Max. tags per items, 0 is unlimited'),('plug', 'tags', '8', 'lim_pages', 1, '0', '', 'Limit of tags in a cloud displayed for pages, 0 is unlimited'),('plug', 'tags', '1', 'pages', 3, '1', '', 'Enable Tags for Pages'),('plug', 'tags', '9', 'lim_forums', 1, '0', '', 'Limit of tags in a cloud displayed in forums, 0 is unlimited'),('plug', 'tags', '2', 'forums', 3, '1', '', 'Enable Tags for Forums');
INSERT INTO `sed_plugins` (`pl_id`, `pl_hook`, `pl_code`, `pl_part`, `pl_title`, `pl_file`, `pl_order`, `pl_active`) VALUES (NULL, 'page.tags', 'tags', 'page', 'Tags', 'tags.page', 10, 1),(NULL, 'page.edit.tags', 'tags', 'page.edit.tags', 'Tags', 'tags.page.edit.tags', 10, 1),(NULL, 'page.edit.update.done', 'tags', 'page.edit', 'Tags', 'tags.page.edit', 10, 1),(NULL, 'page.edit.delete.done', 'tags', 'page.delete', 'Tags', 'tags.page.delete', 10, 1),(NULL, 'page.add.tags', 'tags', 'page.add.tags', 'Tags', 'tags.page.add.tags', 10, 1),(NULL, 'page.add.add.done', 'tags', 'page.add', 'Tags', 'tags.page.add', 10, 1),(NULL, 'index.tags', 'tags', 'index', 'Tags', 'tags.index', 10, 1),(NULL, 'list.tags', 'tags', 'list', 'Tags', 'tags.list', 10, 1),(NULL, 'forums.topics.loop', 'tags', 'forums.topics', 'Tags', 'tags.forums.topics', 10, 1),(NULL, 'forums.sections.tags', 'tags', 'forums', 'Tags', 'tags.forums', 10, 1),(NULL, 'forums.newtopic.tags', 'tags', 'forums.newtopic.tags', 'Tags', 'tags.forums.newtopic.tags', 10, 1),(NULL, 'forums.newtopic.newtopic.done', 'tags', 'forums.newtopic', 'Tags', 'tags.forums.newtopic', 10, 1),(NULL, 'forums.editpost.tags', 'tags', 'forums.editpost.tags', 'Tags', 'tags.forums.editpost.tags', 10, 1),(NULL, 'forums.editpost.update.done', 'tags', 'forums.editpost', 'Tags', 'tags.forums.editpost', 10, 1),(NULL, 'forums.topics.delete.done', 'tags', 'forums.delete', 'Tags', 'tags.forums.delete', 10, 1),(NULL, 'standalone', 'tags', 'search', 'Tags', 'tags', 0, 1);

/* r272 subforums duplicate data removal */
DROP TABLE IF EXISTS sed_forum_subforums;

/* r274 Avatar/photo resizing final structure */
DELETE FROM `sed_config` WHERE `config_owner` = 'core' AND `config_cat` = 'users' AND `config_name` = 'av_resize' LIMIT 1;
DELETE FROM `sed_config` WHERE `config_owner` = 'core' AND `config_cat` = 'users' AND `config_name` = 'sig_resize' LIMIT 1;
DELETE FROM `sed_config` WHERE `config_owner` = 'core' AND `config_cat` = 'users' AND `config_name` = 'ph_resize' LIMIT 1;

/* r285 24 symbols for user name - not enough */ 
ALTER TABLE `sed_users` CHANGE `user_name` `user_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

/* r289 added config option for autovalidate*/
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('core', 'page', '06', 'autovalidate', 3, '1');

/* r290 indexpolls*/
DELETE FROM `sed_plugins` WHERE `pl_file` = 'indexpolls.main';

/* r293 Increase size of user_name and page category title (begins in r285)*/ 
ALTER TABLE `sed_com` CHANGE `com_author` `com_author` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ; 
ALTER TABLE `sed_forum_posts` CHANGE `fp_postername` `fp_postername` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;  
ALTER TABLE `sed_forum_posts` CHANGE `fp_updater` `fp_updater` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL  ;
ALTER TABLE `sed_forum_sections` CHANGE `fs_lt_postername` `fs_lt_postername` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;  
ALTER TABLE `sed_forum_topics` CHANGE `ft_lastpostername` `ft_lastpostername` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ; 
ALTER TABLE `sed_forum_topics` CHANGE `ft_firstpostername` `ft_firstpostername` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;  
ALTER TABLE `sed_logger` CHANGE `log_name` `log_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL  ;
ALTER TABLE `sed_online` CHANGE `online_name` `online_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;  
ALTER TABLE `sed_pages` CHANGE `page_author` `page_author` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL  ;
ALTER TABLE `sed_pm` CHANGE `pm_fromuser` `pm_fromuser` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL  ;
ALTER TABLE `sed_structure` CHANGE `structure_title` `structure_title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

/* r372 Fix, adding new hook for MarkitUp!, add search url style config */ 
DELETE FROM `sed_plugins` WHERE `pl_code` = 'markitup' AND `pl_file` = 'markitup.ajax';
REPLACE INTO `sed_plugins` (`pl_id` ,`pl_hook` ,`pl_code` ,`pl_part` ,`pl_title` ,`pl_file` ,`pl_order` ,`pl_active`) VALUES (NULL , 'ajax', 'markitup', 'preview', 'MarkItUp!', 'markitup.ajax', '10', '1');
INSERT INTO `sed_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_text`) VALUES ('plug', 'search', '1', 'searchurl', 2, 'Normal', 'Normal,Single', 'Type of forum post link to use, Single uses a Single post view, while Normal uses the traditional thread/jump-to link');

/* r387 Structure page count fix for news */ 
UPDATE sed_structure SET structure_pagecount=structure_pagecount+1 WHERE structure_code='news';

/* r400 change poll type */ 
ALTER TABLE sed_polls MODIFY poll_type VARCHAR(100) NOT NULL DEFAULT 'index';
UPDATE sed_polls SET poll_type = 'index' WHERE poll_type = '0';
UPDATE sed_polls SET poll_type = 'forum' WHERE poll_type = '1';

/* r423 comments expand by default option */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('core', 'comments', '03', 'expand_comments', 3, '1');

/* r428 ratings allow rating change */
INSERT INTO `sed_config` (`config_owner` ,`config_cat` ,`config_order` ,`config_name` ,`config_type` ,`config_value`) VALUES ('core', 'ratings', '02', 'ratings_allowchange', 3, '0');

/* r429 turn on comments link on indexpolls */
INSERT INTO `sed_config` ( `config_owner` , `config_cat` , `config_order` , `config_name` , `config_type` , `config_value` , `config_default` , `config_text` ) VALUES ('plug', 'indexpolls', '3', 'commentslink', '3', '1', '', 'Show comments link');

/* r444 move $cfg['mainurl'] to config.php */
DELETE FROM `sed_config` WHERE `config_name` = 'mainurl';