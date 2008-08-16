CREATE TABLE sed_auth (
  auth_id mediumint(8) NOT NULL auto_increment,
  auth_groupid int(11) NOT NULL default '0',
  auth_code varchar(255) NOT NULL default '',
  auth_option varchar(255) NOT NULL default '',
  auth_rights tinyint(1) unsigned NOT NULL default '0',
  auth_rights_lock tinyint(1) unsigned NOT NULL default '0',
  auth_setbyuserid int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (auth_id),
  KEY auth_groupid (auth_groupid),
  KEY auth_code (auth_code)
) TYPE=MyISAM;

CREATE TABLE sed_banlist (
  banlist_id int(11) NOT NULL auto_increment,
  banlist_ip varchar(15) NOT NULL default '',
  banlist_email varchar(64) NOT NULL default '',
  banlist_reason varchar(64) NOT NULL default '',
  banlist_expire int(11) default '0',
  PRIMARY KEY  (banlist_id),
  KEY banlist_ip (banlist_ip)
) TYPE=MyISAM;

CREATE TABLE sed_cache (
  c_name varchar(16) NOT NULL default '',
  c_expire int(11) NOT NULL default '0',
  c_auto tinyint(1) NOT NULL default '1',
  c_value text,
  PRIMARY KEY  (c_name)
) TYPE=MyISAM;

CREATE TABLE sed_com (
  com_id int(11) NOT NULL auto_increment,
  com_code varchar(16) NOT NULL default '',
  com_author varchar(24) NOT NULL default '',
  com_authorid int(11) default NULL,
  com_authorip varchar(15) NOT NULL default '',
  com_text text NOT NULL,
  com_date int(11) NOT NULL default '0',
  com_count int(11) NOT NULL default '0',
  com_isspecial tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (com_id),
  KEY com_code (com_code)
) TYPE=MyISAM;

CREATE TABLE sed_config (
  config_owner varchar(24) NOT NULL default 'core',
  config_cat varchar(24) NOT NULL default '',
  config_order char(2) NOT NULL default '00',
  config_name varchar(32) NOT NULL default '',
  config_type tinyint(2) NOT NULL default '0',
  config_value text NOT NULL,
  config_default varchar(255) NOT NULL default '',
  config_text varchar(255) NOT NULL default ''
) TYPE=MyISAM;

CREATE TABLE sed_core (
  ct_id mediumint(8) NOT NULL auto_increment,
  ct_code varchar(24) NOT NULL default '',
  ct_title varchar(64) NOT NULL default '',
  ct_version varchar(16) NOT NULL default '',
  ct_state tinyint(1) unsigned NOT NULL default '1',
  ct_lock tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (ct_id),
  KEY ct_code (ct_code)
) TYPE=MyISAM;

CREATE TABLE sed_forum_posts (
  fp_id mediumint(8) unsigned NOT NULL auto_increment,
  fp_topicid mediumint(8) NOT NULL default '0',
  fp_sectionid smallint(5) NOT NULL default '0',
  fp_posterid int(11) NOT NULL default '-1',
  fp_postername varchar(24) NOT NULL default '',
  fp_creation int(11) NOT NULL default '0',
  fp_updated int(11) NOT NULL default '0',
  fp_updater varchar(24) NOT NULL default '0',
  fp_text text NOT NULL,
  fp_posterip varchar(15) NOT NULL default '',
  fp_html TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (fp_id),
  UNIQUE KEY fp_topicid (fp_topicid,fp_id),
  KEY fp_updated (fp_creation),
  KEY fp_posterid (fp_posterid),
  KEY fp_sectionid (fp_sectionid)
) TYPE=MyISAM;


CREATE TABLE sed_forum_sections (
  fs_id smallint(5) unsigned NOT NULL auto_increment,
  fs_state tinyint(1) unsigned NOT NULL default '0',
  fs_order smallint(5) unsigned NOT NULL default '0',
  fs_title varchar(128) NOT NULL default '',
  fs_category varchar(64) NOT NULL default '',
  fs_desc varchar(255) NOT NULL default '',
  fs_icon varchar(255) NOT NULL default '',
  fs_lt_id int(11) NOT NULL default '0',
  fs_lt_title varchar(64) NOT NULL default '',
  fs_lt_date int(11) NOT NULL default '0',
  fs_lt_posterid int(11) NOT NULL default '-1',
  fs_lt_postername varchar(24) NOT NULL default '',
  fs_autoprune int(11) NOT NULL default '0',
  fs_allowusertext tinyint(1) NOT NULL default '1',
  fs_allowbbcodes tinyint(1) NOT NULL default '1',
  fs_allowsmilies tinyint(1) NOT NULL default '1',
  fs_allowprvtopics tinyint(1) NOT NULL default '0',
  fs_countposts tinyint(1) NOT NULL default '1',
  fs_topiccount mediumint(8) NOT NULL default '0',
  fs_topiccount_pruned int(11) default '0',
  fs_postcount mediumint(8) NOT NULL default '0',
  fs_postcount_pruned int(11) default '0',
  fs_viewcount mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (fs_id),
  KEY fs_order (fs_order)
) TYPE=MyISAM;

CREATE TABLE sed_forum_structure (
  fn_id mediumint(8) NOT NULL auto_increment,
  fn_path varchar(16) NOT NULL default '',
  fn_code varchar(16) NOT NULL default '',
  fn_tpl varchar(64) NOT NULL default '',
  fn_title varchar(32) NOT NULL default '',
  fn_desc varchar(255) NOT NULL default '',
  fn_icon varchar(128) NOT NULL default '',
  fn_defstate tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (fn_id)
) TYPE=MyISAM;

CREATE TABLE sed_forum_topics (
  ft_id mediumint(8) unsigned NOT NULL auto_increment,
  ft_mode tinyint(1) unsigned NOT NULL default '0',
  ft_state tinyint(1) unsigned NOT NULL default '0',
  ft_sticky tinyint(1) unsigned NOT NULL default '0',
  ft_tag varchar(16) NOT NULL default '',
  ft_sectionid mediumint(8) NOT NULL default '0',
  ft_title varchar(64) NOT NULL default '',
  ft_desc varchar(64) NOT NULL default '',
  ft_creationdate int(11) NOT NULL default '0',
  ft_updated int(11) NOT NULL default '0',
  ft_postcount mediumint(8) NOT NULL default '0',
  ft_viewcount mediumint(8) NOT NULL default '0',
  ft_lastposterid int(11) NOT NULL default '-1',
  ft_lastpostername varchar(24) NOT NULL default '',
  ft_firstposterid int(11) NOT NULL default '-1',
  ft_firstpostername varchar(24) NOT NULL default '',
  ft_poll int(11) default '0',
  ft_movedto int(11) default '0',
  PRIMARY KEY  (ft_id),
  KEY ft_updated (ft_updated),
  KEY ft_mode (ft_mode),
  KEY ft_state (ft_state),
  KEY ft_sticky (ft_sticky),
  KEY ft_sectionid (ft_sectionid),
  KEY ft_movedto (ft_movedto)
) TYPE=MyISAM;


