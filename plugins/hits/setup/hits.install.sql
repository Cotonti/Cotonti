
CREATE TABLE  IF NOT EXISTS `cot_stats` (
  `stat_name` varchar(32) NOT NULL,
  `stat_value` int UNSIGNED NOT NULL default '0',
  PRIMARY KEY  (`stat_name`)
);

INSERT IGNORE INTO `cot_stats` (`stat_name`, `stat_value`) VALUES
('totalpages', 0),
('totalmailsent', 0),
('totalmailpmnot', 0),
('totalpms', 0),
('totalantihammer', 0),
('textboxerprev', 0),
('version', 999);