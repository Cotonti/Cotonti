CREATE TABLE `cot_logs` (
  `log_id` int(11) NOT NULL auto_increment,
  `log_date` int(11) NOT NULL,
  `log_ip` varchar(15) character set utf8 collate utf8_bin NOT NULL,
  `log_usrid` int(11) NOT NULL,
  `log_usrname` varchar(32) character set utf8 collate utf8_bin NOT NULL,
  `log_type` varchar(12) character set utf8 collate utf8_bin NOT NULL,
  `log_message` text character set utf8 collate utf8_bin NOT NULL,
  `log_uri` text character set utf8 collate utf8_bin NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;