CREATE TABLE sed_groups (
  grp_id int(11) NOT NULL auto_increment,
  grp_alias varchar(24) NOT NULL default '',
  grp_level tinyint(2) NOT NULL default '1',
  grp_disabled tinyint(1) NOT NULL default '0',
  grp_hidden tinyint(1) NOT NULL default '0',
  grp_title varchar(64) NOT NULL default '',
  grp_desc varchar(255) NOT NULL default '',
  grp_icon varchar(128) NOT NULL default '',
  grp_pfs_maxfile int(11) NOT NULL default '0',
  grp_pfs_maxtotal int(11) NOT NULL default '0',
  grp_ownerid int(11) NOT NULL default '0',
  PRIMARY KEY  (grp_id)
) TYPE=MyISAM;

CREATE TABLE sed_groups_users (
  gru_userid int(11) NOT NULL default '0',
  gru_groupid int(11) NOT NULL default '0',
  gru_state tinyint(1) NOT NULL default '0',
  gru_extra1 varchar(255) NOT NULL default '',
  gru_extra2 varchar(255) NOT NULL default '',
  KEY gru_userid (gru_userid),
  UNIQUE KEY gru_groupid (gru_groupid,gru_userid)
) TYPE=MyISAM;

CREATE TABLE sed_logger (
  log_id mediumint(11) NOT NULL auto_increment,
  log_date int(11) NOT NULL default '0',
  log_ip varchar(15) NOT NULL default '',
  log_name varchar(24) NOT NULL default '',
  log_group varchar(4) NOT NULL default 'def',
  log_text varchar(255) NOT NULL default '',
  PRIMARY KEY  (log_id)
) TYPE=MyISAM;

CREATE TABLE sed_online (
  online_id int(11) NOT NULL auto_increment,
  online_ip varchar(15) NOT NULL default '',
  online_name varchar(24) NOT NULL default '',
  online_lastseen int(11) NOT NULL default '0',
  online_location varchar(32) NOT NULL default '',
  online_subloc varchar(255) NOT NULL default '',
  online_userid int(11) NOT NULL default '0',
  online_shield int(11) NOT NULL default '0',
  online_action varchar(32) NOT NULL default '',
  online_hammer tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (online_id),
  KEY online_lastseen (online_lastseen),
  KEY online_userid (online_userid),
  KEY online_name (online_name)
) TYPE=MyISAM;

CREATE TABLE sed_pages (
  page_id int(11) unsigned NOT NULL auto_increment,
  page_state tinyint(1) unsigned NOT NULL default '0',
  page_type tinyint(1) default '0',
  page_cat varchar(255) default NULL,
  page_key varchar(16) default NULL,
  page_extra1 varchar(255) NOT NULL default '',
  page_extra2 varchar(255) NOT NULL default '',
  page_extra3 varchar(255) NOT NULL default '',
  page_extra4 varchar(255) NOT NULL default '',
  page_extra5 varchar(255) NOT NULL default '',
  page_title varchar(255) default NULL,
  page_desc varchar(255) default NULL,
  page_text text,
  page_author varchar(24) default NULL,
  page_ownerid int(11) NOT NULL default '0',
  page_date int(11) NOT NULL default '0',
  page_begin int(11) NOT NULL default '0',
  page_expire int(11) NOT NULL default '0',
  page_file tinyint(1) default NULL,
  page_url varchar(255) default NULL,
  page_size varchar(16) default NULL,
  page_count mediumint(8) unsigned default '0',
  page_rating decimal(5,2) NOT NULL default '0.00',
  page_comcount mediumint(8) unsigned default '0',
  page_filecount mediumint(8) unsigned default '0',
  page_alias varchar(255) NOT NULL default '',
  page_html TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (page_id),
  KEY page_cat (page_cat),
  KEY page_alias (page_alias),
  KEY page_state (page_state),
  KEY page_date (page_date)
) TYPE=MyISAM;

CREATE TABLE sed_pfs (
  pfs_id int(11) NOT NULL auto_increment,
  pfs_userid int(11) NOT NULL default '0',
  pfs_date int(11) NOT NULL default '0',
  pfs_file varchar(255) NOT NULL default '',
  pfs_extension varchar(8) NOT NULL default '',
  pfs_folderid int(11) NOT NULL default '0',
  pfs_desc varchar(255) NOT NULL default '',
  pfs_size int(11) NOT NULL default '0',
  pfs_count int(11) NOT NULL default '0',
  PRIMARY KEY  (pfs_id),
  KEY pfs_userid (pfs_userid)
) TYPE=MyISAM;

CREATE TABLE sed_pfs_folders (
  pff_id int(11) NOT NULL auto_increment,
  pff_userid int(11) NOT NULL default '0',
  pff_date int(11) NOT NULL default '0',
  pff_updated int(11) NOT NULL default '0',
  pff_title varchar(64) NOT NULL default '',
  pff_desc varchar(255) NOT NULL default '',
  pff_ispublic tinyint(1) NOT NULL default '0',
  pff_isgallery tinyint(1) NOT NULL default '0',
  pff_count int(11) NOT NULL default '0',
  PRIMARY KEY  (pff_id),
  KEY pff_userid (pff_userid)
) TYPE=MyISAM;

CREATE TABLE sed_plugins (
  pl_id mediumint(8) NOT NULL auto_increment,
  pl_hook varchar(64) NOT NULL default '',
  pl_code varchar(24) NOT NULL default '',
  pl_part varchar(24) NOT NULL default '',
  pl_title varchar(255) NOT NULL default '',
  pl_file varchar(255) NOT NULL default '',
  pl_order tinyint(2) unsigned NOT NULL default '10',
  pl_active tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY (pl_id)
) TYPE=MyISAM;

CREATE TABLE sed_pm (
  pm_id int(11) unsigned NOT NULL auto_increment,
  pm_state tinyint(2) NOT NULL default '0',
  pm_date int(11) NOT NULL default '0',
  pm_fromuserid int(11) NOT NULL default '0',
  pm_fromuser varchar(24) NOT NULL default '0',
  pm_touserid int(11) NOT NULL default '0',
  pm_title varchar(64) NOT NULL default '0',
  pm_text text NOT NULL,
  pm_html TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (pm_id),
  KEY pm_fromuserid (pm_fromuserid),
  KEY pm_touserid (pm_touserid)
) TYPE=MyISAM;

CREATE TABLE sed_polls (
  poll_id mediumint(8) NOT NULL auto_increment,
  poll_type tinyint(1) default '0',
  poll_state tinyint(1) NOT NULL default '0',
  poll_creationdate int(11) NOT NULL default '0',
  poll_text varchar(255) NOT NULL default '',
  PRIMARY KEY  (poll_id),
  KEY poll_creationdate (poll_creationdate)
) TYPE=MyISAM;

