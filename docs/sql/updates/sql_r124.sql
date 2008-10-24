/* r124 Subforum enhancements */
CREATE TABLE `sed_forum_subforums` (
  `fm_id` smallint(5) NOT NULL default '0',
  `fm_masterid` smallint(5) NOT NULL default '0',
  `fm_title` varchar(128) NOT NULL,
  `fm_lt_id` int(11) NOT NULL default '0',
  `fm_lt_title` varchar(64) NOT NULL default '',
  `fm_lt_date` int(11) NOT NULL default '0',
  `fm_lt_posterid` int(11) NOT NULL default '-1',
  `fm_lt_postername` varchar(24) NOT NULL default ''
) TYPE=MyISAM;