CREATE TABLE sed_polls_options (
  po_id mediumint(8) unsigned NOT NULL auto_increment,
  po_pollid mediumint(8) unsigned NOT NULL default '0',
  po_text varchar(128) NOT NULL default '',
  po_count mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (po_id),
  KEY po_pollid (po_pollid)
) TYPE=MyISAM;

CREATE TABLE sed_polls_voters (
  pv_id mediumint(8) unsigned NOT NULL auto_increment,
  pv_pollid mediumint(8) NOT NULL default '0',
  pv_userid mediumint(8) NOT NULL default '0',
  pv_userip varchar(15) NOT NULL default '',
  PRIMARY KEY  (pv_id),
  KEY pv_pollid (pv_pollid)
) TYPE=MyISAM;

CREATE TABLE sed_rated (
  rated_id int(11) unsigned NOT NULL auto_increment,
  rated_code varchar(16) default NULL,
  rated_userid int(11) default NULL,
  rated_value tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (rated_id),
  KEY rated_code (rated_code)
) TYPE=MyISAM;

CREATE TABLE sed_ratings (
  rating_id int(11) NOT NULL auto_increment,
  rating_code varchar(16) NOT NULL default '',
  rating_state tinyint(2) NOT NULL default '0',
  rating_average decimal(5,2) NOT NULL default '0.00',
  rating_creationdate int(11) NOT NULL default '0',
  rating_text varchar(128) NOT NULL default '',
  PRIMARY KEY  (rating_id),
  KEY rating_code (rating_code)
) TYPE=MyISAM;

CREATE TABLE sed_referers (
  ref_url varchar(255) NOT NULL default '',
  ref_date int(11) unsigned NOT NULL default '0',
  ref_count int(11) NOT NULL default '0',
  PRIMARY KEY  (ref_url)
) TYPE=MyISAM;

CREATE TABLE sed_smilies (
  smilie_id int(11) NOT NULL auto_increment,
  smilie_code varchar(16) NOT NULL default '',
  smilie_image varchar(128) NOT NULL default '',
  smilie_text varchar(32) NOT NULL default '',
  smilie_order smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (smilie_id)
) TYPE=MyISAM;

CREATE TABLE sed_stats (
  stat_name varchar(32) NOT NULL default '',
  stat_value int(11) NOT NULL default '0',
  PRIMARY KEY  (stat_name)
) TYPE=MyISAM;

CREATE TABLE sed_structure (
  structure_id mediumint(8) NOT NULL auto_increment,
  structure_code varchar(255) NOT NULL default '',
  structure_path varchar(16) NOT NULL default '',
  structure_tpl varchar(64) NOT NULL default '',
  structure_title varchar(32) NOT NULL default '',
  structure_desc varchar(255) NOT NULL default '',
  structure_icon varchar(128) NOT NULL default '',
  structure_group tinyint(1) NOT NULL default '0',
  structure_order varchar(16) NOT NULL default 'title.asc',
  PRIMARY KEY  (structure_id),
  KEY structure_path (structure_path)
) TYPE=MyISAM;

CREATE TABLE sed_trash (
  tr_id int(11) NOT NULL auto_increment,
  tr_date int(11) unsigned NOT NULL default '0',
  tr_type varchar(24) NOT NULL default '',
  tr_title varchar(128) NOT NULL default '',
  tr_itemid varchar(24) NOT NULL default '',
  tr_trashedby int(11) NOT NULL default '0',
  tr_datas mediumblob,
  PRIMARY KEY  (tr_id)
) TYPE=MyISAM;

CREATE TABLE sed_users (
  user_id int(11) unsigned NOT NULL auto_increment,
  user_banexpire int(11) default '0',
  user_name varchar(24) NOT NULL default '',
  user_password varchar(32) NOT NULL default '',
  user_maingrp int(11) unsigned NOT NULL default '4',
  user_country char(2) NOT NULL default '',
  user_text text NOT NULL,
  user_html TEXT NOT NULL DEFAULT '',
  user_avatar varchar(255) NOT NULL default '',
  user_photo varchar(255) NOT NULL default '',
  user_signature varchar(255) NOT NULL default '',
  user_extra1 varchar(255) NOT NULL default '',
  user_extra2 varchar(255) NOT NULL default '',
  user_extra3 varchar(255) NOT NULL default '',
  user_extra4 varchar(255) NOT NULL default '',
  user_extra5 varchar(255) NOT NULL default '',
  user_extra6 text,
  user_extra7 text,
  user_extra8 text,
  user_extra9 text,
  user_occupation varchar(64) NOT NULL default '',
  user_location varchar(64) NOT NULL default '',
  user_timezone decimal(2,0) NOT NULL default '0',
  user_birthdate int(11) NOT NULL default '0',
  user_gender char(1) NOT NULL default 'U',
  user_irc varchar(128) NOT NULL default '',
  user_msn varchar(64) NOT NULL default '',
  user_icq varchar(16) NOT NULL default '',
  user_website varchar(128) NOT NULL default '',
  user_email varchar(64) NOT NULL default '',
  user_hideemail tinyint(1) unsigned NOT NULL default '1',
  user_pmnotify tinyint(1) unsigned NOT NULL default '0',
  user_newpm tinyint(1) unsigned NOT NULL default '0',
  user_skin varchar(16) NOT NULL default '',
  user_lang varchar(16) NOT NULL default '',
  user_regdate int(11) NOT NULL default '0',
  user_lastlog int(11) NOT NULL default '0',
  user_lastvisit int(11) NOT NULL default '0',
  user_lastip varchar(16) NOT NULL default '',
  user_logcount int(11) unsigned NOT NULL default '0',
  user_postcount int(11) default '0',
  user_gallerycount mediumint(8) NOT NULL default '0',
  user_jrnpagescount mediumint(8) NOT NULL default '0',
  user_jrnupdated int(11) NOT NULL default '0',
  user_sid char(32) NOT NULL default '',
  user_lostpass char(32) NOT NULL default '',
  user_auth text,
  PRIMARY KEY (user_id),
  KEY user_password (user_password),
  KEY user_regdate (user_regdate)
) TYPE=MyISAM;

INSERT INTO sed_structure VALUES (1, 'articles', '1', '', 'Articles', '', '', 0 ,'title.asc');
INSERT INTO sed_structure VALUES (2, 'links', '2', '', 'Links', '', '',  0 ,'title.asc');
INSERT INTO sed_structure VALUES (3, 'events', '3', '', 'Events', '', '',  0 ,'date.asc');
INSERT INTO sed_structure VALUES (4, 'news', '4', '', 'News', '', '', 0 ,'date.desc');

INSERT INTO sed_forum_sections VALUES ('1', '0', '100', 'General discussion', 'pub', 'General chat.', 'system/img/admin/forums.gif', 0, '', 0, 0, '', 365, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0);
INSERT INTO sed_forum_sections VALUES ('2', '0', '101', 'Off-topic', 'pub', 'Various and off-topic.', 'system/img/admin/forums.gif', 0, '', 0, 0, '', 365, 1, 1, 1, 0, 1, 0, 0, 0, 0, 0);
INSERT INTO sed_forum_structure VALUES ('1', '1', 'pub', '', 'Public', '', '', 1);

INSERT INTO sed_smilies VALUES (1, ':D', 'system/smilies/icon_biggrin.gif', 'Mister grin', 5);
INSERT INTO sed_smilies VALUES (2, ':blush', 'system/smilies/icon_blush.gif', 'Blush', 45);
INSERT INTO sed_smilies VALUES (3, ':con', 'system/smilies/icon_confused.gif', 'Confused', 42);
INSERT INTO sed_smilies VALUES (4, ':)', 'system/smilies/icon_smile.gif', 'Smile', 1);
INSERT INTO sed_smilies VALUES (5, ':cry', 'system/smilies/icon_cry.gif', 'Cry', 44);
INSERT INTO sed_smilies VALUES (6, ':dontgetit', 'system/smilies/icon_dontgetit.gif', 'Dont get it', 41);
INSERT INTO sed_smilies VALUES (7, ':dozingoff', 'system/smilies/icon_dozingoff.gif', 'Dozing off', 40);
INSERT INTO sed_smilies VALUES (8, ':love', 'system/smilies/icon_love.gif', 'Love', 10);
INSERT INTO sed_smilies VALUES (9, ':((', 'system/smilies/icon_mad.gif', 'Mad', 50);
INSERT INTO sed_smilies VALUES (10, ':|', 'system/smilies/icon_neutral.gif', 'Neutral', 43);
INSERT INTO sed_smilies VALUES (11, ':no', 'system/smilies/icon_no.gif', 'No', 12);
INSERT INTO sed_smilies VALUES (12, ':O_o', 'system/smilies/icon_o_o.gif', 'Suspicious', 7);
INSERT INTO sed_smilies VALUES (13, ':p', 'system/smilies/icon_razz.gif', 'Razz', 6);
INSERT INTO sed_smilies VALUES (14, ':(', 'system/smilies/icon_sad.gif', 'Sad', 46);
INSERT INTO sed_smilies VALUES (15, ':satisfied', 'system/smilies/icon_satisfied.gif', 'Satisfied', 2);
INSERT INTO sed_smilies VALUES (16, '8)', 'system/smilies/icon_cool.gif', 'Cool', 4);
INSERT INTO sed_smilies VALUES (17, ':wink', 'system/smilies/icon_wink.gif', 'Wink', 3);
INSERT INTO sed_smilies VALUES (18, ':yes', 'system/smilies/icon_yes.gif', 'Yes', 11);

INSERT INTO sed_stats (stat_name, stat_value) VALUES ('totalpages', '0');
INSERT INTO sed_stats (stat_name, stat_value) VALUES ('totalmailsent', '0');
INSERT INTO sed_stats (stat_name, stat_value) VALUES ('totalmailpmnot', '0');
INSERT INTO sed_stats (stat_name, stat_value) VALUES ('totalpms', '0');
INSERT INTO sed_stats (stat_name, stat_value) VALUES ('totalantihammer', '0');
INSERT INTO sed_stats (stat_name, stat_value) VALUES ('textboxerprev', '0');
INSERT INTO sed_stats (stat_name, stat_value) VALUES ('version', '120');

INSERT INTO sed_core VALUES (1, 'admin', 'Administration panel', '100', 1, 1);
INSERT INTO sed_core VALUES (2, 'comments', 'Comments', '100', 1, 0);
INSERT INTO sed_core VALUES (3, 'forums', 'Forums', '100', 1, 0);
INSERT INTO sed_core VALUES (4, 'index', 'Home page', '100', 1, 1);
INSERT INTO sed_core VALUES (5, 'message', 'Messages', '100', 1, 1);
INSERT INTO sed_core VALUES (6, 'page', 'Pages', '100', 1, 0);
INSERT INTO sed_core VALUES (7, 'pfs', 'Personal File Space', '100', 1, 0);
INSERT INTO sed_core VALUES (8, 'plug', 'Plugins', '100', 1, 0);
INSERT INTO sed_core VALUES (9, 'pm', 'Private messages', '100', 1, 0);
INSERT INTO sed_core VALUES (10, 'polls', 'Polls', '100', 1, 0);
INSERT INTO sed_core VALUES (11, 'ratings', 'Ratings', '100', 1, 0);
INSERT INTO sed_core VALUES (12, 'users', 'Users', '100', 1, 1);
INSERT INTO sed_core VALUES (13, 'trash', 'Trash Can', '110', 1, 1);

INSERT INTO sed_groups VALUES (1, 'guests', 0, 0, 0, 'Guests', '', '', 0, 0, 1);
INSERT INTO sed_groups VALUES (2, 'inactive', 1, 0, 0, 'Inactive', '', '', 0, 0, 1);
INSERT INTO sed_groups VALUES (3, 'banned', 1, 0, 0, 'Banned', '', '', 0, 0, 1);
INSERT INTO sed_groups VALUES (4, 'members', 1, 0, 0, 'Members', '', '', 0, 0, 1);
INSERT INTO sed_groups VALUES (5, 'administrators', 99, 0, 0, 'Administrators', '', '', 256, 1024, 1);
INSERT INTO sed_groups VALUES (6, 'moderators', 50, 0, 0, 'Moderators', '', '', 256, 1024, 1);

# ---------- Default configuration

INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '01', 'maintitle', 1, 'Title of your site');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '02', 'subtitle', 1, 'Subtitle');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '03', 'mainurl', 1, 'http://www.yourdomain.com');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '04', 'adminemail', 1, 'admin@mysite.com');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '05', 'clustermode', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '05', 'hostip', 1, '999.999.999.999');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '06', 'cache', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '06', 'gzip', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '07', 'devmode', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '10', 'cookiedomain', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '10', 'cookiepath', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '10', 'cookielifetime', 2, '5184000');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '12', 'disablehitstats', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '20', 'shieldenabled', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '20', 'shieldtadjust', 2, '100');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'main', '20', 'shieldzhammer', 2, '25');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '10', 'parser_vid', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '20', 'parsebbcodeusertext', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '20', 'parsebbcodecom', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '20', 'parsebbcodeforums', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '20', 'parsebbcodepages', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '30', 'parsesmiliesusertext', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '30', 'parsesmiliescom', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '30', 'parsesmiliesforums', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'parser', '30', 'parsesmiliespages', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'time', '11', 'dateformat', 1, 'Y-m-d H:i');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'time', '11', 'formatmonthday', 1, 'm-d');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'time', '11', 'formatyearmonthday', 1, 'Y-m-d');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'time', '11', 'formatmonthdayhourmin', 1, 'm-d H:i');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'time', '11', 'servertimezone', 1, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'time', '12', 'defaulttimezone', 1, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'time', '14', 'timedout', 2, '1200');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'skin', '02', 'forcedefaultskin', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'skin', '04', 'doctypeid', 4, '4');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'skin', '06', 'charset', 4, 'ISO-8859-1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'skin', '08', 'metakeywords', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'skin', '08', 'separator', 1, '/');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'skin', '15', 'disablesysinfos', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'skin', '15', 'keepcrbottom', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'skin', '15', 'showsqlstats', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'lang', '10', 'forcedefaultlang', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '10', 'topline', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '10', 'banner', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '10', 'bottomline', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '15', 'menu1', 0, '<ul>\n<li><a href="index.php">Home</a></li>\n<li><a href="forums.php">Forums</a></li>\n<li><a href="list.php?c=articles">Articles</a></li>\n<li><a href="plug.php?e=search">Search</a></li>\n</ul>');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '15', 'menu2', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '15', 'menu3', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '15', 'menu4', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '15', 'menu5', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '15', 'menu6', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '15', 'menu7', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '15', 'menu8', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '15', 'menu9', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '20', 'freetext1', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '20', 'freetext2', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '20', 'freetext3', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '20', 'freetext4', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '20', 'freetext5', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '20', 'freetext6', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '20', 'freetext7', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '20', 'freetext8', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'menus', '20', 'freetext9', 0, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'comments', '01', 'disable_comments', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'comments', '10', 'countcomments', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'forums', '01', 'disable_forums', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'forums', '10', 'hideprivateforums', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'forums', '10', 'hottopictrigger', 2, '20');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'forums', '10', 'maxtopicsperpage', 2, '30');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'forums', '12', 'antibumpforums', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'page', '01', 'disable_page', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'page', '02', 'allowphp_pages', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'page', '05', 'maxrowsperpage', 2, '15');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '01', 'disable_pfs', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '02', 'pfsuserfolder', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '10', 'th_amode', 2, 'GD2');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '10', 'th_x', 2, '112');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '10', 'th_y', 2, '84');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '10', 'th_border', 2, '4');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '10', 'th_dimpriority', 2, 'Width');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '10', 'th_keepratio', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '10', 'th_jpeg_quality', 2, '85');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '10', 'th_colorbg', 2, '000000');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '10', 'th_colortext', 2, 'FFFFFF');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pfs', '10', 'th_textsize', 2, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'plug', '01', 'disable_plug', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pm', '01', 'disable_pm', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pm', '10', 'pm_maxsize', 2, '10000');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'pm', '10', 'pm_allownotifications', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'polls', '01', 'disable_polls', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'ratings', '01', 'disable_ratings', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'trash', '01', 'trash_prunedelay', 2, '7');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'trash', '10', 'trash_comment', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'trash', '11', 'trash_forum', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'trash', '12', 'trash_page', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'trash', '13', 'trash_pm', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'trash', '14', 'trash_user', 3, '1');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '01', 'disablereg', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '03', 'disablewhosonline', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '05', 'maxusersperpage', 2, '50');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '07', 'regrequireadmin', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '10', 'regnoactivation', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '10', 'useremailchange', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '10', 'usertextimg', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '12', 'av_maxsize', 2, '8000');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '12', 'av_maxx', 2, '64');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '12', 'av_maxy', 2, '64');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '12', 'usertextmax', 2, '300');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '13', 'sig_maxsize', 2, '32000');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '13', 'sig_maxx', 2, '550');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '13', 'sig_maxy', 2, '100');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '14', 'ph_maxsize', 2, '32000');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '14', 'ph_maxx', 2, '128');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '14', 'ph_maxy', 2, '128');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra1title', 1, 'Real name');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra2title', 1, 'Title');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra3title', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra4title', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra5title', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra6title', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra7title', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra8title', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra9title', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra1tsetting', 2, '255');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra2tsetting', 2, '255');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra3tsetting', 2, '255');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra4tsetting', 2, '255');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra5tsetting', 2, '255');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra6tsetting', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra7tsetting', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra8tsetting', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra9tsetting', 1, '');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra1uchange', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra2uchange', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra3uchange', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra4uchange', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra5uchange', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra6uchange', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra7uchange', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra8uchange', 3, '0');
INSERT INTO sed_config (config_owner, config_cat, config_order, config_name, config_type, config_value) VALUES ('core', 'users', '20', 'extra9uchange', 3, '0');


# ---------- Rights for the core :

INSERT INTO sed_auth VALUES (1, 1, 'admin', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (2, 2, 'admin', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (3, 3, 'admin', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (4, 4, 'admin', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (5, 5, 'admin', 'a', 255, 255, 1);
INSERT INTO sed_auth VALUES (6, 1, 'comments', 'a', 1, 254, 1);
INSERT INTO sed_auth VALUES (7, 2, 'comments', 'a', 1, 254, 1);
INSERT INTO sed_auth VALUES (8, 3, 'comments', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (9, 4, 'comments', 'a', 3, 128, 1);
INSERT INTO sed_auth VALUES (10, 5, 'comments', 'a', 255, 255, 1);
INSERT INTO sed_auth VALUES (11, 1, 'index', 'a', 1, 254, 1);
INSERT INTO sed_auth VALUES (12, 2, 'index', 'a', 1, 254, 1);
INSERT INTO sed_auth VALUES (13, 3, 'index', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (14, 4, 'index', 'a', 1, 128, 1);
INSERT INTO sed_auth VALUES (15, 5, 'index', 'a', 255, 255, 1);
INSERT INTO sed_auth VALUES (16, 1, 'message', 'a', 1, 255, 1);
INSERT INTO sed_auth VALUES (17, 2, 'message', 'a', 1, 255, 1);
INSERT INTO sed_auth VALUES (18, 3, 'message', 'a', 1, 255, 1);
INSERT INTO sed_auth VALUES (19, 4, 'message', 'a', 1, 255, 1);
INSERT INTO sed_auth VALUES (20, 5, 'message', 'a', 255, 255, 1);
INSERT INTO sed_auth VALUES (21, 1, 'pfs', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (22, 2, 'pfs', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (23, 3, 'pfs', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (24, 4, 'pfs', 'a', 3, 128, 1);
INSERT INTO sed_auth VALUES (25, 5, 'pfs', 'a', 255, 255, 1);
INSERT INTO sed_auth VALUES (26, 1, 'pm', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (27, 2, 'pm', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (28, 3, 'pm', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (29, 4, 'pm', 'a', 3, 128, 1);
INSERT INTO sed_auth VALUES (30, 5, 'pm', 'a', 255, 255, 1);
INSERT INTO sed_auth VALUES (31, 1, 'polls', 'a', 1, 254, 1);
INSERT INTO sed_auth VALUES (32, 2, 'polls', 'a', 1, 254, 1);
INSERT INTO sed_auth VALUES (33, 3, 'polls', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (34, 4, 'polls', 'a', 3, 128, 1);
INSERT INTO sed_auth VALUES (35, 5, 'polls', 'a', 255, 255, 1);
INSERT INTO sed_auth VALUES (36, 1, 'ratings', 'a', 1, 254, 1);
INSERT INTO sed_auth VALUES (37, 2, 'ratings', 'a', 1, 254, 1);
INSERT INTO sed_auth VALUES (38, 3, 'ratings', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (39, 4, 'ratings', 'a', 3, 128, 1);
INSERT INTO sed_auth VALUES (40, 5, 'ratings', 'a', 255, 255, 1);
INSERT INTO sed_auth VALUES (41, 1, 'users', 'a', 0, 254, 1);
INSERT INTO sed_auth VALUES (42, 2, 'users', 'a', 0, 254, 1);
INSERT INTO sed_auth VALUES (43, 3, 'users', 'a', 0, 255, 1);
INSERT INTO sed_auth VALUES (44, 4, 'users', 'a', 3, 128, 1);
INSERT INTO sed_auth VALUES (45, 5, 'users', 'a', 255, 255, 1);

INSERT INTO sed_auth VALUES (46, 1, 'forums', '1', 1, 254, 1);
INSERT INTO sed_auth VALUES (47, 2, 'forums', '1', 1, 254, 1);
INSERT INTO sed_auth VALUES (48, 3, 'forums', '1', 0, 255, 1);
INSERT INTO sed_auth VALUES (49, 4, 'forums', '1', 3, 128, 1);
INSERT INTO sed_auth VALUES (50, 5, 'forums', '1', 255, 255, 1);
INSERT INTO sed_auth VALUES (51, 1, 'forums', '2', 1, 254, 1);
INSERT INTO sed_auth VALUES (52, 2, 'forums', '2', 1, 254, 1);
INSERT INTO sed_auth VALUES (53, 3, 'forums', '2', 0, 255, 1);
INSERT INTO sed_auth VALUES (54, 4, 'forums', '2', 3, 128, 1);
INSERT INTO sed_auth VALUES (55, 5, 'forums', '2', 255, 255, 1);

INSERT INTO sed_auth VALUES (56, 1, 'page', 'articles', 1, 254, 1);
INSERT INTO sed_auth VALUES (57, 2, 'page', 'articles', 1, 254, 1);
INSERT INTO sed_auth VALUES (58, 3, 'page', 'articles', 0, 255, 1);
INSERT INTO sed_auth VALUES (59, 4, 'page', 'articles', 3, 128, 1);
INSERT INTO sed_auth VALUES (60, 5, 'page', 'articles', 255, 255, 1);
INSERT INTO sed_auth VALUES (61, 1, 'page', 'events', 1, 254, 1);
INSERT INTO sed_auth VALUES (62, 2, 'page', 'events', 1, 254, 1);
INSERT INTO sed_auth VALUES (63, 3, 'page', 'events', 0, 255, 1);
INSERT INTO sed_auth VALUES (64, 4, 'page', 'events', 3, 252, 1);
INSERT INTO sed_auth VALUES (65, 5, 'page', 'events', 255, 255, 1);
INSERT INTO sed_auth VALUES (66, 1, 'page', 'links', 1, 254, 1);
INSERT INTO sed_auth VALUES (67, 2, 'page', 'links', 1, 254, 1);
INSERT INTO sed_auth VALUES (68, 3, 'page', 'links', 0, 255, 1);
INSERT INTO sed_auth VALUES (69, 4, 'page', 'links', 3, 128, 1);
INSERT INTO sed_auth VALUES (70, 5, 'page', 'links', 255, 255, 1);
INSERT INTO sed_auth VALUES (71, 1, 'page', 'news', 1, 254, 1);
INSERT INTO sed_auth VALUES (72, 2, 'page', 'news', 1, 254, 1);
INSERT INTO sed_auth VALUES (73, 3, 'page', 'news', 0, 255, 1);
INSERT INTO sed_auth VALUES (74, 4, 'page', 'news', 3, 252, 1);
INSERT INTO sed_auth VALUES (75, 5, 'page', 'news', 255, 255, 1);

INSERT INTO sed_auth VALUES (76, 1, 'plug', 'adminqv', 0, 255, 1);
INSERT INTO sed_auth VALUES (77, 2, 'plug', 'adminqv', 0, 255, 1);
INSERT INTO sed_auth VALUES (78, 3, 'plug', 'adminqv', 0, 255, 1);
INSERT INTO sed_auth VALUES (79, 4, 'plug', 'adminqv', 1, 254, 1);
INSERT INTO sed_auth VALUES (80, 5, 'plug', 'adminqv', 255, 255, 1);
INSERT INTO sed_auth VALUES (81, 1, 'plug', 'cleaner', 0, 255, 1);
INSERT INTO sed_auth VALUES (82, 2, 'plug', 'cleaner', 0, 255, 1);
INSERT INTO sed_auth VALUES (83, 3, 'plug', 'cleaner', 0, 255, 1);
INSERT INTO sed_auth VALUES (84, 4, 'plug', 'cleaner', 1, 254, 1);
INSERT INTO sed_auth VALUES (85, 5, 'plug', 'cleaner', 255, 255, 1);
INSERT INTO sed_auth VALUES (86, 1, 'plug', 'forumstats', 1, 254, 1);
INSERT INTO sed_auth VALUES (87, 2, 'plug', 'forumstats', 1, 254, 1);
INSERT INTO sed_auth VALUES (88, 3, 'plug', 'forumstats', 0, 255, 1);
INSERT INTO sed_auth VALUES (89, 4, 'plug', 'forumstats', 1, 254, 1);
INSERT INTO sed_auth VALUES (90, 5, 'plug', 'forumstats', 255, 255, 1);
INSERT INTO sed_auth VALUES (91, 1, 'plug', 'massmovetopics', 0, 255, 1);
INSERT INTO sed_auth VALUES (92, 2, 'plug', 'massmovetopics', 0, 255, 1);
INSERT INTO sed_auth VALUES (93, 3, 'plug', 'massmovetopics', 0, 255, 1);
INSERT INTO sed_auth VALUES (94, 4, 'plug', 'massmovetopics', 0, 255, 1);
INSERT INTO sed_auth VALUES (95, 5, 'plug', 'massmovetopics', 255, 255, 1);
INSERT INTO sed_auth VALUES (96, 1, 'plug', 'passrecover', 1, 254, 1);
INSERT INTO sed_auth VALUES (97, 2, 'plug', 'passrecover', 1, 254, 1);
INSERT INTO sed_auth VALUES (98, 3, 'plug', 'passrecover', 0, 255, 1);
INSERT INTO sed_auth VALUES (99, 4, 'plug', 'passrecover', 1, 254, 1);
INSERT INTO sed_auth VALUES (100, 5, 'plug', 'passrecover', 255, 255, 1);
INSERT INTO sed_auth VALUES (101, 1, 'plug', 'search', 1, 254, 1);
INSERT INTO sed_auth VALUES (102, 2, 'plug', 'search', 1, 254, 1);
INSERT INTO sed_auth VALUES (103, 3, 'plug', 'search', 0, 255, 1);
INSERT INTO sed_auth VALUES (104, 4, 'plug', 'search', 1, 254, 1);
INSERT INTO sed_auth VALUES (105, 5, 'plug', 'search', 255, 255, 1);
INSERT INTO sed_auth VALUES (106, 1, 'plug', 'textboxer2', 0, 255, 1);
INSERT INTO sed_auth VALUES (107, 2, 'plug', 'textboxer2', 0, 255, 1);
INSERT INTO sed_auth VALUES (108, 3, 'plug', 'textboxer2', 0, 255, 1);
INSERT INTO sed_auth VALUES (109, 4, 'plug', 'textboxer2', 1, 254, 1);
INSERT INTO sed_auth VALUES (110, 5, 'plug', 'textboxer2', 255, 255, 1);
INSERT INTO sed_auth VALUES (111, 1, 'plug', 'whosonline', 1, 254, 1);
INSERT INTO sed_auth VALUES (112, 2, 'plug', 'whosonline', 1, 254, 1);
INSERT INTO sed_auth VALUES (113, 3, 'plug', 'whosonline', 0, 255, 1);
INSERT INTO sed_auth VALUES (114, 4, 'plug', 'whosonline', 1, 254, 1);
INSERT INTO sed_auth VALUES (115, 5, 'plug', 'whosonline', 255, 255, 1);


INSERT INTO sed_auth VALUES (116, 6, 'admin', 'a', 1, 0, 1);
INSERT INTO sed_auth VALUES (117, 6, 'comments', 'a', 131, 0, 1);
INSERT INTO sed_auth VALUES (118, 6, 'index', 'a', 131, 0, 1);
INSERT INTO sed_auth VALUES (119, 6, 'message', 'a', 131, 0, 1);
INSERT INTO sed_auth VALUES (120, 6, 'pfs', 'a', 131, 0, 1);
INSERT INTO sed_auth VALUES (121, 6, 'pm', 'a', 131, 0, 1);
INSERT INTO sed_auth VALUES (122, 6, 'polls', 'a', 131, 0, 1);
INSERT INTO sed_auth VALUES (123, 6, 'ratings', 'a', 131, 0, 1);
INSERT INTO sed_auth VALUES (124, 6, 'users', 'a', 3, 128, 1);
INSERT INTO sed_auth VALUES (125, 6, 'forums', '1', 131, 0, 1);
INSERT INTO sed_auth VALUES (126, 6, 'forums', '2', 131, 0, 1);
INSERT INTO sed_auth VALUES (127, 6, 'page', 'articles', 131, 0, 1);
INSERT INTO sed_auth VALUES (128, 6, 'page', 'events', 131, 0, 1);
INSERT INTO sed_auth VALUES (129, 6, 'page', 'links', 131, 0, 1);
INSERT INTO sed_auth VALUES (130, 6, 'page', 'news', 131, 0, 1);
INSERT INTO sed_auth VALUES (131, 6, 'plug', 'adminqv', 1, 0, 1);
INSERT INTO sed_auth VALUES (132, 6, 'plug', 'cleaner', 128, 255, 1);
INSERT INTO sed_auth VALUES (133, 6, 'plug', 'forumstats', 1, 254, 1);
INSERT INTO sed_auth VALUES (134, 6, 'plug', 'massmovetopics', 0, 255, 1);
INSERT INTO sed_auth VALUES (135, 6, 'plug', 'passrecover', 1, 254, 1);
INSERT INTO sed_auth VALUES (136, 6, 'plug', 'search', 1, 254, 1);
INSERT INTO sed_auth VALUES (137, 6, 'plug', 'textboxer2', 1, 254, 1);
INSERT INTO sed_auth VALUES (138, 6, 'plug', 'whosonline', 1, 254, 1);

# ---------- Default plugins :

INSERT INTO sed_plugins VALUES (1, 'admin.home', 'adminqv', 'main', 'Admin QuickView', 'adminqv', 10, 1);
INSERT INTO sed_plugins VALUES (2, 'admin.home', 'cleaner', 'main', 'Cleaner', 'cleaner', 10, 1);
INSERT INTO sed_plugins VALUES (3, 'standalone', 'forumstats', 'main', 'Forum statistics', 'forumstats', 10, 1);
INSERT INTO sed_plugins VALUES (4, 'tools', 'massmovetopics', 'admin', 'Mass-move topics in forums', 'massmovetopics.admin', 10, 1);
INSERT INTO sed_plugins VALUES (5, 'standalone', 'passrecover', 'main', 'Password recovery', 'passrecover', 10, 1);
INSERT INTO sed_plugins VALUES (6, 'standalone', 'search', 'main', 'Search', 'search', 10, 1);
INSERT INTO sed_plugins VALUES (7, 'standalone', 'statistics', 'main', 'Statistics', 'statistics', 10, 1);
INSERT INTO sed_plugins VALUES (8, 'comments.newcomment.tags', 'textboxer2', 'comments', 'Textboxer 2.0', 'tb2.comments', 10, 1);
INSERT INTO sed_plugins VALUES (9, 'forums.editpost.tags', 'textboxer2', 'forums.editpost', 'Textboxer 2.0', 'tb2.forums.editpost', 10, 1);
INSERT INTO sed_plugins VALUES (10, 'forums.newtopic.tags', 'textboxer2', 'forums.newtopic', 'Textboxer 2.0', 'tb2.forums.newtopic', 10, 1);
INSERT INTO sed_plugins VALUES (11, 'forums.posts.newpost.tags', 'textboxer2', 'forums.posts', 'Textboxer 2.0', 'tb2.forums.posts', 10, 1);
INSERT INTO sed_plugins VALUES (12, 'page.add.tags', 'textboxer2', 'page.add', 'Textboxer 2.0', 'tb2.page.add', 10, 1);
INSERT INTO sed_plugins VALUES (13, 'page.edit.tags', 'textboxer2', 'page.edit', 'Textboxer 2.0', 'tb2.page.edit', 10, 1);
INSERT INTO sed_plugins VALUES (14, 'pm.send.tags', 'textboxer2', 'pm.send', 'Textboxer 2.0', 'tb2.pm.send', 10, 1);
INSERT INTO sed_plugins VALUES (15, 'users.edit.tags', 'textboxer2', 'users.edit', 'Textboxer 2.0', 'tb2.users.edit', 10, 1);
INSERT INTO sed_plugins VALUES (16, 'profile.tags', 'textboxer2', 'profile', 'Textboxer 2.0', 'tb2.users.profile', 10, 1);
INSERT INTO sed_plugins VALUES (17, 'standalone', 'whosonline', 'main', 'Who''s online', 'whosonline', 10, 1);

INSERT INTO sed_config VALUES ('plug', 'textboxer2', '01', 'popup_smilies', 3, '0', '', 'Use popup for "More smilies.." instead of extending dropdown.');

# ---------- Plugins : News

INSERT INTO sed_auth VALUES (139, 1, 'plug', 'news', 1, 254, 1);
INSERT INTO sed_auth VALUES (140, 2, 'plug', 'news', 1, 254, 1);
INSERT INTO sed_auth VALUES (141, 3, 'plug', 'news', 0, 255, 1);
INSERT INTO sed_auth VALUES (142, 4, 'plug', 'news', 1, 254, 1);
INSERT INTO sed_auth VALUES (143, 5, 'plug', 'news', 255, 255, 1);
INSERT INTO sed_auth VALUES (144, 6, 'plug', 'news', 1, 254, 1);
INSERT INTO sed_plugins VALUES (18, 'index.tags', 'news', 'homepage', 'News', 'news', 10, 1);
INSERT INTO sed_config VALUES ('plug', 'news', '01', 'category', 1, 'news', '', 'Category code of the parent category');
INSERT INTO sed_config VALUES ('plug', 'news', '02', 'maxpages', 2, '10', '0,1,2,3,4,5,6,7,8,9,10,15,20,25,30,50,100', 'Recent pages displayed');

# ---------- Plugin : Recent items

INSERT INTO sed_auth VALUES (145, 1, 'plug', 'recentitems', 1, 254, 1);
INSERT INTO sed_auth VALUES (146, 2, 'plug', 'recentitems', 1, 254, 1);
INSERT INTO sed_auth VALUES (147, 3, 'plug', 'recentitems', 0, 255, 1);
INSERT INTO sed_auth VALUES (148, 4, 'plug', 'recentitems', 1, 254, 1);
INSERT INTO sed_auth VALUES (149, 5, 'plug', 'recentitems', 255, 255, 1);
INSERT INTO sed_auth VALUES (150, 6, 'plug', 'recentitems', 1, 254, 1);
INSERT INTO sed_plugins VALUES (19, 'index.tags', 'recentitems', 'main', 'Recent items', 'recentitems', 10, 1);
INSERT INTO sed_config VALUES ('plug', 'recentitems', 01, 'maxpages', 2, '5', '0,1,2,3,4,5,6,7,8,9,10,15,20,25,30', 'Recent pages displayed');
INSERT INTO sed_config VALUES ('plug', 'recentitems', 03, 'maxpolls', 2, '1', '0,1,2,3,4,5', 'Recent polls displayed');
INSERT INTO sed_config VALUES ('plug', 'recentitems', 04, 'maxtopics', 2, '5', '0,1,2,3,4,5,6,7,8,9,10,15,20,25,30', 'Recent topics in forums displayed');

# ---------- Plugin : Statistics

INSERT INTO sed_auth VALUES (151, 1, 'plug', 'statistics', 1, 254, 1);
INSERT INTO sed_auth VALUES (152, 2, 'plug', 'statistics', 1, 254, 1);
INSERT INTO sed_auth VALUES (153, 3, 'plug', 'statistics', 0, 255, 1);
INSERT INTO sed_auth VALUES (154, 4, 'plug', 'statistics', 1, 254, 1);
INSERT INTO sed_auth VALUES (155, 5, 'plug', 'statistics', 255, 255, 1);
INSERT INTO sed_auth VALUES (156, 6, 'plug', 'statistics', 1, 254, 1);

# ---------- Plugin : Cleaner

INSERT INTO sed_config VALUES ('plug', 'cleaner', 01, 'userprune', 2, '2', '0,1,2,3,4,5,6,7', 'Delete the user accounts not activated within * days (0 to disable).');
INSERT INTO sed_config VALUES ('plug', 'cleaner', 02, 'logprune', 2, '15', '0,1,2,3,7,15,30,60', 'Delete the log entries older than * days (0 to disable).');
INSERT INTO sed_config VALUES ('plug', 'cleaner', 03, 'refprune', 2, '30', '0,15,30,60,120,180,365', 'Delete the referer entries older than * days (0 to disable).');
INSERT INTO sed_config VALUES ('plug', 'cleaner', 04, 'pmnotread', 2, '120', '0,15,30,60,120,180,365', 'Delete the private messages older than * days and not read by the recipient (0 to disable).');
INSERT INTO sed_config VALUES ('plug', 'cleaner', 05, 'pmnotarchived', 2, '180', '0,15,30,60,120,180,365', 'Delete the private messages older than * days and not archived (0 to disable).');
INSERT INTO sed_config VALUES ('plug', 'cleaner', 06, 'pmold', 2, '365', '0,15,30,60,120,180,365', 'ALL the private messages older than * days (0 to disable).');

# ---------- Plugin : IPsearch

INSERT INTO sed_auth VALUES (157, 1, 'plug', 'ipsearch', 0, 255, 1);
INSERT INTO sed_auth VALUES (158, 2, 'plug', 'ipsearch', 0, 255, 1);
INSERT INTO sed_auth VALUES (159, 3, 'plug', 'ipsearch', 0, 255, 1);
INSERT INTO sed_auth VALUES (160, 4, 'plug', 'ipsearch', 0, 255, 1);
INSERT INTO sed_auth VALUES (161, 5, 'plug', 'ipsearch', 255, 255, 1);
INSERT INTO sed_auth VALUES (162, 6, 'plug', 'ipsearch', 0, 255, 1);
INSERT INTO sed_plugins VALUES (20, 'tools', 'ipsearch', 'admin', 'IP search', 'ipsearch.admin', 10, 1);

# ---------- Default news message

INSERT INTO `sed_pages` (`page_id`, `page_state`, `page_type`, `page_cat`, `page_key`, `page_extra1`, `page_extra2`, `page_extra3`, `page_extra4`, `page_extra5`, `page_title`, `page_desc`, `page_text`, `page_author`, `page_ownerid`, `page_date`, `page_begin`, `page_expire`, `page_file`, `page_url`, `page_size`, `page_count`, `page_rating`, `page_comcount`, `page_filecount`, `page_alias`) VALUES 
(1, 0, 0, 'news', '', '', '', '', '', '', 'Welcome !', '...', 'Congratulations, your website is up and running !\r\n\r\nNow create your account, go [url=users.php?m=auth]there[/url], then log-in with the link at the top.\r\n\r\nNote that the first user to register is automatically activated and assigned to the group "Administrators". Then, by default, all the next visitors will have to validate their account by clicking a link in an email sent by Seditio.\r\n\r\nNext step is to go in the [url=admin.php]Administration panel[/url], tab [url=admin.php?m=config]Configuration[/url], and there tweak the settings for the system.', '', 1, UNIX_TIMESTAMP()-23200, UNIX_TIMESTAMP()-23200, 1262343600, 0, '', '', 27, 0.00, 0, 0, '');
